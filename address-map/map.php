<?php
/* file folr address in map
@packgage address-map

*/

function display_map()
{
    $content = '';
    $content .= '<div id="primary" class="content-area">
    <div class="map-container">
    <div id="mapid" style="width: 100%; height: 500px; margin-top: 50px;"> </div>
</div></div>';

    return $content;
}
