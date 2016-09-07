<?php

function realtors_enqueue_scripts(){
    /* enqueue styles */
    wp_enqueue_style( 'realtors-css', REALTORS_ASSETS_URI . '/css/style.css');
    /* enqueue scripts */
    wp_enqueue_script( 'realtors-js', REALTORS_ASSETS_URI . '/js/scripts.min.js', array( 'jquery' ), '1.0.0', FALSE );
}
add_action('wp_enqueue_scripts', 'realtors_enqueue_scripts');