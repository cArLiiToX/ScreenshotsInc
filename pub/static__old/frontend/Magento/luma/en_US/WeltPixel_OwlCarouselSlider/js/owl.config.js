var OWL = {
	init: function () {
	},

	load: function () {
		this.arrows();
	},

	resize: function () {
		this.arrows();
	},

	ajaxComplete: function () {
		this.loader();
	},

	arrows: function () {
		var carouselElement = jQuery('[class*="owl-carousel-products-"]'),
			windowWidth = jQuery(window).width(),
			carouselWidth = carouselElement.width(),
			carouselContainer = carouselWidth + 120,
			carouselControls = carouselElement.find('.owl-nav');

		if (carouselContainer >= windowWidth) {
			carouselControls.addClass('fullscreen').find('.owl-prev').css({
				'left': 0,
				'top': -15
			});
			carouselControls.addClass('fullscreen').find('.owl-next').css({
				'right': 0,
				'top': -15
			});
		} else {
			carouselControls.find('.owl-prev').removeClass('fullscreen').removeAttr('style');
			carouselControls.find('.owl-next').removeClass('fullscreen').removeAttr('style');
		}
	},

	loader: function () {
			jQuery('.custom-slider #pre-div, .products.products-grid #pre-div').each(function(){
				jQuery(this).fadeOut('slow');
			});
	}
};

require(['jquery'],
	function ($) {
		$(document).ready(function () {
			OWL.init();
		});

		$(window).load(function () {
			OWL.load();
		});

		$(document).ready(function(){
			OWL.ajaxComplete();
		});

		var reinitTimer;
		$(window).on('resize', function () {
			clearTimeout(reinitTimer);
			reinitTimer = setTimeout(OWL.resize(), 100);
		});
	}
);
