<?php
    function form_fields_options_listing_main($per_page){
        global $wpdb;
        $table = new Form_FieldOptions_List_Table();
        $table->prepare_items();

        $message = '';
        if ('delete' === $table->current_action()) {
            $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('%d Items deleted.', 'switch_panel_cost_estimator'), count($_REQUEST['id'])) . '</p></div>';
        }
        ?>
    <div class="wrap">
        <div class="icon32" id="icon-users"><br></div>
        <h2><?php _e('List of Field Options', 'switch_panel_cost_estimator')?> 
            <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=edit-field-options');?>"><?php _e('Add New Option', 'switch_panel_cost_estimator')?></a>
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
            <?php $table->search_box( __( 'Search', 'switch_panel_cost_estimator' ), 'field-options' ); ?>
            <?php $table->display(); ?>
        </form>
    </div>
     <?php   
    }
    
    function form_field_options_edit_main(){
        global $wpdb, $table_name_options;
        $message = '';
        $notice = '';
        $default = array(
            'id_form_field_detail' => 0,
            'id_form_field' => '',
            'option_name' => '',
            'option_value' => '',
            'price' => '',
            'sequence' => '',
        );

        if (wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
            $item = shortcode_atts($default, $_REQUEST);
            $item_valid = switch_panel_cost_estimator_validate($item);
//            echo '<pre>';
//            print_r($_POST);
//            echo '</pre>';
            if ($item_valid === true) {
                if ($item['id_form_field_detail'] == 0) {
                    $result = $wpdb->insert($table_name_options, $item);
                    $item['id_form_field_detail'] = $wpdb->insert_id;
                    if ($result) {
                        $message = __('Item was successfully saved', 'switch_panel_cost_estimator');
                    } else {
                        $notice = __('There was an error while saving item', 'switch_panel_cost_estimator');
                    }
                } else {
                    $result = $wpdb->update($table_name_options, $item, array('id_form_field_detail' => $item['id_form_field_detail']));
                    if ($result) {
                        $message = __('Item was successfully updated', 'switch_panel_cost_estimator');
                    } else {
                        $notice = __('There was an error while updating item', 'switch_panel_cost_estimator');
                    }
                }
            } else {
                $notice = $item_valid;
            }
        }
        else {
            $item = $default;
            if (isset($_REQUEST['id'])) {
                $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name_options WHERE id_form_field_detail = %d", $_REQUEST['id']), ARRAY_A);
                if (!$item) {
                    $item = $default;
                    $notice = __('Item not found', 'switch_panel_cost_estimator');
                }
            }
        }
        add_meta_box('switch_panel_cost_estimator_meta_box', 'Add FieldSet', 'switch_panel_cost_estimator_meta_box_handler', 'fieldsets', 'normal', 'default');
        ?>
        <div class="wrap">
        <div class="icon32" id="icon-users"><br></div>
        <h2><?php _e('Add Field Option', 'switch_panel_cost_estimator')?> 
            <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=form-field-options');?>"><?php _e('Back to list', 'switch_panel_cost_estimator')?></a>
        </h2>

        <?php if (!empty($notice)): ?>
        <div id="notice" class="error"><p><?php echo $notice ?></p></div>
        <?php endif;?>
        <?php if (!empty($message)): ?>
        <div id="message" class="updated"><p><?php echo $message ?></p></div>
        <?php endif;?>

        <form id="form" method="POST">
            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
            <input type="hidden" name="id_form_field_detail" value="<?php echo $item['id_form_field_detail'] ?>"/>
            
            <div class="metabox-holder" id="poststuff">
                <div id="post-body">
                    <div id="post-body-content">
                        <?php do_meta_boxes('fieldsets', 'normal', $item); ?>
                        <input type="submit" value="<?php _e('Save', 'switch_panel_cost_estimator')?>" id="submit" class="button-primary" name="submit">
                    </div>
                </div>
            </div>
        </form>
        </div>
    <?php
    }

    function switch_panel_cost_estimator_meta_box_handler($item){
        global $wpdb, $table_name;
    ?>
        <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
        <tbody>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="id_form_field"><?php _e('Select Field', 'switch_panel_cost_estimator')?></label>
                </th>
                <td>
                    <select name="id_form_field" id="id_form_field" style="width: 30%;">
                        <option value="0" <?php echo ('0' == $item['id_form_field'])?'selected="selected"':'';?>>--Select One--</option>
                    <?php 
                        $getAllFields = $wpdb->get_results("SELECT * FROM $table_name ORDER BY sequence ASC", ARRAY_A);
                        if(!empty($getAllFields)){
                            foreach ($getAllFields as $getAllField) {
                    ?>
                        <option value="<?php echo $getAllField['id_form_field']; ?>" <?php echo ($getAllField['id_form_field'] == $item['id_form_field'])?'selected="selected"':'';?>><?php echo $getAllField['title']; ?></option>
                    <?php } } ?>
                    </select>
                </td>
            </tr>
            <tr class="form-field option-type-tr">
                <th valign="top" scope="row">
                    <label for="option_name"><?php _e('Options', 'switch_panel_cost_estimator')?></label>
                </th>
                <td>
                    <label for="option_name"><?php _e('Option Name', 'switch_panel_cost_estimator');?></label>
                    <input type="text" name="option_name" id="start_value" style="width: 30%; margin: 0 15px 15px 15px;" value="<?php echo stripslashes_deep(esc_attr($item['option_name']));?>"/><br/>
                    <label for="option_value"><?php _e('Option Value', 'switch_panel_cost_estimator');?></label>
                    <input type="text" name="option_value" id="end_value" style="width: 30%; margin: 0 15px 15px 15px;" value="<?php echo stripslashes_deep(esc_attr($item['option_value']))?>"/>
                </td>
            </tr>
            <tr class="form-field price">
                <th valign="top" scope="row">
                    <label for="price"><?php _e('Price', 'switch_panel_cost_estimator')?></label>
                </th>
                <td>
                    <input id="price" name="price" type="text" style="width: 15%; margin: 0 15px 15px 15px;" value="<?php echo esc_attr($item['price'])?>" class="code" /><span></span>
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="sequence"><?php _e('Sequence', 'switch_panel_cost_estimator')?></label>
                </th>
                <td>
                    <select name="sequence" id="sequence" style="width: 10%;">
                    <?php 
                        for($sequence = 1; $sequence <= 20; $sequence++){
                    ?>
                        <option value="<?php echo $sequence; ?>" <?php echo ($sequence == $item['sequence'])?'selected="selected"':'';?>><?php echo $sequence; ?></option>
                    <?php } ?>
                    </select>
                </td>
            </tr>
        </tbody>
        </table>
    <?php
    }
    
    function switch_panel_cost_estimator_validate($item){
        $messages = array();

        if (!isset($item['id_form_field'])) $messages[] = __('Please select a field', 'switch_panel_cost_estimator');

        if (empty($messages)) return true;
        return implode('<br />', $messages);
    }
?>
