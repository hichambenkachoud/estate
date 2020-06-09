$(document).ready(function () {
    $('.change-language').on('click', function () {
        $('.lang-value').val($(this).data('lang'));
        $('#change-language').submit();

        return true;
    }) ;
    
   $('.estate-radio').on('click', function () {
       $('#estate_characteristics_icon').val($(this).val());
   }) ;
});
