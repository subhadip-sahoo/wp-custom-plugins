(function($){
    $(function(){
        $('#btn-search').click(function(e){
            e.preventDefault();
            var fields = $('#search-realtors').find('input[type=text], input[type=email], select').length;
            var field_values = 0;
            $('#search-realtors').find('input[type=text], input[type=email], select').each(function(){
                if($(this).val() == ''){
                    field_values++;
                }
            });
            if(fields == field_values){
                $('#realtors-list').remove();
                $('.realtors-wrapper .alert').show();
                return false;
            }
            $('.realtors-wrapper .alert').hide();
            $('#search-realtors').submit();
            return true;
        });
    });
})(jQuery);