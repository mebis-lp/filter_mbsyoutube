// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Javascript set youtube provider cache .
 *
 * @module     filter_mbsyoutube/setvideoprovidercache
 * @package    filter_mbsyoutube
 * @copyright  2019 Peter Mayer, ISB Bayern, peter.mayer@isb.bayern.de
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/notification'], function ($, ajax, notification) {

    var params;

    /**
     * Mark a medium as accepted.
     * @param {string} provider
     */
    function onVideoAcceptanceChange(provider) {
        ajax.call([{
            methodname: 'filter_mbsyoutube_setvideoprovidercache',
            args: {
                provider: provider,
                courseid: params.courseid
            },
            done: function (response) {
                if (response) {
                    location.reload();
                }
            },
            fail: notification.exception
        }]);
    }

    /**
     * Initialize the click event
     */
    function initClickEvent() {
        // Nothing to do, because there is no button to bind an event.
        if ($('.mbsyoutube-confirm').length == 0) {
            return;
        }

        // Unbind the click event, because otherwise the event could be bind multiple times.
        $('.mbsyoutube-confirm').unbind();

        // Now bind the click event.
        $('.mbsyoutube-confirm').click(function () {
            onVideoAcceptanceChange("YouTube");
        });
    }

    return {
        init: function (args) {
            params = args;

            // If there is already a mbsembed yt confirm button then bind the click event.
            if ($('.mbsyoutube-confirm').length != 0) {
                initClickEvent();
                return;
            }

            // If there is no yt confirm button. Observe the dome if there will be a change.
            var observer = new MutationObserver(function () {
                // Fired when a mutation occurs.
                if ($('.mbsyoutube-confirm').length > 0) {
                    initClickEvent();
                }
            });

            // Define what element should be observed by the observer
            // and what types of mutations trigger the callback
            observer.observe(document, {
                subtree: true,
                attributes: true
            });
        }
    };
});