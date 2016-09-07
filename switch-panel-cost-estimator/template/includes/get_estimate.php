<?php
    global $wpdb, $table_name, $table_name_options, $tbl_wp_relational_fields, $tbl_wp_relational_field_details, $tbl_enquiries;
    $err_msg = '';
    $suc_msg = '';
    if(isset($_POST['get_estimate'])){
        $fullName = esc_sql($_POST['fullName']);
        $company = esc_sql($_POST['company']);
        $email = esc_sql($_POST['email']);
        $phone = esc_sql($_POST['phone']);
        $requestType = esc_sql($_POST['requestType']);
        $no_of_panels = esc_sql($_POST['no_of_panels']);
        if(isset($_POST['how_did_you_hear_about_us']) && is_array($_POST['how_did_you_hear_about_us'])){
            $how_did_you_hear_about_us = implode(',', esc_sql($_POST['how_did_you_hear_about_us']));
        }
        if(isset($_POST['other_option'])){
            $other_option = esc_sql($_POST['other_option']);
        }
        $wouldLikeContact = esc_sql($_POST['wouldLikeContact']);
        $projectDetails = esc_sql($_POST['projectDetails']);
        if(empty($projectDetails)){
            $projectDetails = 'Undefined';
        }
        if(empty($fullName)){
            $err_msg = 'Full name is required.';
        }
        else if(empty($email)){
            $err_msg = 'Email is required.';
        }
        else if(filter_var($email, FILTER_VALIDATE_EMAIL) == FALSE){
            $err_msg = 'Enter a valid email.';
        }
        else if($requestType == '0'){
            $err_msg = 'Request Type is required.';
        }
        else if(in_array('Other', $_POST['how_did_you_hear_about_us']) && empty ($other_option)){
            $err_msg = 'Enter how did you hear about us.';
        }
        if(empty($err_msg)){
            $starting_price = get_option('switch_panel_starting_price');
            $total_price = $starting_price;
            
            $get_all_fields = $wpdb->get_results("SELECT * FROM $table_name ORDER BY sequence ASC");
            $data = array();
            $user_enquiries = array();
            array_push($user_enquiries, array('title' => 'Full name', 'value' => $fullName));
            array_push($user_enquiries, array('title' => 'Company', 'value' => $company));
            array_push($user_enquiries, array('title' => 'Email', 'value' => $email));
            array_push($user_enquiries, array('title' => 'Phone', 'value' => $phone));
            array_push($user_enquiries, array('title' => 'Request Type', 'value' => $requestType));
            array_push($user_enquiries, array('title' => 'How many of these panels would you be looking to purchase?', 'value' => $no_of_panels));
            array_push($user_enquiries, array('title' => 'How did you hear about us?', 'value' => $how_did_you_hear_about_us));
            if(isset($_POST['other_option'])){
                array_push($user_enquiries, array('title' => 'Other', 'value' => $other_option));
            }
            array_push($user_enquiries, array('title' => 'Would you like a New Wire Marine representative to contact you?', 'value' => $wouldLikeContact));
            array_push($user_enquiries, array('title' => 'Please tell us about your project', 'value' => $projectDetails));
            if(!empty($get_all_fields)){
                foreach ($get_all_fields as $get_all_field) {
                    if($get_all_field->option_type == 'quantity' && $get_all_field->is_dependent == 'Y'){

                    }
                    else if($get_all_field->option_type == 'quantity' && $get_all_field->is_dependent == 'N'){
                        $field_id = $get_all_field->id_form_field;
                        $field_title = $get_all_field->title;
                        $field_value = stripslashes_deep($_POST[$get_all_field->field_name]);
                        array_push($data, array('title' => $field_title, 'value' => $field_value));
                        array_push($user_enquiries, array('title' => $field_title, 'value' => $field_value));
                        $fetch_options = $wpdb->get_results("SELECT * FROM $table_name_options WHERE id_form_field = $field_id");
                        if(!empty($fetch_options)){
                            foreach ($fetch_options as $fetch_option) {
                                $options_price = $fetch_option->price;
                            }
                            $total_price = $total_price + ($field_value * $options_price);
                        }
                    }
                    else if($get_all_field->option_type == 'group_value' && $get_all_field->is_dependent == 'Y'){
                        if(isset($_POST[$get_all_field->field_name])){
                            $field_id = $get_all_field->id_form_field;
                            $field_title = $get_all_field->title;
                            $field_value = stripslashes_deep($_POST[$get_all_field->field_name]);
                            array_push($data, array('title' => $field_title, 'value' => $field_value));
                            array_push($user_enquiries, array('title' => $field_title, 'value' => $field_value));
                            $fetch_options = $wpdb->get_results("SELECT * FROM $table_name_options WHERE id_form_field = $field_id AND option_value = '".$field_value."'");
                            if(!empty($fetch_options)){
                                foreach ($fetch_options as $fetch_option) {
                                    $option_id = $fetch_option->id_form_field_detail;
                                }
                                $fetch_related_fields = $wpdb->get_results("SELECT * FROM $tbl_wp_relational_fields WHERE id_dependent_field_option = $option_id");
                                 if(!empty($fetch_related_fields)){
                                     foreach ($fetch_related_fields as $fetch_related_field) {
                                         $id_field_related_to = $fetch_related_field->id_field_related_to;
                                         $id_relational_field = $fetch_related_field->id_relational_field;
                                         $check_option_types = $wpdb->get_results("SELECT * FROM $table_name WHERE id_form_field = $id_field_related_to");
                                         foreach ($check_option_types as $check_option_type) {
                                             $related_field_name = $check_option_type->field_name;
                                             if(isset($_POST[$related_field_name])){
                                                 $related_field_value = $_POST[$related_field_name];
                                             }
                                             if($check_option_type->option_type == 'quantity'){
                                                 $get_related_field_option_id = $wpdb->get_results("SELECT * FROM $table_name_options WHERE id_form_field = $id_field_related_to");
                                                 if(!empty($get_related_field_option_id)){
                                                     foreach ($get_related_field_option_id as $id) {
                                                         $related_field_option_id = $id->id_form_field_detail;
                                                     }
                                                     $get_prices = $wpdb->get_results("SELECT * FROM $tbl_wp_relational_field_details WHERE id_relational_field = $id_relational_field AND id_field_related_to_option = $related_field_option_id");
                                                     if(!empty($get_prices)){
                                                         foreach ($get_prices as $get_price) {
                                                             $price = $get_price->price;
                                                         }
                                                         $total_price = $total_price + ($related_field_value * $price);
                                                     }
                                                 }
                                             }
                                             else if($check_option_type->option_type == 'group_value'){
                                                 $get_related_field_option_id = $wpdb->get_results("SELECT * FROM $table_name_options WHERE id_form_field = $id_field_related_to AND option_value = '".$related_field_value."'");
                                                 if(!empty($get_related_field_option_id)){
                                                     foreach ($get_related_field_option_id as $id) {
                                                         $related_field_option_id = $id->id_form_field_detail;
                                                     }
                                                     $get_prices = $wpdb->get_results("SELECT * FROM $tbl_wp_relational_field_details WHERE id_relational_field = $id_relational_field AND id_field_related_to_option = $related_field_option_id");
                                                     if(!empty($get_prices)){
                                                         foreach ($get_prices as $get_price) {
                                                             $price = $get_price->price;
                                                         }
                                                         $total_price = $total_price + $price;
                                                     }
                                                 }
                                             }
                                         }
                                     }
                                 }
                            }
                        }
                    }
                    else if($get_all_field->option_type == 'group_value' && $get_all_field->is_dependent == 'N'){
                        $field_id = $get_all_field->id_form_field;
                        $field_title = $get_all_field->title;
                        $field_value = $_POST[$get_all_field->field_name];
                        //echo stripslashes_deep(stripslashes_deep($field_value));
                        array_push($data, array('title' => $field_title, 'value' => stripslashes_deep(stripslashes_deep($field_value))));
                        array_push($user_enquiries, array('title' => $field_title, 'value' => stripslashes_deep(stripslashes_deep($field_value))));
                        $fetch_options = $wpdb->get_results("SELECT * FROM $table_name_options WHERE id_form_field = $field_id AND option_value = '".$field_value."'");
                        if(!empty($fetch_options)){
                            foreach ($fetch_options as $fetch_option) {
                                $options_price = $fetch_option->price;
                            }
                            $total_price = $total_price + $options_price;
                        }
                    }
                }
            }
            $low_end_price_percent = 3;
            $high_end_price_percent = 5;
            $low_end_price = $total_price - (($total_price * $low_end_price_percent)/100);
            $high_end_price = $total_price + (($total_price * $high_end_price_percent)/100);
            $us_phone_format = format_phone($phone);
            $email_to = get_option('switch_panel_email');
            $admin_name = get_option('switch_panel_email_name');
            $headers = "From: $fullName<".$email.">\r\n";
            $headers .= "Reply-To: ".$email."\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
            $subject = "An estimate has been calculated";
            $content = "Name: $fullName<br/>";
            $content .= "Company: $company<br/>";
            $content .= "Email: $email<br/>";
            $content .= "Phone: $us_phone_format<br/><br/><br/>";
            $content .= "Request Type: $requestType<br/>";
            $content .= "How many panels they'd like to purchase: $no_of_panels<br/>";
            $content .= "How they heard about us: $how_did_you_hear_about_us<br/>";
            if(isset($other_option) && $other_option != ''){
                $content .= "Other: $other_option<br/>";
            }
            
            $content .= "Would like contact: $wouldLikeContact<br/>";
            $content .= "Project details: $projectDetails<br/><br/><br/>";
            $content .= "--- Switch panel configuration ---";
            $content .= "<br/><br/>";
            foreach ($data as $panel) {
                $content .= "$panel[title]: $panel[value]<br/>";
            }
            $content .= "<br/><br/>";
            $content .= "--- Panel estimate ---";
            $content .= "<br/><br/>";
            $content .= "Actual: $".number_format($total_price, 2, '.', '')."<br/>";
            $content .= "Low end: $".number_format($low_end_price, 2, '.', '')."<br/>";
            $content .= "High end: $".number_format($high_end_price, 2, '.', '')."<br/>";
            $actual_price = "Actual: $".number_format($total_price, 2, '.', '')."<br/>";
            array_push($user_enquiries, array('title' => 'Actual', 'value' => '$'.number_format($total_price, 2, '.', '')));
            array_push($user_enquiries, array('title' => 'Low end', 'value' => '$'.number_format($low_end_price, 2, '.', '')));
            array_push($user_enquiries, array('title' => 'High end', 'value' => '$'.number_format($high_end_price, 2, '.', '')));
            if(wp_mail( $email_to, $subject, $content, $headers )){
                $user_email = $email;
                $user_headers = "From: $admin_name<".$email_to.">\r\n";
                $user_headers .= "Reply-To: ".$email_to."\r\n";
                $user_headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                $user_subject = 'An estimate has been calculated';
                if((get_option('switch_panel_email_subject')) != ''){
                    $user_subject = str_replace('[user_name]', $fullName, get_option('switch_panel_email_subject'));
                }
                $attachments = array();
                $all_files = unserialize(get_option('switch_panel_email_attachment'));
                if(!empty($all_files)){
                    foreach ($all_files as $file) {
                        array_push($attachments, $file);
                    }
                }
                $user_content = str_replace(array('[user_name]', '[switch_panel_form_details]'), array($fullName, $content),  stripslashes_deep(get_option('switch_panel_email_content')));
                $user_content = str_replace($actual_price, '', $user_content);
//                echo '<pre>';
//                print_r($user_enquiries);
//                echo '</pre>';
//                die();
                if(wp_mail($user_email, $user_subject, $user_content, $user_headers, $attachments)){
                    $show_estimate = TRUE;
                    $enquiry_data = array(
                      'name' => $fullName,
                      'email' => $email,
                      'estimated_cost' => $total_price,
                      'enquiry_date' => date('Y-m-d H:i:s'),
                      'enquiries' => serialize($user_enquiries)
                    );
                    $wpdb->insert($tbl_enquiries, $enquiry_data);
                }else{
                    $err_msg = 'Something has gone wrong. Please try again later.';
                }
            }else{
                $err_msg = 'Something has gone wrong. Please try again later.';
            }
        }
    }
?>