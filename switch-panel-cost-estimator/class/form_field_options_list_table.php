<?php
class Form_FieldOptions_List_Table extends Field_Options_WP_List_Table {
    function __construct(){
        global $status, $page, $table_name_options;

        parent::__construct(array(
            'singular' => '',
            'plural' => '',
            'ajax' => false
        ));
    }
    function column_default($item, $column_name){
        return $item[$column_name];
    }

    function column_title($item){
        $actions = array(
            'edit' => sprintf('<a href="?page=edit-field-options&id=%s">%s</a>', $item['id_form_field_detail'], __('Edit', 'switch_panel_cost_estimator')),
            'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['id_form_field_detail'], __('Delete', 'switch_panel_cost_estimator')),
        );

        return sprintf('%s %s', $item['title'], $this->row_actions($actions));
    }

    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['id_form_field_detail']
        );
    }

    function get_columns(){
        $columns = array(
            'cb' => '<input type="checkbox" />', 
            'title' => __('Field Title', 'switch_panel_cost_estimator'),
            'print_option' => __('Option Name', 'switch_panel_cost_estimator'),
            'print_option_value' => __('Option Value', 'switch_panel_cost_estimator'),
            'price' => __('Price ( '.get_option('switch_panel_price_unit').' )', 'switch_panel_cost_estimator'),
            'sequence' => __('Sequence', 'switch_panel_cost_estimator')
        );
        return $columns;
    }

    function get_sortable_columns(){
        $sortable_columns = array(
            'title' => array('title', TRUE),
            'print_option' => array('option_name', FALSE),
            'price' => array('price', FALSE),
            'sequence' => array('sequence', FALSE)
        );
        return $sortable_columns;
    }

    function get_bulk_actions(){
        $actions = array(
            'delete' => 'Delete'
        );
        return $actions;
    }

    function process_bulk_action(){
        global $wpdb, $table_name_options, $tbl_wp_relational_fields, $tbl_wp_relational_field_details;
        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $tbl_wp_relational_field_details WHERE id_field_related_to_option IN($ids)");
                $wpdb->query("DELETE FROM $tbl_wp_relational_fields WHERE id_dependent_field_option IN($ids)");
                $wpdb->query("DELETE FROM $table_name_options WHERE id_form_field_detail IN($ids)");
            }
        }
    }
    function prepare_items(){
        global $wpdb, $table_name, $table_name_options;
        $user = get_current_user_id();
        $screen = get_current_screen();
        $option = $screen->get_option('per_page', 'option');
        $per_page = get_user_meta($user, $option, true);

        if ( empty ( $per_page) || $per_page < 1 ) {
            $per_page = $screen->get_option( 'per_page', 'default');
        }
        $this->_column_headers = $this->get_column_info();
        $this->process_bulk_action();
        $args = array(
                'posts_per_page' => $per_page,
                'orderby' => 'title',
                'order' => 'ASC',
                'offset' => ( $this->get_pagenum() - 1 ) * $per_page 
            );
        $where = '';
        if (isset($_REQUEST['s']) && ! empty( $_REQUEST['s'] ) ){
                $args['s'] = $_REQUEST['s'];
                $where = " WHERE wpff.title LIKE '%%".$args['s']."%%' OR wpffd.option_name LIKE '%%".$args['s']."%%' OR wpffd.option_value LIKE '%%".$args['s']."%%' OR wpffd.price LIKE '%%".$args['s']."%%'";
        }
        if ( ! empty( $_REQUEST['orderby'] ) ) {
                if ( 'title' == $_REQUEST['orderby'] ){
                        $args['orderby'] = 'title';
                }
                else if ( 'option_name' == $_REQUEST['orderby'] ){
                        $args['orderby'] = 'option_name';
                }
                else if ( 'price' == $_REQUEST['orderby'] ){
                        $args['orderby'] = 'price';
                }
                else if ( 'sequence' == $_REQUEST['orderby'] ){
                        $args['orderby'] = 'sequence';
                }
        }
        
        if ( ! empty( $_REQUEST['order'] ) ) {
                if ( 'asc' == strtolower( $_REQUEST['order'] ) ){
                        $args['order'] = 'ASC';
                }
                elseif ( 'desc' == strtolower( $_REQUEST['order'] ) ){
                        $args['order'] = 'DESC';
                }
        }
        $query = "  SELECT 
                        wpff.title, 
                        wpffd.id_form_field_detail,
                        wpffd.price,
                        wpffd.sequence,
                    CASE wpff.option_type
                        WHEN 'quantity' THEN CONCAT(wpffd.option_name,' through ',wpffd.option_value)
                        WHEN 'group_value' THEN wpffd.option_name
                    END AS print_option,
                    CASE wpff.option_type
                        WHEN 'quantity' THEN CONCAT(wpffd.option_name,' through ',wpffd.option_value)
                        WHEN 'group_value' THEN wpffd.option_value
                    END AS print_option_value
                    FROM 
                        $table_name AS wpff 
                    INNER JOIN 
                        $table_name_options AS wpffd 
                    ON wpff.id_form_field = wpffd.id_form_field 
                    $where ORDER BY ".$args['orderby']." ".$args['order']." LIMIT %d OFFSET %d";
        $this->items = $wpdb->get_results($wpdb->prepare($query, $per_page, $args['offset']), ARRAY_A);
        $total_items = $wpdb->get_var("SELECT COUNT(id_form_field_detail) FROM $table_name_options $where");
        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'total_pages' => ceil($total_items / $per_page),
            'per_page' => $per_page
        ));
    }
}
?>