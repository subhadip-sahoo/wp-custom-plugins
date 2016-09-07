<?php
    function plugin_custom_settings(){
        $notice = '';
        $message = '';
        $upload_dir = dirname(__FILE__). '/template/attachments/';
        $upload_url = plugins_url() . '/switch-panel-cost-estimator//template/attachments/';
        if(isset($_POST['submit'])){
            if(!empty($_POST['switch_panel_email']) && filter_var($_POST['switch_panel_email'], FILTER_VALIDATE_EMAIL) == FALSE){
                $notice .= 'Wrong email address.';
            }
            
            if(empty($notice)){
                $files = array();
                if(isset($_FILES['switch_panel_email_attachment']) && !empty($_FILES['switch_panel_email_attachment']['name'][0])){
                    $x = 0;  
                    foreach ( $_FILES['switch_panel_email_attachment']['name'] AS $k => $v){  
                         move_uploaded_file($_FILES["switch_panel_email_attachment"]["tmp_name"][$x], $upload_dir . $_FILES["switch_panel_email_attachment"]["name"][$x]);
                         array_push($files, $upload_dir . $_FILES["switch_panel_email_attachment"]["name"][$x]);
                         $x++;  
                    }
                    update_option('switch_panel_email_attachment', serialize($files));
                }
                foreach ($_POST as $key => $value) {
                    if($key === 'switch_panel_starting_price'){
                        update_option($key, number_format($value, 2, '.', ''));
                    }else if($key === 'switch_panel_email_content'){
                        update_option($key, $value);
                    }else if($key === 'switch_panel_email_attachment'){
                        continue;
                    }else{
                        update_option($key, sanitize_text_field($value));
                    }
                }
                $message .= 'Settings successfully saved.';
            }
        }
?>
        <div class="wrap">
            <div class="icon32" id="icon-users"><br></div>
            <h2><?php _e('Switch Panel Settings', 'switch_panel_cost_estimator')?></h2>

            <?php if (!empty($notice)): ?>
            <div id="notice" class="error"><p><?php echo $notice; ?></p></div>
            <?php endif;?>
            <?php if (!empty($message)): ?>
            <div id="message" class="updated"><p><?php echo $message; ?></p></div>
            <?php endif;?>

            <form id="form" method="POST" enctype="multipart/form-data">
                <div class="metabox-holder" id="poststuff">
                    <div id="post-body">
                        <div id="post-body-content">
                            <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
                                <tbody>
                                    <tr class="form-field">
                                        <th valign="top" scope="row">
                                            <label for="switch_panel_email"><?php _e('Admin Email Address', 'switch_panel_cost_estimator')?></label>
                                        </th>
                                        <td>
                                            <input id="switch_panel_email" name="switch_panel_email" type="email" style="width: 50%;" value="<?php echo (esc_attr(get_option('switch_panel_email')) != '')?esc_attr(get_option('switch_panel_email')):get_option('admin_email');?>" class="code" />
                                            <p class="description">Double check the email address before save. If email address is not provided then by default it will take the administrative email address of the site.</p>
                                        </td>
                                    </tr>
                                    <tr class="form-field">
                                        <th valign="top" scope="row">
                                            <label for="switch_panel_email_name"><?php _e('Set Administrative Name', 'switch_panel_cost_estimator')?></label>
                                        </th>
                                        <td>
                                            <input id="switch_panel_email_name" name="switch_panel_email_name" type="text" style="width: 50%;" value="<?php echo (esc_attr(get_option('switch_panel_email_name'))  != '')?esc_attr(get_option('switch_panel_email_name')):get_option('blogname');?>" class="code" />
                                            <p class="description">This will be used in email as from name. If this is not provided then by default it will take the blogname of the site.</p>
                                        </td>
                                    </tr>
                                    <tr class="form-field">
                                        <th valign="top" scope="row">
                                            <label for="switch_panel_email_subject"><?php _e('Email Subject', 'switch_panel_cost_estimator')?></label>
                                        </th>
                                        <td>
                                            <input id="switch_panel_email_subject" name="switch_panel_email_subject" type="text" style="width: 50%;" value="<?php echo (esc_attr(get_option('switch_panel_email_subject')));?>" class="code" />
                                            <p class="description">If email subject is not provided then by default it will display "An estimate has been calculated" as user's email subject. Here [user_name] can be used if you want user input name from the form. Ex: Dear [user_name] || Open it to view the details. In that case the variable should be the same as given.</p>
                                        </td>
                                    </tr>
                                    <tr class="form-field">
                                        <th valign="top" scope="row">
                                            <label for="switch_panel_price_unit"><?php _e('Choose Price Unit', 'switch_panel_cost_estimator')?></label>
                                        </th>
                                        <td>
                                            <select name="switch_panel_price_unit" id="switch_panel_price_unit" style="width: 15%;">
                                                <option value="USD" <?php echo ('USD' == get_option('switch_panel_price_unit'))?'selected="selected"':'';?>>USD</option>
                                                <option value="GBP" <?php echo ('GBP' == get_option('switch_panel_price_unit'))?'selected="selected"':'';?>>GBP</option>
                                                <option value="EUR" <?php echo ('EUR' == get_option('switch_panel_price_unit'))?'selected="selected"':'';?>>EUR</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr class="form-field">
                                        <th valign="top" scope="row">
                                            <label for="switch_panel_starting_price"><?php _e('Starting Price', 'switch_panel_cost_estimator')?></label>
                                        </th>
                                        <td>
                                            <input id="switch_panel_starting_price" name="switch_panel_starting_price" type="number" pattern="[0-9]+([\.|,][0-9]+)?" step="0.01" required style="width: 15%;" value="<?php echo esc_attr(get_option('switch_panel_starting_price'));?>" class="code"/><span><?php echo get_option('switch_panel_price_unit');?></span>
                                            <p class="description">Set Starting Price. If don't have any starting price then put 0. Enter value upto two decimal points. Example: 16.09</p>
                                        </td>
                                    </tr>
                                    <tr class="form-field">
                                        <th valign="top" scope="row">
                                            <label for="switch_panel_legend"><?php _e('Set Panel Title ', 'switch_panel_cost_estimator')?></label>
                                        </th>
                                        <td>
                                            <input id="switch_panel_legend" name="switch_panel_legend" type="text" style="width: 50%;" value="<?php echo esc_attr(get_option('switch_panel_legend'));?>" class="code"/>
                                            <p class="description">Set a fieldset. Example: Switch Panel Configuration</p>
                                        </td>
                                    </tr>
                                    <tr class="form-field">
                                        <th valign="top" scope="row">
                                            <label for="switch_panel_email_content"><?php _e('Email Content', 'switch_panel_cost_estimator')?></label>
                                        </th>
                                        <td>
                                            <?php wp_editor(stripslashes_deep(get_option('switch_panel_email_content')), 'switch_panel_email_content',  $settings = array('media_buttons' => FALSE, 'quicktags' => FALSE, 'tinymce' => TRUE, 'textarea_rows' => 20, 'wpautop' => FALSE, 'tabindex' => 1));?>
                                            <p class="description">In this email content if you want to show username from the from then put [user_name] for username and to show from generated estimated details put [switch_panel_form_details] into the email content where exactly you want to display these things. An example has been set here.</p>
                                        </td>
                                    </tr>
                                    <tr class="form-field upload-TR">
                                        <th valign="top" scope="row">
                                            <label for="switch_panel_email_attachment"><?php _e('Upload files', 'switch_panel_cost_estimator')?></label>
                                        </th>
                                        <td>
                                            <?php 
                                                $all_files = unserialize(get_option('switch_panel_email_attachment'));
                                                if(!empty($all_files)){
                                                    foreach ($all_files as $file) {
                                            ?>
                                                <p><?php echo end(explode('/', $file));?> <a href="<?php echo $upload_url.end(explode('/', $file));?>">View</a></p>
                                            <?php
                                                        
                                                    }
                                                }
                                            ?>
                                            <p>
                                                <input id="switch_panel_email_attachment" name="switch_panel_email_attachment[]" type="file" style="width: 50%; margin: 0 10px 10px 0;" class="code"/>
                                            </p>
                                            <input type="button" name="add_more" id="add_more_files" class="button-primary" value="Add More Files" style="width: 15%;"/>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <input type="submit" value="<?php _e('Save Settings', 'switch_panel_cost_estimator')?>" id="submit" class="button-primary" name="submit" style="margin-bottom:20px;">
                        </div>
                    </div>
                </div>
            </form>
        </div>
<?php
    }
?>
