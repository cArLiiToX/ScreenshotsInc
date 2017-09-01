var config = {
    map: {
        '*': {
            owl_carousel: 'WeltPixel_OwlCarouselSlider/js/owl.carousel',
            owl_config: 'WeltPixel_OwlCarouselSlider/js/owl.config'
        }
    },
    shim: {
        owl_carousel: {
            deps: ['jquery']
        },
        owl_config: {
            deps: ['jquery','owl_carousel']
        }
    }
};