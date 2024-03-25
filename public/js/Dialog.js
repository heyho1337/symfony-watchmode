$(document).ready(function () {
	$('body').on('click', '.closeButton', function () {
		var dialog = $(this).parent();
		dialog.remove();
	});
});