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
 * Lib for filter_mbsyoutube.
 *
 * @package   filter_mbsyoutube
 * @copyright 2022 Paola Maneggia, ISB
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use \block_mbsteachshare as mbst;
/**
 * Serve the files from the filter_mbsyoutube file area.
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return mixed
 */
function filter_mbsyoutube_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {

    // All contextlevel are admitted.
    if (!$context->contextlevel) {
        return false;
    }

    // Serve only from $filearea 'logo' or 'background'
    if ($filearea != 'logo' && $filearea != 'background') {
        return false;
    }

    // Make sure the user is logged in.
    require_login();

    // Extract the filename / filepath from the $args array.
    $filename = array_pop($args); // The last item in the $args array.
    $file = get_file_storage()->get_file($context->id, 'filter_mbsyoutube', $filearea, 0, '/', $filename);
    if (!$file or $file->is_directory()) {
        return false;
    }

    // We can now send the file back to the browser - in this case with a cache lifetime of $CFG->filelifetime and no filtering. 
    send_stored_file($file, 0, 0, false, $options);
    
}
