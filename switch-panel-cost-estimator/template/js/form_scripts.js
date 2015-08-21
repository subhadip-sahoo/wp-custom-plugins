(function($) {
    $(function() {
        $('fieldset#switch-panel select').change(function(){
            var current_field_name = $(this).attr('name');
            var current_field_id = $(this).attr('id');
            var current_field_value = $(this).val();
            var split_current_id = current_field_id.split('-');
            var field_id = split_current_id[1];
            var current_data = {
                action: 'populate_related_field', 
                current_field_name: + current_field_name, 
                current_field_id: + field_id, 
                current_field_value: + current_field_value
            };
            $.post(switch_panel.ajaxurl, current_data, function(response){
//                alert(response);
                var parse_arr = JSON.parse(response);
                var related_field_ids = parse_arr.related_field_ids;
                var other_dependent_field_ids = parse_arr.other_dependent_field_ids;
                var common_field_ids = parse_arr.common_field_ids;
//                alert(related_field_ids);
//                alert(other_dependent_field_ids);
//                alert(common_field_ids);
                if(related_field_ids != 'NULL'){
                    if(current_field_value.toString() != '0' && current_field_value.toUpperCase().trim() != 'NONE'){
                        if(related_field_ids.indexOf(',') != -1){
                            var split_str = related_field_ids.split(',');
                            for(var i = 0; i < split_str.length; i++){
                                $('#field-'+ split_str[i]).prop('disabled', false);
                                $('#label-'+ split_str[i]).show();
                                $('#field-'+ split_str[i]).show();
                                $('#span-'+ split_str[i]).show();
                            }
                        }else{
                            $('#field-'+ related_field_ids).prop('disabled', false);
                            $('#label-'+ related_field_ids).show();
                            $('#field-'+ related_field_ids).show();
                            $('#span-'+ related_field_ids).show();
                        }
                    }else{
                        if(other_dependent_field_ids != 'NULL'){
                            if(other_dependent_field_ids.indexOf(',') != -1){
                                var get_all_ids = other_dependent_field_ids.split(',');
                                for(var j = 0; j < get_all_ids.length; j++){
                                    var check_field_value = $('#field-' + get_all_ids[j]).val();
                                    if(check_field_value.toString() == '0' || check_field_value.toUpperCase().trim() == 'NONE'){
                                        if(related_field_ids.indexOf(',') != -1){
                                            var split_str = related_field_ids.split(',');
                                            for(var i = 0; i < split_str.length; i++){
                                                $('#field-'+ split_str[i]).prop('disabled', true);
                                                $('#label-'+ split_str[i]).hide();
                                                $('#field-'+ split_str[i]).hide();
                                                $('#span-'+ split_str[i]).hide();
                                            }
                                        }else{
                                            $('#field-'+ related_field_ids).prop('disabled', true);
                                            $('#label-'+ related_field_ids).hide();
                                            $('#field-'+ related_field_ids).hide();
                                            $('#span-'+ related_field_ids).hide();
                                        }
                                    }else{
                                        if(related_field_ids.indexOf(',') != -1){
                                            var split_str = related_field_ids.split(',');
                                            if(common_field_ids.indexOf(',') != -1){
                                                var split_common_field_ids = common_field_ids.split(',');
                                                for(var i = 0; i < split_str.length; i++){
                                                    if(split_str[i] == split_common_field_ids[i]){
                                                        continue;
                                                    }
                                                    $('#field-'+ split_str[i]).prop('disabled', true);
                                                    $('#label-'+ split_str[i]).hide();
                                                    $('#field-'+ split_str[i]).hide();
                                                    $('#span-'+ split_str[i]).hide();
                                                }
                                            }
                                            else{
                                                for(var i = 0; i < split_str.length; i++){
                                                    if(split_str[i] == common_field_ids){
                                                        continue;
                                                    }
                                                    $('#field-'+ split_str[i]).prop('disabled', true);
                                                    $('#label-'+ split_str[i]).hide();
                                                    $('#field-'+ split_str[i]).hide();
                                                    $('#span-'+ split_str[i]).hide();
                                                }
                                            }
                                        }else{
                                            $('#field-'+ related_field_ids).prop('disabled', true);
                                            $('#label-'+ related_field_ids).hide();
                                            $('#field-'+ related_field_ids).hide();
                                            $('#span-'+ related_field_ids).hide();
                                        }
                                    }
                                }
                            }else{
                                var check_field_value = $('#field-' + other_dependent_field_ids).val();
                                if(check_field_value.toString() == '0' || check_field_value.toUpperCase().trim() == 'NONE'){
                                    if(related_field_ids.indexOf(',') != -1){
                                        var split_str = related_field_ids.split(',');
                                        for(var i = 0; i < split_str.length; i++){
                                            $('#field-'+ split_str[i]).prop('disabled', true);
                                            $('#label-'+ split_str[i]).hide();
                                            $('#field-'+ split_str[i]).hide();
                                            $('#span-'+ split_str[i]).hide();
                                        }
                                    }else{
                                        $('#field-'+ related_field_ids).prop('disabled', true);
                                        $('#label-'+ related_field_ids).hide();
                                        $('#field-'+ related_field_ids).hide();
                                        $('#span-'+ related_field_ids).hide();
                                    }
                                }else{
                                    if(related_field_ids.indexOf(',') != -1){
                                        var split_str = related_field_ids.split(',');
                                        if(common_field_ids.indexOf(',') != -1){
                                            var split_common_field_ids = common_field_ids.split(',');
                                            for(var i = 0; i < split_str.length; i++){
                                                if(split_common_field_ids.indexOf(split_str[i]) != -1){
                                                    continue;
                                                }
                                                $('#field-'+ split_str[i]).prop('disabled', true);
                                                $('#label-'+ split_str[i]).hide();
                                                $('#field-'+ split_str[i]).hide();
                                                $('#span-'+ split_str[i]).hide();
                                            }
                                        }
                                        else{
                                            for(var i = 0; i < split_str.length; i++){
                                                if(split_str[i] == common_field_ids){
                                                    continue;
                                                }
                                                $('#field-'+ split_str[i]).prop('disabled', true);
                                                $('#label-'+ split_str[i]).hide();
                                                $('#field-'+ split_str[i]).hide();
                                                $('#span-'+ split_str[i]).hide();
                                            }
                                        }
                                    }else{
                                        $('#field-'+ related_field_ids).prop('disabled', true);
                                        $('#label-'+ related_field_ids).hide();
                                        $('#field-'+ related_field_ids).hide();
                                        $('#span-'+ related_field_ids).hide();
                                    }
                                }
                            }
                        }else{
                            if(related_field_ids.indexOf(',') != -1){
                                var split_str = related_field_ids.split(',');
                                for(var i = 0; i < split_str.length; i++){
                                    $('#field-'+ split_str[i]).prop('disabled', true);
                                    $('#label-'+ split_str[i]).hide();
                                    $('#field-'+ split_str[i]).hide();
                                    $('#span-'+ split_str[i]).hide();
                                }
                            }else{
                                $('#field-'+ related_field_ids).prop('disabled', true);
                                $('#label-'+ related_field_ids).hide();
                                $('#field-'+ related_field_ids).hide();
                                $('#span-'+ related_field_ids).hide();
                            }
                        }
                    }
                }
            });
        });
        $('.item-switch').tooltipster({
            content: 'Loading...',
            functionBefore: function(origin, continueTooltip) {
                continueTooltip();
                if (origin.data('ajax') !== 'cached') {
                    var anchor_id = $(this).attr('id');
                    var str_to_arr = anchor_id.split('_');
                    var field_id = str_to_arr[1];
                    var image_data = {action:'get_popup_image', field_id: + field_id};
                    $.post(switch_panel.ajaxurl, image_data, function(data){
                        var json_data = JSON.parse(data);
                        var image = json_data.image;
                        var image_desc = json_data.image_desc;
                        origin.tooltipster('content', $('<div class="tooltip_description">'+ image + '<span>'+ image_desc +'</span></div>')).data('ajax', 'cached');
                    });
                }
            }
        });
        if($('#Other').prop('checked') == true){
            $('#heard_from_other').show();
        }
        $('#Other').change(function(){
            if($(this).prop('checked') == true){
                $('#heard_from_other').show();
            }
            else{
                $('#heard_from_other').hide();
            }
        });
	$("div.close_popup").hover(
            function() {
                $('span.ecs_tooltip').show();
            },
            function () {
                $('span.ecs_tooltip').hide();
            }
        );

	$("div.close_popup").click(function() {
            disablePopup();  
	});

	$(this).keyup(function(event) {
            if (event.which == 27) {
                    disablePopup(); 
            }
	});

        $("div#backgroundPopup").click(function() {
            disablePopup(); 
	});
        

	 /************** start: functions. **************/
	function loading() {
            $("div.loader").show();
	}
	function closeloading() {
            $("div.loader").fadeOut('normal');
	}

	var popupStatus = 0; 

	function loadPopup() {
            if(popupStatus == 0) { 
                closeloading(); 
                $("#toPopup").fadeIn(0500);
                $("#backgroundPopup").css("opacity", "0.7"); 
                $("#backgroundPopup").fadeIn(0001);
                popupStatus = 1;
            }
	}

	function disablePopup() {
            if(popupStatus == 1) {
                $("#toPopup").fadeOut("normal");
                $("#backgroundPopup").fadeOut("normal");
                popupStatus = 0; 
            }
	}
    });
})(jQuery);