/*
 * Copyright (c) 2019. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

var config = {
    map: {
        "*": {
            bss_owlslider: "MageCloud_WeltPixelOwlCarouselSlider/js/owl.carousel",
            qtyAddToCart: "js/quantity-add-to-cart",
            scrollTo: "js/scroll-to"
        }
    },
    config: {
        mixins: {
            "Magento_Catalog/js/product/addtocart-button": {
                "Magento_Catalog/js/product/addtocart-button-mixin": true
            },
            // 'mage/collapsible': {
            //     'js/mage/collapsible-mixin': true
            // }
        }
    }
};
 
