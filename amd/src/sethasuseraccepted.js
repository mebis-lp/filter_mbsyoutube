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

import $ from 'jquery';
import Ajax from 'core/ajax';
import Notification from 'core/notification';

let mycourseid = 0;

export const init = (courseid) => {
    mycourseid = courseid;
    initClickEvent();
    // The button might not be on the document yet: Observe the DOM for a change.
    new MutationObserver(initClickEvent).observe(document,
    {
        subtree: true,
        childList: true
    });
}

/**
 * Mark a medium as accepted.
 * @param {string} provider
 */
function onVideoAcceptanceChange(mycourseid, provider) {
    Ajax.call([{
        methodname: 'filter_mbsyoutube_setvideoprovidercache',
        args: {
            provider: provider,
            courseid: mycourseid
        },
        done: function (response) {
            if (response) {
                location.reload();
            }
        },
        fail: Notification.exception
    }]);
}

/**
 * Initialize the click event
 */
function initClickEvent() {
    // First unbind the click event, because it could be bound multiple times.
    $('.mbsyoutube-confirm').unbind().click(function(e) {
        onVideoAcceptanceChange(mycourseid, "YouTube");
        e.stopPropagation();
    });
}

