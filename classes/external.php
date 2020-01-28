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
 * Set cache for two click solution
 * @package   filter_mbsyoutube
 * @copyright 2019 Peter Mayer, ISB Bayern, peter.mayer@isb.bayern.de
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once("$CFG->libdir/externallib.php");

/**
 * Set cache for two click solution
 * @package   filter_mbsyoutube
 * @copyright 2019 Peter Mayer, ISB Bayern, peter.mayer@isb.bayern.de
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_mbsyoutube_external extends external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function setvideoprovidercache_parameters() {

        return new external_function_parameters([
            'provider'    => new external_value(PARAM_TEXT, 'Title of Provider'),
            'courseid'    => new external_value(PARAM_INT, 'Course ID')
        ]);
    }

    /**
     * Store a flag when a user has accept to view media from a specific video provider in session cache.
     *
     * @param string $provider
     * @param integer $courseid
     * @return bool
     */
    public static function setvideoprovidercache($provider, $courseid) {
        global $USER;

        $cache = \cache::make('filter_mbsyoutube', 'mbsexternalsourceaccept');
        $cache->set($USER->id . "_" . $courseid . "_" . $provider, true);

        if ($cache->get($USER->id . "_" . $courseid . "_" . $provider)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     */
    public static function setvideoprovidercache_returns() {
        return new external_value(PARAM_BOOL, 'Cache set success');
    }
}
