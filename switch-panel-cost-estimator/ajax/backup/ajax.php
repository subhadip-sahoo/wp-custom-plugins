<?php
    add_action( 'wp_ajax_get_option_type', 'get_option_type_callback' );
    add_action( 'wp_ajax_get_options', 'get_options_callback' );
    add_action( 'wp_ajax_get_field_options', 'get_field_options_callback' );
    add_action( 'wp_ajax_get_popup_image', 'get_popup_image_callback' );
    add_action( 'wp_ajax_nopriv_get_popup_image', 'get_popup_image_callback' );
    add_action( 'wp_ajax_populate_related_field', 'populate_related_field_callback' );
    add_action( 'wp_ajax_nopriv_populate_related_field', 'populate_related_field_callback' );
    
    function get_option_type_callback(){
        global $wpdb, $table_name;
        $id_form_field = $_REQUEST['id_form_field'];
        $get_options = $wpdb->get_results("SELECT * FROM $table_name WHERE id_form_field = $id_form_field", ARRAY_A);
        if(!empty($get_options)){
            foreach ($get_options as $get_option) {
                echo json_encode(
                        array(
                            'option_type' => $get_option['option_type'], 
                            'price_unit' => get_option('switch_panel_price_unit'), 
                            'is_dependent' => $get_option['is_dependent']
                        )
                    );
            }
        }
        die();
    }
    
    function get_options_callback(){
        global $wpdb, $table_name, $table_name_options, $tbl_wp_relational_fields;
        $id_dependent_field = $_REQUEST['id_dependent_field'];
        $id_relational_field = $_REQUEST['id_relational_field'];
        $query = "  SELECT
                        wpffd.id_form_field_detail,                         
                        wpffd.option_name, 
                        wpffd.option_value, 
                        wpffd.sequence
                    FROM 
                        $table_name AS wpff
                    INNER JOIN 
                        $table_name_options AS wpffd
                    ON 
                        wpff.id_form_field = wpffd.id_form_field                    
                    WHERE 
                        wpff.id_form_field = $id_dependent_field
                    ORDER BY 
                        sequence ASC";
        $get_options = $wpdb->get_results($query, ARRAY_A);
        if(!empty($get_options)){
            $design = '<th valign="top" scope="row">';
            $design .= '<label for="id_dependent_field_option">Select Dependent Field Option</label>';
            $design .= '</th>';
            $design .= '<td>';
            $design .= '<select name="id_dependent_field_option" id="id_dependent_field_option" style="width: 30%;">';
            
            foreach ($get_options as $get_option) {
                $get_relation_option_id = $wpdb->get_results("SELECT id_dependent_field_option FROM $tbl_wp_relational_fields WHERE id_relational_field = $id_relational_field", ARRAY_A);
                $selected = '';
                if(!empty($get_relation_option_id) && $wpdb->num_rows == 1){
                    foreach ($get_relation_option_id as $relation_option_id) {
                        if($relation_option_id['id_dependent_field_option'] == $get_option['id_form_field_detail']){
                            $selected = "selected='selected'";
                        }
                    }
                }
                $design .= "<option value='".$get_option['id_form_field_detail']."' $selected>".$get_option['option_name']."</option>"; 
            }
            $design .= '</select>';
            $design .= '</td>';
        }
        echo $design;
        die();
    }
    
    function get_field_options_callback(){
        global $wpdb, $table_name, $table_name_options, $tbl_wp_relational_field_details;
        $id_field_related_to = $_REQUEST['id_field_related_to'];
        $id_relational_field = $_REQUEST['id_relational_field'];
        $query = "  SELECT 
                        wpffd.id_form_field_detail, 
                        wpffd.sequence,
                        CASE wpff.option_type
                            WHEN 'quantity' THEN CONCAT(wpffd.option_name,' through ',wpffd.option_value)
                            WHEN 'group_value' THEN wpffd.option_name
                        END
                        AS print_option
                    FROM 
                        $table_name AS wpff
                    INNER JOIN 
                        $table_name_options AS wpffd
                    ON 
                        wpff.id_form_field = wpffd.id_form_field
                    WHERE 
                        wpff.id_form_field = $id_field_related_to
                    ORDER BY 
                        sequence ASC";
        $get_options = $wpdb->get_results($query, ARRAY_A);
        if(!empty($get_options)){
            $design = '<th valign="top" scope="row">';
            $design .= '<label for="option_name"></label>';
            $design .= '</th>';
            $design .= '<td>';
            foreach ($get_options as $get_option) {
                $get_relation_option_id = $wpdb->get_results("SELECT price FROM $tbl_wp_relational_field_details WHERE id_relational_field = $id_relational_field AND id_field_related_to_option = ".$get_option['id_form_field_detail'], ARRAY_A);
                $price_val = '';
                if(!empty($get_relation_option_id) && $wpdb->num_rows == 1){
                    foreach ($get_relation_option_id as $relation_option_id) {
                        $price_val = $relation_option_id['price'];
                    }
                }
                $design .= '<label for="option_name">Option Name</label>'; 
                $design .= "<input type='text' name='opt_name' readonly id='".$get_option['print_option']."' style='width: 30%; margin: 15px 15px 15px 15px;' value='".stripslashes_deep($get_option['print_option'])."'/>"; 
                $design .= '<input type="hidden" name="related_options[]" style="width: 30%; margin-bottom: 15px;" value="'.$get_option['id_form_field_detail'].'"/>'; 
                $design .= '<label for="option_value">Price</label>'; 
                $design .= '<input type="text" name="related_options_price[]" style="width: 15%; margin: 15px 15px 15px 15px;" value="'.$price_val.'"/><span>'.get_option('switch_panel_price_unit').'</span><br/>'; 
            }
            $design .= '</td>';
        }
        echo $design;
        die();
    }
    
    function get_popup_image_callback(){
        global $wpdb, $table_name;
        $field_id = $_REQUEST['field_id'];
        $get_strOBJ = $wpdb->get_results("SELECT popup_image, popup_description FROM $table_name WHERE id_form_field = $field_id");
        if(!empty($get_strOBJ)){
            foreach ($get_strOBJ as $img) {
                $img_src = explode('/', $img->popup_image);
                $image_name = end($img_src);
                $image = "<img src='$img->popup_image' alt='$image_name' height='150' width='150'/>";
                $description = stripslashes_deep($img->popup_description);
            }
        }
        echo json_encode(array('image' => $image, 'image_desc' => $description));
        die();
    }
    
    function populate_related_field_callback(){
        global $wpdb, $table_name, $table_name_options, $tbl_wp_relational_fields, $tbl_wp_relational_field_details;
        $current_field_name = $_REQUEST['current_field_name'];
        $current_field_id = $_REQUEST['current_field_id'];
        $current_field_value = $_REQUEST['current_field_value'];
        $query = "  SELECT 
                        wffd.id_form_field,
                        wrf.id_dependent_field_option
                    FROM 
                        $tbl_wp_relational_fields AS wrf
                    INNER JOIN
                        $table_name_options AS wffd
                    ON wrf.id_dependent_field_option = wffd.id_form_field_detail
                    WHERE
                        wrf.id_field_related_to = $current_field_id";
        $get_field_ids = $wpdb->get_results($query);
        $all_ids = array();
        $dependency_ids = array();
        $all_commpn_field_ids = array();
        if(!empty($get_field_ids)){
            foreach ($get_field_ids as $get_field_id) {
                $all_ids[] = $get_field_id->id_form_field;
                $check_dependencies = $wpdb->get_results("SELECT id_field_related_to FROM $tbl_wp_relational_fields WHERE id_dependent_field_option = $get_field_id->id_dependent_field_option");
                if($wpdb->num_rows > 1){
                    foreach ($check_dependencies as $check_dependency) {
                        if($check_dependency->id_field_related_to == $current_field_id){
                            continue;
                        }
                        $dependency_ids[] = $check_dependency->id_field_related_to;
                        $get_all_common_fields = $wpdb->get_results("SELECT id_form_field FROM $table_name_options WHERE id_form_field_detail = $get_field_id->id_dependent_field_option");
                        if(!empty($get_all_common_fields)){
                            foreach ($get_all_common_fields as $get_all_common_field) {
                                $all_commpn_field_ids[] = $get_all_common_field->id_form_field;
                            }
                        }
                    }
                    $other_dependent_field_ids = implode(',', $dependency_ids);
                    $common_field_ids = implode(',', $all_commpn_field_ids);
                }
                else if($wpdb->num_rows == 1){
                    $other_dependent_field_ids = 'NULL';
                    $common_field_ids = 'NULL';
                }
            }
            $ids = implode(',', $all_ids);
        }else{
            $ids = 'NULL';
            $other_dependent_field_ids = 'NULL';
            $common_field_ids = 'NULL';
        }
        echo json_encode(array('related_field_ids' => $ids, 'other_dependent_field_ids' => $other_dependent_field_ids, 'common_field_ids' => $common_field_ids));
        die();
    }
?>