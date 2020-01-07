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
 * @module     filter_mbsembed/setvideoprovidercache
 * @package    filter_mbsembed
 * @copyright  2019 Peter Mayer, ISB Bayern, peter.mayer@isb.bayern.de
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 define(['jquery', 'core/ajax', 'core/notification'], function ($, ajax, notification) {

    var params;

    function onVideoAcceptanceChange(provider) {

        ajax.call([{
            methodname: 'filter_mbsembed_setvideoprovidercache',
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

    return {
        init: function (args) {
            params = args;

            $('.mbsembed-twoclickwarning-button').click(function () {
                onVideoAcceptanceChange("YouTube");
            });
        }
    };
});