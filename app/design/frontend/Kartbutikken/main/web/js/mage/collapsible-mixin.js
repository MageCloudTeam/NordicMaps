/*
 * Copyright (c) 2019. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

define([
    'jquery',
], function ($) {

    var hideProps = {},
        showProps = {};

    hideProps.height =  'hide';
    showProps.height =  'show';

    return function (widget) {

        $.widget('mage.collapsible', widget, {
            options: {
                scrollTo: false,
                parentOffset: null,
                target: null,
                offsetSelector: null,
            },

            /**
             * Create widget mixin
             *
             * @private
             */
            _create: function () {
                this.storage = $.localStorage;
                this.icons = false;

                if (typeof this.options.icons === 'string') {
                    this.options.icons = $.parseJSON(this.options.icons);
                }

                this._processPanels();
                this._processState();
                this._refresh();

                if (this.options.icons.header && this.options.icons.activeHeader) {
                    this._createIcons();
                    this.icons = true;
                }

                if (this.options.scrollTo) {
                    this.element.on('dimensionsChanged', function (e) {
                        if (e.target && e.target.classList.contains('active')) {
                            if (!this.options.target) {
                                return;
                            }

                            let scrollTop = $(this.options.target).offset().top;

                            if (this.options.parentOffset) {
                                scrollTop -= this.options.parentOffset;
                            }

                            if (this.options.offsetSelector) {
                                scrollTop -= $(this.options.offsetSelector).height();
                            }

                            if(scrollTop !== null) {
                                $([document.documentElement, document.body]).animate({scrollTop: scrollTop}, 100);
                            }
                        }
                    }.bind(this));
                }

                this._bind('click');
                this._trigger('created');
            },
        });

        return $.mage.collapsible;
    };
});