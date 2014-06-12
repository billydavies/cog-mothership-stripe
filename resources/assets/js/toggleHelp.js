jQuery(document).ready(function($) {

	// Help Toggle (CVC)
	$('.form-row .help span').click(function () {
		if ($('.form-row .help p').hasClass("open")) {
			$('.form-row .help p').fadeOut().removeClass("open");
		} else {
			$('.form-row .help p').fadeIn().addClass("open");
		}
	});
});
