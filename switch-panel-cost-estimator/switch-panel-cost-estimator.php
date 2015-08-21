<?php
/* 
 * Plugin Name: Switch Panel Cost Estimator
 * Plugin URI: http://qss.in/
 * Description: Create a form that will calculate cost of switch panel.
 * Version: 1.1
 * Author: Quintessential Software Solutions Private Limited (QSS)
 * Author URI: http://qss.in/
 * Licence: GPL2
*/
    define('SWITCH_PANEL_ATTACHMENT_DIR', dirname(__FILE__) . '/template/attachments');
    define('SWITCH_PANEL_ROOT_DIR', dirname(__FILE__));
    global $switch_panel_cost_estimator_db_version, 
            $wpdb, 
            $table_name, 
            $table_name_options, 
            $tbl_wp_relational_fields, 
            $tbl_wp_relational_field_details,
            $tbl_enquiries;
    
    function frontend_enqueue_scripts(){
        wp_enqueue_style( 'app-css', plugins_url() . '/switch-panel-cost-estimator/template/css/app.css');
        wp_enqueue_style( 'popup_css', plugins_url() . '/switch-panel-cost-estimator/template/css/custom_css.css');
        wp_enqueue_style( 'tooltip_css', plugins_url() . '/switch-panel-cost-estimator/template/css/tooltipster.css');
        wp_enqueue_script( 'tooltip_scripts', plugins_url() . '/switch-panel-cost-estimator/template/js/jquery.tooltipster.js', array( 'jquery' ), '1', FALSE );
        wp_enqueue_script( 'form_scripts', plugins_url() . '/switch-panel-cost-estimator/template/js/form_scripts.js', array( 'jquery' ), '1', FALSE );
        wp_localize_script( 'form_scripts', 'switch_panel', array('ajaxurl' => admin_url( 'admin-ajax.php' )));
    }
    add_action('wp_enqueue_scripts', 'frontend_enqueue_scripts');
    
    function cost_estimate_custom_js(){
        add_editor_style( plugins_url() . '/switch-panel-cost-estimator/css/plugins_admin.css');
        wp_enqueue_script( 'cost_estimate_custom_js', plugins_url( '/js/cost_estimator_custom.js', __FILE__ ), array('jquery') );
    }
    add_action('admin_enqueue_scripts', 'cost_estimate_custom_js');
    
    require_once dirname(__FILE__).'/settings.php';
    require_once dirname(__FILE__).'/default/plugins_default_page.php';
    
    require_once dirname(__FILE__).'/includes/switch_panel_enquiries.php';
    require_once dirname(__FILE__).'/includes/form-fields.php';
    require_once dirname(__FILE__).'/includes/form-field-options.php';
    require_once dirname(__FILE__).'/includes/relational-fields.php';
    
    require_once dirname(__FILE__).'/ajax/ajax.php';
     
    require_once dirname(__FILE__).'/install_tables.php';
    
    require_once dirname(__FILE__).'/class/euquiry-wp-list-table.php';
    require_once dirname(__FILE__).'/class/field-wp-list-table.php';
    require_once dirname(__FILE__).'/class/field-options-wp-list-table.php';
    require_once dirname(__FILE__).'/class/related-fields-wp-list-table.php';
    
    require_once dirname(__FILE__).'/class/enquiry_list_table.php';
    require_once dirname(__FILE__).'/class/form_fields_list_table.php';
    require_once dirname(__FILE__).'/class/form_field_options_list_table.php';
    require_once dirname(__FILE__).'/class/relational_fields_list_table.php';
    
    require_once dirname(__FILE__).'/admin_menu.php';

    function show_switch_panel_cost_estimators_shortcode(){
        ob_start();
        extract( shortcode_atts( array(), $atts ) );
        require_once dirname(__FILE__).'/template/switch-panel-cost-estimator-form.php';
        return ob_get_clean();
    }
    add_shortcode('switch-panel-cost-estimator', 'show_switch_panel_cost_estimators_shortcode');
?>