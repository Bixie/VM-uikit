/* *************
 * Joomla! System messages via uikit.notify
 * (c)2014 Matthijs Alles - Bixie
 */

jQuery(function ($) {
    (function () {
        $('#system-message').addClass('uk-hidden');
        $('#system-message [data-uk-alert]').each(function () {
            var message = $(this);
            showMessage(message.find('.text').html(), message.data('type'));
        });
        var count = 0, posX = ['top-center', 'bottom-center', 'top-left', 'top-right'],
            messX = ['Van voor', 'Naar achter', 'Van links', 'Naar rechts'];
        $('[href="http://www.bixie.nl"]').bind("contextmenu", function () {
            if (count == 4) count = 0;
            $.UIkit.notify({
                message: messX[count],
                status: 'success',
                timeout: 1000,
                pos: posX[count]
            });
            count++;
            return false;
        });
        $('[href="http://www.webgerelateerd.nl"]').bind("contextmenu", function () {
            (function () {
                var s = document.createElement('style');
                s.innerHTML = '@-webkit-keyframes roll {from { -webkit-transform: rotate(0deg) } to { -webkit-transform: rotate(360deg) }}' +
                    ' @-moz-keyframes roll { from { -moz-transform: rotate(0deg) } to { -moz-transform: rotate(360deg) }}' +
                    ' @keyframes roll {from { transform: rotate(0deg) } to { transform: rotate(360deg) }}' +
                    ' body { -moz-animation-name: roll; -moz-animation-duration: 4s; -moz-animation-iteration-count: 1; ' +
                    '-webkit-animation-name: roll; -webkit-animation-duration: 4s; -webkit-animation-iteration-count: 1;}';
                document.getElementsByTagName('head')[0].appendChild(s);
            }());
            return false;
        });
    })();
    function showMessage(message, style) {
        $.UIkit.notify({
            message: message,
            status: style,
            timeout: 5000,
            pos: 'top-center'
        });
    }
});