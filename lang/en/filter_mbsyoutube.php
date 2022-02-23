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
 * Languange definition
 *
 * @package    filter_mbsyoutube
 * @copyright  2020 Peter Mayer, ISB Bayern
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['filtername'] = 'Render two click solution for YouTube videos.';
$string['privacy:metadata'] = 'The embed mebis content filter plugin does not store any personal data.';
$string['mbsopenpopup'] = 'Open video player.';
$string['mbstwoclickboxtext'] = '<strong>Privacy Policy</strong>'
    . '<br />Once the video plays, personal <a href="https://policies.google.com/privacy" '
    . 'target="_blank" style="color:#e3e3e3 !important;">information</a>, such as the IP address, will be sent to YouTube.';
$string['mbsyoutube_twoclickacceptancebuttontext'] = 'Text for the acceptance button.';
$string['mbsyoutube_twoclickacceptancebuttontext_desc'] = 'This text will appear on the button to accept '
    . 'the privacy policy and proceed to access and play the video.';
$string['mbsyoutube_twoclickacceptancebuttonmsgtext'] = 'Text for the acceptance button in a user message.';
$string['mbsyoutube_twoclickacceptancebuttonmsgtext_desc'] = 'This text will appear on the button to accept '
        . 'proceed to access and play the video when this is sent in a message.';
$string['mbsyoutube_twoclickbackground'] = 'Background for the two click solution.';
$string['mbsyoutube_twoclickbackground_desc'] = 'Upload an image to use as background for the two click solution. '
        . 'This will tile the overlay asking to accept to watch the video.';
$string['mbsyoutube_twoclicklogo'] = 'Logo for the two click solution.';
$string['mbsyoutube_twoclicklogo_desc'] = 'Upload an image to use as logo for the two click solution. '
    . 'This will appear in the overlay asking to accept to watch the video.';
$string['mbsyoutube_twoclickmessage'] = 'Message for the two click solution.';
$string['mbsyoutube_twoclickmessage_desc'] = 'Enter a message informing about the privacy policy. '
        . 'This will appear in the overlay asking to accept to watch the video.';
$string['mbswatchvideo'] = 'Start videos ✓';
