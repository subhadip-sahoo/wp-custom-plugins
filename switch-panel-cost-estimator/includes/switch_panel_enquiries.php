<?php
    function switch_panel_enquiries($per_page){
        global $wpdb;
        $enquiries = new Enquiry_List_Table();
        $enquiries->prepare_items();

        $message = '';
        if ('delete' === $enquiries->current_action()) {
            $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('%d Items deleted.', 'switch_panel_cost_estimator'), count($_REQUEST['id'])) . '</p></div>';
        }
        ?>
    <div class="wrap">
        <div class="icon32" id="icon-users"><br></div>
        <h2><?php _e('List of Enquiries', 'switch_panel_cost_estimator')?> 
    <?php
        if ( ! empty( $_REQUEST['s'] ) ) {
                    echo sprintf( '<span class="subtitle">'
                            . __( 'Search results for &#8220;%s&#8221;', 'switch_panel_cost_estimator' )
                            . '</span>', esc_html( $_REQUEST['s'] ) );
            }
    ?>
        </h2>
        <?php echo $message; ?>

        <form method="get" action="">
            <input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>"/>
            <?php $enquiries->search_box( __( 'Search', 'switch_panel_cost_estimator' ), 'enquiries' ); ?>
            <?php $enquiries->display(); ?>
        </form>
    </div>
<?php   
    }
    
    function switch_panel_view_enquiries(){
        $id = $_REQUEST['id'];
        add_meta_box('switch_panel_cost_estimator_meta_box', 'View Enquiry Details', 'view_enquiries_meta_box_handler', 'view-enquiry', 'normal', 'default');
?>
        <div class="wrap">
            <div class="icon32" id="icon-users"><br></div>
            <h2><?php _e('View Enquiry', 'switch_panel_cost_estimator')?> 
                <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=all-enquiries');?>"><?php _e('Back to list', 'switch_panel_cost_estimator')?></a>
            </h2>
            <form id="form" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
                <input type="hidden" name="id_form_field" value="<?php echo $item['id_form_field'] ?>"/>

                <div class="metabox-holder" id="poststuff">
                    <div id="post-body">
                        <div id="post-body-content">
                             <?php do_meta_boxes('view-enquiry', 'normal', $id); ?>
                        </div>
                    </div>
                </div>
            </form>
        </div>
<?php }
    function view_enquiries_meta_box_handler($id){
        global $wpdb;
?>        
        <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
            <tbody>
                <?php
                    $get_the_details = $wpdb->get_results("SELECT * FROM wp_enquiries WHERE id_enquiry = $id");
                    if(!empty($get_the_details)){
                        foreach ($get_the_details as $get_the_detail) {
                            $field_details = unserialize($get_the_detail->enquiries);
                            foreach ($field_details as $field_detail) {
                                echo '<tr class="form-field" style="margin-bottom:10px;">';
                                echo '<th valign="top" scope="row" style="width:30%;">';
                                echo '<label for="title">';
                                echo $field_detail['title'];
                                echo '</label>';
                                echo '</th>';
                                echo '<td style="width:30%;">';
                                echo '<label for="value">';
                                echo $field_detail['value'];
                                echo '</label>';
                                echo '</td>';
                                echo '</tr>';
                            }
                        }
                    }
                ?>
            </tbody>
        </table>
<?php } ?>