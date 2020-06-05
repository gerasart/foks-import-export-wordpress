(function ($) {

    $(function () {
        $(window).on('keydown', function (event) {
            if (event.ctrlKey || event.metaKey) {
                switch (String.fromCharCode(event.which).toLowerCase()) {
                    case 's':
                        event.preventDefault();
                        // console.log('ctrl + s');

                        $('#wpbody-content form').filter(':visible').submit();
                        break;
                }
            }
        });
    });

})(jQuery);