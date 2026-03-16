/*
 * Copyright (c) 2019. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

define([
    'jquery',
    'mage/smart-keyboard-handler',
    'mage/mage',
    'mage/ie-class-fixer',
    'domReady!',
    "toggleAdvanced"
], function ($, keyboardHandler) {
    'use strict';

    let stickyHeader = $('.page-header').first();
    if (stickyHeader.length > 0) {
        let threshold = 50,
            timeAdd,
            timeRemove;


        const addSticky = function () {
            stickyHeader.trigger("stickyMenuOn");
            $('body').addClass('sticky-header');
            stickyHeader.addClass('sticky');
        };

        const removeSticky = function () {
            stickyHeader.removeClass('sticky');
            $('body').removeClass('sticky-header');
            stickyHeader.trigger("stickyMenuOff");
        };

        $(window).on('scroll resize', function () {
            let win = $(this),
                curWinTop = win.scrollTop();

            if (win.width() < 768) {
                return;
            }

            if (curWinTop > threshold && !stickyHeader.hasClass('sticky')) {
                addSticky();
            }

            if (curWinTop == 0 && stickyHeader.hasClass('sticky')) {
                removeSticky();
            }

        });
        $(window).scroll();
    }

    $("[data-toggle]").each(function () {
        $(this).toggleAdvanced({
            selectorsToggleClass: "active",
            baseToggleClass: "expanded",
            toggleContainers: $(this).data('toggle'),
        });
    });

    if ($('body').hasClass('checkout-cart-index')) {
        if ($('#co-shipping-method-form .fieldset.rates').length > 0 &&
            $('#co-shipping-method-form .fieldset.rates :checked').length === 0
        ) {
            $('#block-shipping').on('collapsiblecreate', function () {
                $('#block-shipping').collapsible('forceActivate');
            });
        }
    }
      $(document).ready(function () {
        $('#switcher-store').prependTo('#store.menu');
      });

    $('.cart-summary').mage('sticky', {container: '#maincontent'});

    keyboardHandler.apply();
});
