<?php

function switch_panel_cost_estimator_admin_menu(){
    add_menu_page(__('FieldSets', 'switch_panel_cost_estimator'), __('Cost Estimator', 'switch_panel_cost_estimator'), 'activate_plugins', 'welcome-page', 'plugins_default_page', plugins_url().'/switch-panel-cost-estimator/images/calculator.png');
    add_submenu_page('welcome-page', __('Settings', 'switch_panel_cost_estimator'), __('Switch Panel Settings', 'switch_panel_cost_estimator'), 'activate_plugins', 'plugin-custom-settings', 'plugin_custom_settings');
    $hook4 = add_submenu_page('welcome-page', __('Enquiries', 'switch_panel_cost_estimator'), __('Enquiries', 'switch_panel_cost_estimator'), 'activate_plugins', 'all-enquiries', 'switch_panel_enquiries');
    add_submenu_page('hidden-page', __('View Enquiry', 'switch_panel_cost_estimator'), '', 'activate_plugins', 'view-enquiry', 'switch_panel_view_enquiries');
    $hook1 = add_submenu_page('welcome-page', __('Fields', 'switch_panel_cost_estimator'), __('All Fields', 'switch_panel_cost_estimator'), 'activate_plugins', 'form-fields', 'form_fields_listing_main');
    add_submenu_page('welcome-page', __('Add New Field', 'switch_panel_cost_estimator'), __('Add New Field', 'switch_panel_cost_estimator'), 'activate_plugins', 'edit-field', 'form_field_edit_main');
    $hook2 = add_submenu_page('welcome-page',__('Field Options', 'switch_panel_cost_estimator'), __('All Field Options', 'switch_panel_cost_estimator'), 'activate_plugins', 'form-field-options', 'form_fields_options_listing_main');
    add_submenu_page('welcome-page', __('Add Option', 'switch_panel_cost_estimator'), __('Add Field Option', 'switch_panel_cost_estimator'), 'activate_plugins', 'edit-field-options', 'form_field_options_edit_main');
    $hook3 = add_submenu_page('welcome-page',__('Relational Fields', 'switch_panel_cost_estimator'), __('All Relational Fields', 'switch_panel_cost_estimator'), 'activate_plugins', 'relational-field-options', 'relational_fields_listing_main');
    add_submenu_page('welcome-page', __('Create New Relation', 'switch_panel_cost_estimator'), __('Create New Relation', 'switch_panel_cost_estimator'), 'activate_plugins', 'edit-relational-fields', 'relational_fields_edit_main');
    add_action('load-'.$hook4, 'enquiries_per_page_add_option');
    add_action('load-'.$hook1, 'fields_per_page_add_option');
    add_action('load-'.$hook2, 'field_options_per_page_add_option');
    add_action('load-'.$hook3, 'relational_fields_per_page_add_option');
}

function fields_per_page_add_option() {
    $option = 'per_page';
    $args = array(
        'label' => 'Fields',
        'default' => 10,
        'option' => 'fields_per_page'
    );

    $screen = get_current_screen();
    add_filter( 'manage_'.$screen->id.'_columns', array( 'Form_Fields_List_Table', 'get_columns' ));
    add_screen_option( $option, $args );
}
function field_options_per_page_add_option() {
    $option = 'per_page';
    $args = array(
        'label' => 'Field Options',
        'default' => 10,
        'option' => 'field_options_per_page'
    );

    $screen = get_current_screen();
    add_filter( 'manage_'.$screen->id.'_columns', array( 'Form_FieldOptions_List_Table', 'get_columns' ));
    add_screen_option( $option, $args );
}
function relational_fields_per_page_add_option() {
    $option = 'per_page';
    $args = array(
        'label' => 'Realtioanl Fields',
        'default' => 10,
        'option' => 'relational_fields_per_page'
    );

    $screen = get_current_screen();
    add_filter( 'manage_'.$screen->id.'_columns', array( 'Relational_Fields_List_Table', 'get_columns' ));
    add_screen_option( $option, $args );
}
function enquiries_per_page_add_option() {
    $option = 'per_page';
    $args = array(
        'label' => 'Enquiries',
        'default' => 10,
        'option' => 'enquiries_per_page'
    );

    $screen = get_current_screen();
    add_filter( 'manage_'.$screen->id.'_columns', array( 'Enquiry_List_Table', 'get_columns' ));
    add_screen_option( $option, $args );
}
function fields_per_page_set_option($status, $option, $value) {
    if ( 'fields_per_page' == $option ) return $value;
    return $status;
}
function field_options_per_page_set_option($status, $option, $value) {
    if ( 'field_options_per_page' == $option ) return $value;
    return $status;
}
function relational_fields_per_page_set_option($status, $option, $value) {
    if ( 'relational_fields_per_page' == $option ) return $value;
    return $status;
}
function enquiries_per_page_set_option($status, $option, $value) {
    if ( 'enquiries_per_page' == $option ) return $value;
    return $status;
}
add_action('admin_menu', 'switch_panel_cost_estimator_admin_menu');
add_filter('set-screen-option', 'field_options_per_page_set_option', 10, 3);
add_filter('set-screen-option', 'fields_per_page_set_option', 10, 3);
add_filter('set-screen-option', 'relational_fields_per_page_set_option', 10, 3);
add_filter('set-screen-option', 'enquiries_per_page_set_option', 10, 3);
?>