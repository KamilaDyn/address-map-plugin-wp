<?php

function address_map_order_cpt()
{
    $labels = array(
        'name' => __('Adressy', 'address-map'),
        'singular_name' => __('Adress', 'address-map'),
        'menu_name' => __('Mapa adresów', 'address-map'),
        'name_admin_bar' => __('Mapa adresów', 'address-map'),
        'add_new' => __('Dodaj', 'adres-map'),
        'add_new_item' => __('Add nowy adres', 'address-map'),
        'new_item' => __('Nowy adres', 'address-map'),
        'edit_item' => __('Edytuj adres', 'address-map'),
        'view_item' => __('Zobacz adres', 'address-map'),
        'all_items' => __('Wszystkie', 'address-map'),
        'search_items' => __('Szukaj Adresu', 'address-map'),
        'not_found' => __('Nie znaleziono.', 'address-map'),
        'not_found_in_trash' => __('Nie znaleziono w koszu.', 'address-map'),
    );


    // access to administrator screen options
    $capability = 'manage_options';
    $capabilities = array(
        'edit_post' => $capability,
        'read_post' => $capability,
        'delete_post' => $capability,
        'create_posts' => $capability,
        'edit_posts' => $capability,
        'edit_others_posts' => $capability,
        'publish_posts' => $capability,
        'read_private_posts' => $capability,
        'read' => $capability,
        'delete_posts' => $capability,
        'delete_private_posts' => $capability,
        'delete_published_posts' => $capability,
        'delete_others_posts' => $capability,
        'edit_private_posts' => $capability,
        'edit_published_posts' => $capability
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_nav_menus' => false,
        'show_in_menu' => current_user_can('manage_options') ? true : false,
        'show_in_rest' => true,
        'query_var' => false,
        'rewrite' => false,
        'capabilities' => $capabilities,
        'has_archive' => false,
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('editor', 'title', 'thumbnail'),
        'menu_icon' => 'dashicons-location-alt',
    );

    register_post_type('address_map', $args);
}


/// create own table with columns

function address_map_column($columns)
{
    unset($columns['title']);
    unset($columns['date']);
    $edit_columns = array(
        'cb' => '&lt;input type="checkbox" />',
        'title' => __('Nazwa placówki', 'address-map'),
        'street' =>  __('Ulica', 'address-map'),
        'number' => __('Nr', 'address-map'),
        'postcode' => __('Kod pocztowy', 'address-map'),
        'city' => __('Miejscowość', 'address-map'),
        'country' => __('Kraj', 'address-map'),
        'date' => __('Data', 'address-map'),
    );

    return array_merge($columns, $edit_columns);
}


// meta boxes

function address_map_order_meta_box($post)
{
    $street = get_post_meta($post->ID, '_street', true);
    $postcode = get_post_meta($post->ID, '_postcode', true);
    $city = get_post_meta($post->ID, '_city', true);
    $country  =  get_post_meta($post->ID, '_country', true);
    $number  =  get_post_meta($post->ID, '_number', true);
    // make sure the form request comes from WordPress
    wp_nonce_field(basename(__FILE__), 'meta_box_nonce');
    // wp_nonce_field('address_map_meta_box', 'address_map_meta_box_nonce');
?>
    <table class="form-table">
        <tbody>
            <tr valign="top">
                <th scope="row"><label for="street"><?php _e('Ulica', 'address-map'); ?></label></th>
                <td><input name="_street" type="text" id="_street" value="<?php echo $street; ?>" class="regular-text">
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="number"><?php _e('Number domu', 'address-map'); ?></label></th>
                <td><input name="_number" type="text" id="_number" value="<?php echo $number; ?>" class="regular-text">
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="postcode"><?php _e('Kod pocztowy', 'address-map'); ?></label></th>
                <td><input name="_postcode" type="text" id="_postcode" value="<?php echo $postcode; ?>" class="regular-text">
                </td>
            </tr>

            <tr valign="top">
                <th scope="row"><label for="city"><?php _e('Nazwa miasta', 'address-map'); ?></label></th>
                <td><input name="_city" type="text" id="_city" value="<?php echo $city ?>" class="regular-text">
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="country"><?php _e('Kraj', 'vat-counter'); ?></label></th>
                <td><input name="_country" type="text" id="_country" value="<?php echo $country ?>" class="regular-text">
                </td>
            </tr>
        </tbody>

    </table>

<?php
}


function address_map_calculation_columns($column, $post_id)
{
    switch ($column) {
        case 'title':
            echo $post_id;
            break;
        case 'street':
            echo get_post_meta($post_id, '_street', true);
            break;
        case 'number':
            echo get_post_meta($post_id, '_number', true);
            break;
        case 'postcode':
            echo get_post_meta($post_id, '_postcode', true);
            break;
        case 'city':
            echo get_post_meta($post_id, '_city', true);
            break;
        case 'country':
            echo get_post_meta($post_id, '_country', true);
            break;
    }
}

function address_map_meta_boxes($post_id, $post, $update)
{
    /*
     * We need to verify this came from our screen and with proper authorization,
     * because the save_post action can be triggered at other times.
     */


    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }


    // Verify that the nonce is valid.
    if (!isset($_POST['meta_box_nonce']) || !wp_verify_nonce($_POST['meta_box_nonce'], basename(__FILE__))) {
        return;
    }

    if (!current_user_can('edit_post', $post_id))
        return;



    $field_lists = [
        '_street',
        '_number',
        '_postcode',
        '_city',
        '_country',


    ];
    // if (isset($_POST['street'])) {
    //     update_post_meta($post_id, 'street',  sanitize_text_field($_POST['street']));
    // }
    // update_post_meta($post_id, 'city',  sanitize_text_field($_POST['city']));


    foreach ($field_lists as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }
}

add_action('save_post', 'address_map_meta_boxes', 10, 3);
