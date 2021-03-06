<?php

/* file address-router
@packgage address-map

*/
add_action('rest_api_init', 'address_on_map');

function address_on_map()
{
    register_rest_route('address-map/v1', 'search', array(
        'methods' => WP_REST_SERVER::READABLE,
        'callback' => 'all_addresses_on_map',
    ));
}
function all_addresses_on_map()
{
    $mainQuery = new WP_Query(array(
        'post_type' => array('address_map'),
        's' => sanitize_text_field($data['term'])
    ));

    $results = array(
        'addresses' => array(),

    );

    while ($mainQuery->have_posts()) {
        $mainQuery->the_post();
        // Create a stream
        $city = get_post_meta(get_the_ID(), '_city', true);
        $postcode =  get_post_meta(get_the_ID(), '_postcode', true);
        $street = get_post_meta(get_the_ID(), '_street', true);

        $api_url = "https://nominatim.openstreetmap.org/search?format=json&limit=1&city=$city& postalcode=$postcode&street=$street";

        $opts = array(
            'http' => array(
                'header' => array("Referer: $api_url\r\n")
            )
        );
        $context = stream_context_create($opts);



        $context = stream_context_create($opts);
        $json_data = file_get_contents($api_url, false, $context);

        $response_data = json_decode($json_data, true);

        // wyciągnięcie danych lat i long do wysłania dla tabeli adres
        $lat = $response_data[0]['lat'];
        $lon = $response_data[0]['lon'];
        // $data = new Date();
        if (get_post_type() == 'address_map') {
            array_push($results['addresses'], array(
                'id' => get_the_ID(),
                'name' => get_the_title(),
                'description' => wp_trim_words(get_the_content(), 20),
                'address' => array(
                    'street' => get_post_meta(get_the_ID(), '_street', true),
                    'number' => get_post_meta(get_the_ID(), '_number', true),
                    'postcode' => get_post_meta(get_the_ID(), '_postcode', true),
                    'city' => get_post_meta(get_the_ID(), '_city', true),
                    'country' => get_post_meta(get_the_ID(), '_country', true),
                ),
                'image' => get_the_post_thumbnail(),
                'geolocation' => array(
                    'lat' => $lat,
                    'lon' => $lon
                ),

                'url' => get_the_permalink(),

            ));
        }
    }
    wp_reset_postdata();

    return $results;
}
