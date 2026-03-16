/*
 * Copyright (c) 2019. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

var config = {
    map: {
        "*": {
            lazyLoad: "MageCloud_ImageLazyLoad/js/lazy.min",
            lazyLoadPlugins: 'MageCloud_ImageLazyLoad/js/lazy.plugins.min'
        }
    },
    shim: {
        'lazyLoad': {
            'deps': ['jquery']
        },
        'lazyLoadPlugins': {
            'deps': ['jquery', 'lazyLoad']
        }
    }
};