/**
 * Admin code for dismissing notifications.
 *
 */
/**
 * @version 0.1.0
 */
(function ($) {
    'use strict';
    $(function () {
        $('#simple-digital-clock-notice').on('click', '.notice-dismiss', function() {
            $.ajax({
                url: simpleDigitalClockAjax.ajaxurl,
                type: 'post',
                data: {
                    action: 'simple_digital_clock_hide_notice',
                    security: simpleDigitalClockAjax.nonce
                },
                success: function(response) {
                    //console.log(response);
                }
            });
        });
    });
})(jQuery);
