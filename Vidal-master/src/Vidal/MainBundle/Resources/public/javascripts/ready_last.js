$(document).ready(function() {
	$('.vidalbox-menu').click(function() {
		ga('send', 'event', 'vidalbox', 'click', 'Меню слева');
	});

	$('.neirontin-menu').click(function() {
		ga('send', 'event', 'neirontin', 'click', 'Меню слева');
	});
});