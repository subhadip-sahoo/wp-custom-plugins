(function($) {
    $(function() {
        $('#toplevel_page_welcome-page ul li.wp-first-item').hide();
        
        var id_form_field = $('#id_form_field').val();
        var data = {action:'get_option_type', id_form_field: + id_form_field};
        $.post(ajaxurl, data, function(response){
            var strOBJ = JSON.parse(response);
            if(strOBJ.option_type === 'quantity'){
                $('.option-type-tr td label:first').empty().text('Start Value');
                $('.option-type-tr td label:last').empty().text('End Value');
            }
            else if(strOBJ.option_type === 'group_value'){
                $('.option-type-tr td label:first').empty().text('Option Name');
                $('.option-type-tr td label:last').empty().text('Option Value');
            }
            if(strOBJ.is_dependent === 'N'){
                $('.price td input').prop('disabled', false);
                $('.price span').empty().text(strOBJ.price_unit);
            }
            else if(strOBJ.is_dependent === 'Y'){
                $('.price td input').prop('disabled', true);
                $('.price span').empty().text('No price needed. This field is dependen on others fields.');
            }
        });
        
        var id_dependent_field = $('#dependent_field').val();
        var id_relational_field = $('#id_relational_field').val();
        var data_dependent_field = {action:'get_options', id_dependent_field: + id_dependent_field, id_relational_field: + id_relational_field};
        $.post(ajaxurl, data_dependent_field, function(second_TR){
            $("#get_dependent_field_options").empty().append(second_TR);
        });
        
        var id_field_related_to = $('#id_field_related_to').val();
        var id_relational_field = $('#id_relational_field').val();
            var data_field_related_to = {action:'get_field_options', id_field_related_to: + id_field_related_to, id_relational_field: + id_relational_field};
        $.post(ajaxurl, data_field_related_to, function(last_TR){
            $("#get_related_to_field_options").empty().append(last_TR);
        });
        
        $('#field_name').focus(function(){
            var title = $('#title').val();
            title = title.replace(/[&\/\\#,+()@_$~%.'":*?<>{}]/g, '');
            title = title.replace(/[, ]+/g, ' ').trim();
            var words = title.split(' ');
            var str = words.join('_');
            $(this).val(str.toLowerCase());
        });
        
        $('#field_type').change(function(){
            var fieldsArr = ['select', 'radio', 'checkbox'];
            var field_type = $(this).val();
            if(fieldsArr.indexOf(field_type) != -1){
                if(field_type != 'select'){
                    $('#quantity').attr('disabled', 'disabled');
                }else{
                    $('#quantity').removeAttr('disabled');
                }
            }
        });
        
        $('#id_form_field').change(function(){
            var id_form_field = $(this).val();
            var data = {action:'get_option_type', id_form_field: + id_form_field};
            $.post(ajaxurl, data, function(response){
                var strOBJ = JSON.parse(response);
                if(strOBJ.option_type === 'quantity'){
                    $('.option-type-tr td label:first').empty().text('Start Value');
                    $('.option-type-tr td label:last').empty().text('End Value');
                }
                else if(strOBJ.option_type === 'group_value'){
                    $('.option-type-tr td label:first').empty().text('Option Name');
                    $('.option-type-tr td label:last').empty().text('Option Value');
                }
                if(strOBJ.is_dependent === 'N'){
                    $('.price td input').prop('disabled', false);
                    $('.price span').empty().text(strOBJ.price_unit);
                }
                else if(strOBJ.is_dependent === 'Y'){
                    $('.price td input').prop('disabled', true);
                    $('.price span').empty().text('No price needed. This field is dependent on others fields.');
                }
            });
        });
        
        $('#dependent_field').change(function(){
            var id_dependent_field = $(this).val();
            var id_relational_field = $('#id_relational_field').val();
            var data_dependent_field = {action:'get_options', id_dependent_field: + id_dependent_field, id_relational_field: + id_relational_field};
            $.post(ajaxurl, data_dependent_field, function(second_TR){
                $("#get_dependent_field_options").empty().append(second_TR);
            });
        });
        
        $('#id_field_related_to').change(function(){
            var id_field_related_to = $(this).val();
            var id_relational_field = $('#id_relational_field').val();
            var data_field_related_to = {action:'get_field_options', id_field_related_to: + id_field_related_to, id_relational_field: + id_relational_field};
            $.post(ajaxurl, data_field_related_to, function(last_TR){
                $("#get_related_to_field_options").empty().append(last_TR);
            });
        });
        
        ImagePreview = {
            UpdatePreview: function(obj){
              if(!window.FileReader){
                 alert('This browser does not support file reader!');
                 return false;
              } else {
                 var reader = new FileReader();
                 var target = null;

                 reader.onload = function(e) {
                    target =  e.target || e.srcElement;
                    $("#popup_image_preview").prop("src", target.result);
                 };
                  reader.readAsDataURL(obj.files[0]);
              }
            }
        };
        $("#add_more_files").click(function(){
             $(this).before('<p><input id="switch_panel_email_attachment" name="switch_panel_email_attachment[]" type="file" style="width: 50%; margin: 0 10px 10px 0;" class="code"/><a href="javascript:void(0);" class="remove-upload">Delete</a></p>');
        });
        $(".remove-upload").live('click',function() {
             $(this).parent().remove();
        });
    });
})(jQuery);