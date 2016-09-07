<?php

function handle_form_view($args){
    extract($args);
    $relators = new Realtors($_REQUEST);
    $req = $relators->get_request();
    require_once REALTORS_TEAMPLATE_DIR . '/search-form.php';
}

function handle_req_res(){
    if(empty($_REQUEST))
        return;
    $relators = new Realtors($_REQUEST);
    $relators->call();
}

function handle_shortcode_params($atts){
    $_defaults = [
        'action' => ''
    ];
    $args = shortcode_atts( $_defaults, $atts );    
    return $args;
}