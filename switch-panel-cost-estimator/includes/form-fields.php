<?php
    function form_fields_listing_main($per_page){
        global $wpdb;
        $table = new Form_Fields_List_Table();
        $table->prepare_items();

        $message = '';
        if ('delete' === $table->current_action()) {
            $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('%d Items deleted.', 'switch_panel_cost_estimator'), count($_REQUEST['id'])) . '</p></div>';
        }
        ?>
    <div class="wrap">
        <div class="icon32" id="icon-users"><br></div>
        <h2><?php _e('List of Fields', 'switch_panel_cost_estimator')?> 
            <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=edit-field');?>"><?php _e('Add New Field', 'switch_panel_cost_estimator')?></a>
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
            <?php $table->search_box( __( 'Search', 'switch_panel_cost_estimator' ), 'fields' ); ?>
            <?php $table->display(); ?>
        </form>
    </div>
     <?php   
    }
    
    function form_field_edit_main(){
        global $wpdb, $table_name;
        $message = '';
        $notice = '';
        $default = array(
            'id_form_field' => 0,
            'title' => '',
            'field_type' => '',
            'field_name' => '',
            'option_type' => 'group_value',
            'popup_description' => '',
            'is_dependent' => 'N',
            'sequence' => ''
        );
        
        if (wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
            $item = shortcode_atts($default, $_REQUEST);
            $item_valid = fom_fields_validate($item);
            if ($item_valid === true) {
                if ($item['id_form_field'] == 0) {
                    $result = $wpdb->insert($table_name, $item);
                    $item['id_form_field'] = $wpdb->insert_id;
                    if ($result) {
                        $message = __('Item was successfully saved', 'switch_panel_cost_estimator');
                    } else {
                        $notice = __('There was an error while saving item', 'switch_panel_cost_estimator');
                    }
                } else {
                    $result = $wpdb->update($table_name, $item, array('id_form_field' => $item['id_form_field']));
                    $message = __('Item was successfully updated', 'switch_panel_cost_estimator');
                }
            } else {
                $notice = $item_valid;
            }
        }
        else {
            $item = $default;
            if (isset($_REQUEST['id'])) {
                $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id_form_field = %d", $_REQUEST['id']), ARRAY_A);
                if (!$item) {
                    $item = $default;
                    $notice = __('Item not found', 'switch_panel_cost_estimator');
                }
            }
        }
        if(isset($_POST['submit'])){
            $popup_image = $_FILES['popup_image']['name'];
            if(!is_dir(ABSPATH.'wp-content/uploads')){
                mkdir(ABSPATH.'wp-content/uploads');
            }
            if(!is_dir(ABSPATH.'wp-content/uploads/popup_image')){
                mkdir(ABSPATH.'wp-content/uploads/popup_image');
            }
            $destination_popup_image = ABSPATH.'wp-content/uploads/popup_image/'. $popup_image;
            $popup_image_url = site_url().'/wp-content/uploads/popup_image/'.$popup_image;
            if(move_uploaded_file($_FILES['popup_image']['tmp_name'], $destination_popup_image)){
                if(empty($item['popup_image'])){
                    $wpdb->update($table_name, array('popup_image' => $popup_image_url), array('id_form_field' => $item['id_form_field']));
                }
            }
            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id_form_field = %d", $item['id_form_field']), ARRAY_A);
        }
        add_meta_box('switch_panel_cost_estimator_meta_box', 'Add Field', 'fom_fields_meta_box_handler', 'fields', 'normal', 'default');
        ?>
        <div class="wrap">
            <div class="icon32" id="icon-users"><br></div>
            <h2><?php _e('Add Field', 'switch_panel_cost_estimator')?> 
                <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=form-fields');?>"><?php _e('Back to list', 'switch_panel_cost_estimator')?></a>
            </h2>

            <?php if (!empty($notice)): ?>
            <div id="notice" class="error"><p><?php echo $notice ?></p></div>
            <?php endif;?>
            <?php if (!empty($message)): ?>
            <div id="message" class="updated"><p><?php echo $message ?></p></div>
            <?php endif;?>

            <form id="form" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
                <input type="hidden" name="id_form_field" value="<?php echo $item['id_form_field'] ?>"/>

                <div class="metabox-holder" id="poststuff">
                    <div id="post-body">
                        <div id="post-body-content">
                            <?php do_meta_boxes('fields', 'normal', $item); ?>
                            <input type="submit" value="<?php _e('Save', 'switch_panel_cost_estimator')?>" id="submit" class="button-primary" name="submit" style="margin-bottom:20px;">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    <?php
    }

    function fom_fields_meta_box_handler($item){
        global $wpdb, $table_name;
    ?>
        <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
        <tbody>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="title"><?php _e('Field Title', 'switch_panel_cost_estimator')?></label>
                </th>
                <td>
                    <input id="title" name="title" type="text" style="width: 50%;" value="<?php echo esc_attr($item['title']);?>" class="code" required />
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="field_name"><?php _e('Field Name', 'switch_panel_cost_estimator')?></label>
                </th>
                <td>
                    <input id="field_name" name="field_name" type="text" style="width: 50%;" value="<?php echo esc_attr($item['field_name']);?>" class="code" />
                </td>
            </tr>
            <tr class="form-field popup-image-box">
                <th valign="top" scope="row">
                    <label for="popup_image"><?php _e('Popup Image', 'switch_panel_cost_estimator')?></label>
                </th>
                <td>
                    <p>
                        <?php echo(isset($item['popup_image']) && $item['popup_image']!="" )? '<img id="popup_image_preview" src="'.$item['popup_image'].'"  height="150px" width="150px"/>':'<img id="popup_image_preview" src="'.plugins_url().'/switch-panel-cost-estimator/images/noMediaUploaded.png"  height="150px" width="150px"/>';  ?>
                    </p>
                    <a href="javascript:void(0);" value="Browse..." onclick="document.getElementById('popup_image').click();" style="font-size:17px"/>Upload image</a>
                    <input id="popup_image" name="popup_image" type="file"  onchange='ImagePreview.UpdatePreview(this)' class="code" style="display:none"/>
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="popup_description"><?php _e('Popup Description', 'switch_panel_cost_estimator')?></label>
                </th>
                <td>
                    <?php wp_editor(stripslashes_deep($item['popup_description']), 'popup_description',  $settings = array('media_buttons' => FALSE, 'quicktags' => FALSE, 'tinymce' => TRUE, 'textarea_rows' => 10));?>
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="is_dependent"><?php _e('Dependent On Other Fields', 'switch_panel_cost_estimator')?></label>
                </th>
                <td>
                    <select name="is_dependent" id="is_dependent" style="width: 15%;">
                        <option value="N" <?php echo ('N' == $item['is_dependent'])?'selected="selected"':'';?>>No</option>
                        <option value="Y" <?php echo ('Y' == $item['is_dependent'])?'selected="selected"':'';?>>Yes</option>
                    </select>
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="field_type"><?php _e('Select Field Type', 'switch_panel_cost_estimator')?></label>
                </th>
                <td>
                    <select name="field_type" id="field_type" style="width: 15%;">
                        <option value="0" <?php echo ('0' == $item['field_type'])?'selected="selected"':'';?>>--Select One--</option>
                        <option value="select" <?php echo ('select' == $item['field_type'])?'selected="selected"':'';?>>Select Box</option>
                    </select>
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="option_type"><?php _e('Option Type', 'switch_panel_cost_estimator')?></label>
                </th>
                <td>
                    <input type="radio" name="option_type" id="quantity" style="width: 2%;" value="quantity" <?php echo ($item['option_type'] === 'quantity')?'checked':'';?>/>
                    <label for="quantity"><?php _e('Quantity', 'switch_panel_cost_estimator');?></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="radio" name="option_type" id="group_value" style="width: 2%;" value="group_value" <?php echo ($item['option_type'] === 'group_value')?'checked':'';?>/>
                    <label for="group_value"><?php _e('Guoup Value', 'switch_panel_cost_estimator');?></label>
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
    
    function fom_fields_validate($item){
        $messages = array();

        if (empty($item['title'])) $messages[] = __('Field title is required', 'switch_panel_cost_estimator');
        if (empty($item['field_name'])) $messages[] = __('Field name is required', 'switch_panel_cost_estimator');
        if ($item['field_type'] == '0') $messages[] = __('Please select a field type', 'switch_panel_cost_estimator');

        if (empty($messages)) return true;
        return implode('<br />', $messages);
    }

    function switch_panel_cost_estimator_languages(){
        load_plugin_textdomain('switch_panel_cost_estimator', false, dirname(plugin_basename(__FILE__)));
    }
    add_action('init', 'switch_panel_cost_estimator_languages');
?>