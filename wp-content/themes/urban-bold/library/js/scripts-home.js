
/* jquery cycle */
jQuery(document).ready(function($){
	var $slider = $('.cycle-slideshow');
	$slider.imagesLoaded( function() {
	$('#load-cycle').hide(); /* preloader */
	$slider.slideDown(1000);
	});
});