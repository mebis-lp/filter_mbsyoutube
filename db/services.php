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
 * Settings for two click solution.
 * @package   filter_mbsyoutube
 * @copyright 2019 Peter Mayer, ISB Bayern, peter.mayer@isb.bayern.de
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$functions = [
    'filter_mbsyoutube_setvideoprovidercache'         => [
        'classname'    => 'filter_mbsyoutube_external',
        'methodname'   => 'setvideoprovidercache',
        'classpath'    => 'filter/mbsyoutube/classes/external.php',
        'description'  => 'Set two click acceptance status to cache',
        'type'         => 'write',
        'ajax'         => true,
    ]
];
