<?php
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
 * Settings for filter_mbsyoutube.
 *
 * @package    filter_mbsyoutube
 * @copyright  2022 Paola Maneggia, ISB Bayern
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    $settings->add(new admin_setting_configstoredfile(
            'filter_mbsyoutube/mbsyoutube_two_click_background',
            new lang_string('mbsyoutube_twoclickbackground', 'filter_mbsyoutube'),
            new lang_string('mbsyoutube_twoclickbackground_desc', 'filter_mbsyoutube'),
            'background',
            0,
            ['accepted_types' => ['image']]
        )
    );

    $settings->add(new admin_setting_configstoredfile(
            'filter_mbsyoutube/mbsyoutube_two_click_logo',
            new lang_string('mbsyoutube_twoclicklogo', 'filter_mbsyoutube'),
            new lang_string('mbsyoutube_twoclicklogo_desc', 'filter_mbsyoutube'),
            'logo',
            0,
            ['accepted_types' => ['image']]
        )
    );

    $settings->add(new admin_setting_confightmleditor(
            'filter_mbsyoutube/mbsyoutube_two_click_message',
            new lang_string('mbsyoutube_twoclickmessage', 'filter_mbsyoutube'),
            new lang_string('mbsyoutube_twoclickmessage_desc', 'filter_mbsyoutube'),
            get_string('mbstwoclickboxtext', 'filter_mbsyoutube')
        )
    );

    $settings->add(new admin_setting_configtext(
            'filter_mbsyoutube/mbsyoutube_two_click_acceptancebuttontext',
            new lang_string('mbsyoutube_twoclickacceptancebuttontext', 'filter_mbsyoutube'),
            new lang_string('mbsyoutube_twoclickacceptancebuttontext_desc', 'filter_mbsyoutube'),
            get_string('mbswatchvideo', 'filter_mbsyoutube')
        )
    );

    $settings->add(new admin_setting_configtext(
            'filter_mbsyoutube/mbsyoutube_two_click_acceptancebuttonmsgtext',
            new lang_string('mbsyoutube_twoclickacceptancebuttonmsgtext', 'filter_mbsyoutube'),
            new lang_string('mbsyoutube_twoclickacceptancebuttonmsgtext_desc', 'filter_mbsyoutube'),
            get_string('mbsopenpopup', 'filter_mbsyoutube')
        )
    );
}
