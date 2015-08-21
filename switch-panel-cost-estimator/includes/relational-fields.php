<?php
    function relational_fields_listing_main($per_page){
        global $wpdb;
        $table = new Relational_Fields_List_Table();
        $table->prepare_items();

        $message = '';
        if ('delete' === $table->current_action()) {
            $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('%d Items deleted.', 'switch_panel_cost_estimator'), count($_REQUEST['id'])) . '</p></div>';
        }
        ?>
    <div class="wrap">
        <div class="icon32" id="icon-users"><br></div>
        <h2><?php _e('List of Related Fields', 'switch_panel_cost_estimator')?> 
            <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=edit-relational-fields');?>"><?php _e('Create New Relation', 'switch_panel_cost_estimator')?></a>
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
            <?php //$table->search_box( __( 'Search', 'switch_panel_cost_estimator' ), 'relational-fields' ); ?>
            <?php $table->display(); ?>
        </form>
    </div>
     <?php   
    }
    
    function relational_fields_edit_main(){
        global $wpdb, $tbl_wp_relational_fields, $tbl_wp_relational_field_details;
        $message = '';
        $notice = '';
        $default = array(
            'id_relational_field' => 0,
            'id_dependent_field' => '',
            'id_dependent_field_option' => '',
            'id_field_related_to' => ''
        );

        if (wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
            $item = shortcode_atts($default, $_REQUEST);
            $item_valid = relational_fields_validate($item);
//            echo '<pre>';
//            print_r($_POST);
//            echo '</pre>';
            if ($item_valid === true) {
                if ($item['id_relational_field'] == 0) {
                    unset($item['id_dependent_field']);
                    $result = $wpdb->insert($tbl_wp_relational_fields, $item);
                    $item['id_relational_field'] = $wpdb->insert_id;
                    if ($result) {
                        $message = __('Item was successfully saved', 'switch_panel_cost_estimator');
                    } else {
                        $notice = __('There was an error while saving item', 'switch_panel_cost_estimator');
                    }
                } else {
                    unset($item['id_dependent_field']);
                    $result = $wpdb->update($tbl_wp_relational_fields, $item, array('id_relational_field' => $item['id_relational_field']));
                    $message = __('Item was successfully updated', 'switch_panel_cost_estimator');
                }
            } else {
                $notice = $item_valid;
            }
        }
        else {
            $item = $default;
            if (isset($_REQUEST['id'])) {
                $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $tbl_wp_relational_fields WHERE id_relational_field = %d", $_REQUEST['id']), ARRAY_A);
                if (!$item) {
                    $item = $default;
                    $notice = __('Item not found', 'switch_panel_cost_estimator');
                }
            }
        }
        if(isset($_POST['submit'])){
            if($item['id_relational_field'] != 0){
                $wpdb->delete($tbl_wp_relational_field_details, array('id_relational_field' => $item['id_relational_field']));
                $count_related_options = count($_POST['related_options']);
                for($count = 0; $count < $count_related_options; $count++){
                    $insert_arr = array(
                        'id_relational_field' => $item['id_relational_field'],
                        'id_field_related_to_option' => $_POST['related_options'][$count],
                        'price' => $_POST['related_options_price'][$count]
                    );
                    $wpdb->insert($tbl_wp_relational_field_details, $insert_arr);
                }
            }
        }
        add_meta_box('switch_panel_cost_estimator_meta_box', 'Create New Relation', 'relational_fields_meta_box_handler', 'relational-fields', 'normal', 'default');
        ?>
        <div class="wrap">
        <div class="icon32" id="icon-users"><br></div>
        <h2><?php _e('Create New Relation', 'switch_panel_cost_estimator')?> 
            <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=relational-field-options');?>"><?php _e('Back to list', 'switch_panel_cost_estimator')?></a>
        </h2>

        <?php if (!empty($notice)): ?>
        <div id="notice" class="error"><p><?php echo $notice ?></p></div>
        <?php endif;?>
        <?php if (!empty($message)): ?>
        <div id="message" class="updated"><p><?php echo $message ?></p></div>
        <?php endif;?>

        <form id="form" method="POST">
            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
            <input type="hidden" name="id_relational_field" id="id_relational_field" value="<?php echo $item['id_relational_field'] ?>"/>
            
            <div class="metabox-holder" id="poststuff">
                <div id="post-body">
                    <div id="post-body-content">
                        <?php do_meta_boxes('relational-fields', 'normal', $item); ?>
                        <input type="submit" value="<?php _e('Save', 'switch_panel_cost_estimator')?>" id="submit" class="button-primary" name="submit">
                    </div>
                </div>
            </div>
        </form>
        </div>
    <?php
    }

    function relational_fields_meta_box_handler($item){
        global $wpdb, $table_name, $table_name_options;
    ?>
        <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table" id="relation-tbl">
        <tbody>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="id_dependent_field"><?php _e('Select Dependent Field', 'switch_panel_cost_estimator')?></label>
                </th>
                <td>
                    <select name="id_dependent_field" id="dependent_field" style="width: 30%;">
                        <?php
                            $get_field_ID = $wpdb->get_results("SELECT id_form_field FROM $table_name_options WHERE id_form_field_detail = ".$item['id_dependent_field_option'], ARRAY_A);
                            foreach ($get_field_ID as $field_ID) {
                                $ID = $field_ID['id_form_field'];
                            }
                        ?>
                        <option value="0" <?php echo ('0' == $ID)?'selected="selected"':'';?>>--Select One--</option>
                    <?php 
                        $getAllFields = $wpdb->get_results("SELECT * FROM $table_name WHERE is_dependent = 'Y' ORDER BY sequence ASC", ARRAY_A);
                        if(!empty($getAllFields)){
                            foreach ($getAllFields as $getAllField) {
                    ?>
                        <option value="<?php echo $getAllField['id_form_field']; ?>" <?php echo ($getAllField['id_form_field'] == $ID)?'selected="selected"':'';?>><?php echo $getAllField['title']; ?></option>
                    <?php } } ?>
                    </select>
                </td>
            </tr>
            <tr class="form-field" id="get_dependent_field_options"></tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="id_field_related_to"><?php _e('Field Related To', 'switch_panel_cost_estimator')?></label>
                </th>
                <td>
                    <select name="id_field_related_to" id="id_field_related_to" style="width: 30%;">
                        <option value="0" <?php echo ('0' == $item['id_field_related_to'])?'selected="selected"':'';?>>--Select One--</option>
                    <?php 
                        $getAllFields = $wpdb->get_results("SELECT * FROM $table_name WHERE is_dependent = 'N' ORDER BY sequence ASC", ARRAY_A);
                        if(!empty($getAllFields)){
                            foreach ($getAllFields as $getAllField) {
                    ?>
                        <option value="<?php echo $getAllField['id_form_field']; ?>" <?php echo ($getAllField['id_form_field'] == $item['id_field_related_to'])?'selected="selected"':'';?>><?php echo $getAllField['title']; ?></option>
                    <?php } } ?>
                    </select>
                </td>
            </tr>
            <tr class="form-field" id="get_related_to_field_options"></tr>
        </tbody>
        </table>
    <?php
    }
    
    function relational_fields_validate($item){
        $messages = array();

        if ($item['id_dependent_field'] == 0) $messages[] = __('Please select a dependent field', 'switch_panel_cost_estimator');
        if ($item['id_field_related_to'] == 0) $messages[] = __('Please select field related to', 'switch_panel_cost_estimator');

        if (empty($messages)) return true;
        return implode('<br />', $messages);
    }
?>
