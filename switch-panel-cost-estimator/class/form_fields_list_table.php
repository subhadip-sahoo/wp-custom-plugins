<?php
class Form_Fields_List_Table extends Field_WP_List_Table {
    function __construct(){
        global $status, $page, $table_name;

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
            'edit' => sprintf('<a href="?page=edit-field&id=%s">%s</a>', $item['id_form_field'], __('Edit', 'switch_panel_cost_estimator')),
            'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['id_form_field'], __('Delete', 'switch_panel_cost_estimator')),
        );

        return sprintf('%s %s', $item['title'], $this->row_actions($actions));
    }

    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['id_form_field']
        );
    }

    function get_columns(){
        $columns = array(
            'cb' => '<input type="checkbox" />', 
            'title' => __('Field Title', 'switch_panel_cost_estimator'),
            'field_type' => __('Field Type', 'switch_panel_cost_estimator'),
            'popup_image' => __('Popup Image', 'switch_panel_cost_estimator'),
            'popup_description' => __('Description', 'switch_panel_cost_estimator'),
            'sequence' => __('Sequence', 'switch_panel_cost_estimator')
        );
        return $columns;
    }

    function get_sortable_columns(){
        $sortable_columns = array(
            'title' => array('title', FALSE),
            'field_type' => array('field_type', FALSE),
            'sequence' => array('sequence', TRUE)
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
        global $wpdb, $table_name, $table_name_options, $tbl_wp_relational_fields;
        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $tbl_wp_relational_fields WHERE id_field_related_to IN($ids)");
                $wpdb->query("DELETE FROM $table_name_options WHERE id_form_field IN($ids)");
                $wpdb->query("DELETE FROM $table_name WHERE id_form_field IN($ids)");
            }
        }
    }
    function prepare_items(){
        global $wpdb, $table_name;
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
                'orderby' => 'sequence',
                'order' => 'ASC',
                'offset' => ( $this->get_pagenum() - 1 ) * $per_page );
        $where = '';
        if (isset($_REQUEST['s']) && ! empty( $_REQUEST['s'] ) ){
                $args['s'] = $_REQUEST['s'];
                $where = " WHERE title LIKE '%%".$args['s']."%%' OR field_type LIKE '%%".$args['s']."%%' OR sequence LIKE '%%".$args['s']."%%'";
        }
        if ( ! empty( $_REQUEST['orderby'] ) ) {
                if ( 'title' == $_REQUEST['orderby'] ){
                        $args['orderby'] = 'title';
                }
                else if ( 'field_type' == $_REQUEST['orderby'] ){
                        $args['orderby'] = 'field_type';
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
        $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name $where ORDER BY ".$args['orderby']." ".$args['order']." LIMIT %d OFFSET %d", $per_page, $args['offset']), ARRAY_A);
        $total_items = $wpdb->get_var("SELECT COUNT(id_form_field) FROM $table_name $where");
        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'total_pages' => ceil($total_items / $per_page),
            'per_page' => $per_page
        ));
    }
}
?>