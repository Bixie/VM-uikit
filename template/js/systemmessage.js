/* *************
* Joomla! System messages via uikit.notify
* (c)2014 Matthijs Alles - Bixie
*/


jQuery(function($) {

	(function(){
		$('#system-message [data-uk-alert]').each(function(){
			var message = $(this);
			showMessage(message.find('.text').html(),message.data('type'));
			message.remove();
		});

	})();
	function showMessage(message,style) {
		$.UIkit.notify({
			message : message,
			status  : style,
			timeout : 5000,
			pos     : 'top-center'
		});
	}
});