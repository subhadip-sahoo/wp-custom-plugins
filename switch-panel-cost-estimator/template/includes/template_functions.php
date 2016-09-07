<?php
    function switch_panel_configuration(){
        global $wpdb, $table_name;
        $config_panel = '';
        $get_all_fields = $wpdb->get_results("SELECT * FROM $table_name ORDER BY sequence ASC", ARRAY_A);
        if(!empty($get_all_fields)){
            foreach ($get_all_fields as $get_all_field) {
                $config_panel .= generate_fields($get_all_field['id_form_field'], $get_all_field['title'], $get_all_field['field_name'], $get_all_field['field_type'], $get_all_field['option_type'], $get_all_field['popup_image'], $get_all_field['popup_description'], $get_all_field['is_dependent']);
            }
        }
        return $config_panel;
    }
    function generate_fields($field_id, $field_title, $field_name, $field_type, $option_type, $image_src, $image_description, $is_dependent){
        global $wpdb, $table_name_options;
        switch($field_type){
            case 'select':
                $style = 'style="display:none;"';
                $disabled = 'disabled';
                if(isset($_POST[$field_name])){
                    $style = '';
                    $disabled = '';
                }
                $design_field = ($is_dependent == 'Y')?'<label '.$style.' id="label-'.$field_id.'">'.$field_title.'</label>':'<label id="label-'.$field_id.'">'.$field_title.'</label>';
                $design_field .= ($is_dependent == 'Y')?'<select name="'.$field_name.'" id="field-'.$field_id.'" '.$style.' '.$disabled.'>':'<select name="'.$field_name.'" id="field-'.$field_id.'">';
                $design_field .= field_according_option_type($field_id, $field_name, $option_type);
                $design_field .= '</select>';
                $design_field .= ($is_dependent == 'Y')?'<span id="span-'.$field_id.'" '.$style.'><a href="javascript:void(0);" class="item-switch item_1 tooltiptopright tooltipbottomright tooltiptop tooltipbottom" title="What is this?" id="image_'.$field_id.'"><img src="'.  plugins_url().'/switch-panel-cost-estimator/template/images/question-mark.png" alt="question-mark"/><div class="tooltip_description" style="display:none" title="'.stripslashes_deep($image_description).'"><img src="'.$image_src.'" height="150" width="150" alt=""></div></a></span>'
                                                        :'<span id="span-'.$field_id.'"><a href="javascript:void(0);" class="item-switch item_1 tooltiptopright tooltipbottomright tooltiptop tooltipbottom" title="What is this?" id="image_'.$field_id.'"><img src="'.  plugins_url().'/switch-panel-cost-estimator/template/images/question-mark.png" alt="question-mark"/><div class="tooltip_description" style="display:none" title="'.stripslashes_deep($image_description).'"><img src="'.$image_src.'" height="150" width="150" alt=""></div></a></span>';
                return $design_field;
                break;
            case 'radio':
                break;
            case 'checkbox':
                break;
            default:
                break;
        }
    }
    function field_according_option_type($field_id, $field_name, $option_type){
        global $wpdb, $table_name_options;
        switch($option_type){
            case 'quantity':
                $fetch_options = $wpdb->get_results("SELECT * FROM $table_name_options WHERE id_form_field = $field_id ORDER BY sequence ASC", ARRAY_A);
                if(!empty($fetch_options)){
                    $design_options = '';
                    foreach ($fetch_options as $fetch_option) {
                        $start_val = 0; //$fetch_option['option_name'];
                        $end_val = $fetch_option['option_value'];
                        for($count = $start_val; $count <= $end_val; $count++){
                            $selected = '';
                            if(isset($_POST) && $_POST[$field_name] == $count){
                                $selected .= 'selected="selected"';
                            }
                            $design_options .= '<option value="'.$count.'" '.$selected.'>'.$count.'</option>';
                        }
                    }
                }
                return $design_options;
                break;
            case 'group_value':
                $fetch_options = $wpdb->get_results("SELECT * FROM $table_name_options WHERE id_form_field = $field_id ORDER BY sequence ASC", ARRAY_A);
                if(!empty($fetch_options)){
                    $design_options = '';
                    foreach ($fetch_options as $fetch_option) {
                        $option_name = $fetch_option['option_name'];
                        $option_value = $fetch_option['option_value'];
                        $selected = '';
                        if(isset($_POST) && $_POST[$field_name] == esc_sql($option_value)){
                            $selected .= "selected='selected'";
                        }
                        $design_options .= "<option value='".$option_value."' ".$selected.">".stripslashes_deep($option_name)."</option>";
                    }
                }
                return $design_options;
                break;
        }
    }
    
    function format_phone($phone){
        $phone = preg_replace("/[^0-9]/", "", $phone);

        if(strlen($phone) == 7)
            return preg_replace("/([0-9]{3})([0-9]{4})/", "$1-$2", $phone);
        elseif(strlen($phone) == 10)
            return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $phone);
        elseif(strlen($phone) == 11)
            return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $phone);
        else
            return $phone;
    }
?>