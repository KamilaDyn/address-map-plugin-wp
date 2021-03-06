<?php
/* file folr address in map
@packgage address-map

*/

function display_map()
{
    $content = '';
    $content .= '<div id="primary" class="content-area"><h1>Mapa z zleceniami</h1>
    <div class="map-container">
    <div id="mapid" style="width: 100%; height: 500px; margin-top: 50px;"> </div>
</div>
</div>';

    return $content;
}

global $wpdb;
$sql_name = " SELECT * FROM wp_posts INNER JOIN wp_postmeta on wp_posts.ID = wp_postmeta.post_id WHERE wp_posts.post_type = 'address_map' ORDER BY wp_posts.ID;";


$result = $wpdb->get_results($sql_name);
