<?php

function realtors_search_form_shortcode($atts){
    $args = handle_shortcode_params($atts);
    ob_start();
    handle_form_view($args);
    return ob_get_clean();
}
add_shortcode('realtors-search', 'realtors_search_form_shortcode');


function realtors_list_shortcode($atts){
    ob_start();
    handle_req_res();
    return ob_get_clean();
}
add_shortcode('realtors-list', 'realtors_list_shortcode');

function realtors_shortcode($atts){
    $args = handle_shortcode_params($atts);
    ob_start();    
    handle_form_view($args);
    handle_req_res();
    return ob_get_clean();
}
add_shortcode('realtors', 'realtors_shortcode');