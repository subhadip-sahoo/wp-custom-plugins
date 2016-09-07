<?php
class Enquiry_List_Table extends Enquiry_WP_List_Table {
    function __construct(){
        global $status, $page, $tbl_enquiries;

        parent::__construct(array(
            'singular' => '',
            'plural' => '',
            'ajax' => false
        ));
    }
    function column_default($item, $column_name){
        return $item[$column_name];
    }

    function column_name($item){
        $actions = array(
            'edit' => sprintf('<a href="?page=view-enquiry&id=%s">%s</a>', $item['id_enquiry'], __('View', 'switch_panel_cost_estimator')),
            'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['id_enquiry'], __('Delete', 'switch_panel_cost_estimator')),
        );

        return sprintf('%s %s', $item['name'], $this->row_actions($actions));
    }

    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['id_enquiry']
        );
    }

    function get_columns(){
        $columns = array(
            'cb' => '<input type="checkbox" />', 
            'name' => __('Name', 'switch_panel_cost_estimator'),
            'email' => __('Email Address', 'switch_panel_cost_estimator'),
            'estimated_cost' => __('Estimated Cost', 'switch_panel_cost_estimator'),
            'enquiry_date' => __('Enquiry Date', 'switch_panel_cost_estimator')
        );
        return $columns;
    }

    function get_sortable_columns(){
        $sortable_columns = array(
            'name' => array('name', FALSE),
            'email' => array('email', FALSE),
            'estimated_cost' => array('estimated_cost', FALSE),
            'enquiry_date' => array('enquiry_date', TRUE)
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
        global $wpdb, $tbl_enquiries;
        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $tbl_enquiries WHERE id_enquiry IN($ids)");
            }
        }
    }
    function prepare_items(){
        global $wpdb, $tbl_enquiries;
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
                'orderby' => 'enquiry_date',
                'order' => 'DESC',
                'offset' => ( $this->get_pagenum() - 1 ) * $per_page );
        $where = '';
        if (isset($_REQUEST['s']) && ! empty( $_REQUEST['s'] ) ){
                $args['s'] = $_REQUEST['s'];
                $where = " WHERE name LIKE '%%".$args['s']."%%' OR email LIKE '%%".$args['s']."%%' OR estimated_cost LIKE '%%".$args['s']."%%' OR enquiry_date LIKE '%%".$args['s']."%%'";
        }
        if ( ! empty( $_REQUEST['orderby'] ) ) {
                if ( 'name' == $_REQUEST['orderby'] ){
                        $args['orderby'] = 'name';
                }
                else if ( 'email' == $_REQUEST['orderby'] ){
                        $args['orderby'] = 'email';
                }
                else if ( 'estimated_cost' == $_REQUEST['orderby'] ){
                        $args['orderby'] = 'estimated_cost';
                }
                else if ( 'enquiry_date' == $_REQUEST['orderby'] ){
                        $args['orderby'] = 'enquiry_date';
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
        $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $tbl_enquiries $where ORDER BY ".$args['orderby']." ".$args['order']." LIMIT %d OFFSET %d", $per_page, $args['offset']), ARRAY_A);
        $total_items = $wpdb->get_var("SELECT COUNT(id_enquiry) FROM $tbl_enquiries $where");
        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'total_pages' => ceil($total_items / $per_page),
            'per_page' => $per_page
        ));
    }
}
?>