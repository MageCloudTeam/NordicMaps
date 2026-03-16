/**
 * Cookie Model
 */

define([
    'jquery',
    'underscore',
    'Amasty_GdprFrontendUi/js/model/cookie-data-provider',
    'Amasty_GdprFrontendUi/js/storage/cookie',
    'Amasty_GdprFrontendUi/js/storage/essential-cookie',
    'Amasty_GdprFrontendUi/js/action/ga-initialize',
    'mage/cookies',
    'jquery/jquery-storageapi'
], function ($, _, cookieDataProvider, cookieStorage, essentialStorage) {
    'use strict';

    return {
        initEventHandlers: function () {
            var body = $('body');

            body.on('amcookie_save', function () {
                this.setLastCookieAcceptance();
            }.bind(this));
            body.on('amcookie_allow', function () {
                this.setLastCookieAcceptance();
            }.bind(this));
        },

        deleteDisallowedCookie: function () {
            var disallowedCookie = $.mage.cookies.get('amcookie_disallowed');

            if (!disallowedCookie) {
                return;
            }

            disallowedCookie.split(',').forEach(function (name) {
                if (!essentialStorage.isEssential(name)) {
                    cookieStorage.delete(name);
                }
            });
        },

        getEssentialGroups: function () {
            var groups,
                filteredGroups;

            cookieDataProvider.getCookieData().done(function (cookieData) {
                groups = cookieData;
            });

            filteredGroups = _.filter(groups, function (group) {
                return group.isEssential;
            });

            return {
                'groups': filteredGroups.map(function (group) {
                    return group.groupId;
                })
            };
        },

        isCookieAllowed: function (cookieName) {
            var allowedGroups = $.mage.cookies.get('amcookie_allowed'),
                disallowedCookie = $.mage.cookies.get('amcookie_disallowed') || '',
                isCookiePolicyAllowed = $.mage.cookies.get('amcookie_policy_restriction') === 'allowed';

            if (!isCookiePolicyAllowed || essentialStorage.isEssential(cookieName)) {
                return true;
            }

            return !((!allowedGroups && !disallowedCookie)
                || disallowedCookie.split(',').indexOf(cookieName) !== -1);
        },

        setLastCookieAcceptance: function () {
            cookieDataProvider.getCookieData().done(function (cookieData) {
                $.localStorage.set('am-last-cookie-acceptance', cookieData.lastUpdate);
            });
        },

        triggerSave: function () {
            $('body').trigger('amcookie_save');
        },

        triggerAllow: function () {
            $('body').trigger('amcookie_allow');
        }
    };
});
