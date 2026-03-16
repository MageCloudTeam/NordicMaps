/*
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

var config = {
    map: {
        "*": {
            owl_carousel: "MageCloud_WeltPixelOwlCarouselSlider/js/owl.carousel",
            swiper: "MageCloud_WeltPixelOwlCarouselSlider/js/swiper-bundle.min",
            swiperWidget: "MageCloud_WeltPixelOwlCarouselSlider/js/swiperWidget",
            owlWidget: "MageCloud_WeltPixelOwlCarouselSlider/js/widget"
        }
    },
    shim: {
        owl_carousel: {
            deps: ["jquery"]
        },
    }
};
