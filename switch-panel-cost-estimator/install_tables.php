<?php
$switch_panel_cost_estimator_db_version = '1.1';

$table_name = $wpdb->prefix . 'form_fields';
$table_name_options = $wpdb->prefix . 'form_field_details';
$tbl_wp_relational_fields = $wpdb->prefix . 'relational_fields';
$tbl_wp_relational_field_details = $wpdb->prefix . 'relational_field_details';
$tbl_enquiries = $wpdb->prefix . 'enquiries';

function switch_panel_cost_estimator_db_install(){
   global $wpdb, 
           $table_name, 
           $table_name_options, 
           $tbl_wp_relational_fields, 
           $tbl_wp_relational_field_details,
           $tbl_enquiries,
           $switch_panel_cost_estimator_db_version;
   $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
            `id_form_field` int(11) NOT NULL AUTO_INCREMENT,
            `title` varchar(255) NOT NULL,
            `field_type` varchar(255) NOT NULL,
            `field_name` varchar(255) NOT NULL,
            `option_type` varchar(255) NOT NULL,
            `popup_image` varchar(255) NOT NULL,
            `popup_description` text NOT NULL,
            `is_dependent` char(1) NOT NULL,
            `sequence` int(11) NOT NULL,
            PRIMARY KEY (`id_form_field`)
          ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";

   $sql1 = "CREATE TABLE IF NOT EXISTS `$table_name_options` (
            `id_form_field_detail` int(11) NOT NULL AUTO_INCREMENT,
            `id_form_field` int(11) NOT NULL,
            `option_name` varchar(255) NOT NULL,
            `option_value` varchar(255) NOT NULL,
            `price` decimal(7,2) NOT NULL,
            `sequence` int(11) NOT NULL,
            PRIMARY KEY (`id_form_field_detail`)
          ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";

   $sql2 = "CREATE TABLE IF NOT EXISTS `$tbl_wp_relational_fields` (
            `id_relational_field` int(11) NOT NULL AUTO_INCREMENT,
            `id_dependent_field_option` int(11) NOT NULL,
            `id_field_related_to` int(11) NOT NULL,
            PRIMARY KEY (`id_relational_field`)
          ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";

   $sql3 = "CREATE TABLE IF NOT EXISTS `$tbl_wp_relational_field_details` (
            `id_relational_field_detail` int(11) NOT NULL AUTO_INCREMENT,
            `id_relational_field` int(11) NOT NULL,
            `id_field_related_to_option` int(11) NOT NULL,
            `price` decimal(7,2) NOT NULL,
            PRIMARY KEY (`id_relational_field_detail`)
          ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
   
   $sql4 = "CREATE TABLE IF NOT EXISTS `$tbl_enquiries` (
            `id_enquiry` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `email` varchar(255) NOT NULL,
            `estimated_cost` decimal(7,2) NOT NULL,
            `enquiry_date` datetime NOT NULL,
            `enquiries` text NOT NULL,
            PRIMARY KEY (`id_enquiry`)
          ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";

   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   dbDelta($sql);
   dbDelta($sql1);
   dbDelta($sql2);
   dbDelta($sql3);
   add_option('switch_panel_cost_estimator_db_version', $switch_panel_cost_estimator_db_version);
}
register_activation_hook(__FILE__, 'switch_panel_cost_estimator_db_install');

function switch_panel_cost_estimator_update_db_check(){
   global $switch_panel_cost_estimator_db_version;
   if (get_site_option('switch_panel_cost_estimator_db_version') != $switch_panel_cost_estimator_db_version) {
       switch_panel_cost_estimator_db_install();
   }
}
add_action('plugins_loaded', 'switch_panel_cost_estimator_update_db_check');
?>
