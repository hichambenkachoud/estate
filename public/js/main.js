$(document).ready(function () {
    $('.change-language').on('click', function () {
        $('.lang-value').val($(this).data('lang'));
        $('#change-language').submit();

        return false;
    }) ;
});
