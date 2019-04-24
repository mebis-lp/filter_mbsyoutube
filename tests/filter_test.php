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
 * Unit tests.
 *
 * @package filter_mbsembed
 * @category test
 * @copyright 2019 Franziska Hübler, ISB Bayern
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/filter/mbsembed/filter.php'); // Include the code to test.

/**
 * Test case for filter_mbsembed.
 *
 * @copyright 2019 Franziska Hübler, ISB Bayern
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_mbsembed_testcase extends advanced_testcase {

    public function test_links() {
        global $DB, $USER;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $systemcontext = context_system::instance();

        \core\plugininfo\media::set_enabled_plugins(''); // Disable core mediaplugin.

        // Enable filter mbsembed.
        $filterobject = new stdClass();
        $filterobject->filter = 'mbsembed';
        $filterobject->contextid = $systemcontext->id;
        $filterobject->active = 1;
        $filterobject->sortorder = 0;
        $DB->insert_record('filter_active', $filterobject);

        $filter = new filter_mbsembed($systemcontext, []);

        // Check Mediathek H5P-Link - case 'provideVideo'.
        $h5plink = '<p>Roboter kümmern sich um Haus und Garten, helfen in der Altenpflege und ziehen in den Krieg'
                . ' - Science-Fiction oder Realität?<br>'
                . '<a href="https://mediathek.mebis.bayern.de/?doc=provideVideo&amp;identifier=BWS-04985575&amp;type=video'
                . '&amp;start=0&amp;title=Die20%Roboter20%kommen&amp;file=default.mp4">Die Roboter kommen</a></p>';
        $filtered = $filter->filter($h5plink);
        $expected = '<p>Roboter kümmern sich um Haus und Garten, helfen in der Altenpflege und ziehen in den Krieg'
                . ' - Science-Fiction oder Realität?<br><div class="mbsembed-responsive mbsembed-responsive-16by9 '
                . 'mbsembed-wrapper"><iframe class="mbsembed-frame mbsembed-responsive-item" src="'
                . 'https://mediathek.mebis.bayern.de/?doc=embeddedObject&amp;id=BWS-04985575&amp;type=video&amp;start=0'
                . '&amp;title=Die20%Roboter20%kommen&amp;file=default.mp4" allowfullscreen="allowfullscreen"></iframe></div>'
                . '<div class="pull-right mbsembed-link"><a class="internal" target="_blank" rel="noopener noreferrer" '
                . 'href="https://mediathek.mebis.bayern.de/?doc=record&amp;identifier=BWS-04985575">'
                . get_string('mediatheksitelink', 'filter_mbsembed') . '</a></div></p>';
        $this->assertEquals($expected, $filtered);

        // Check mediathek Mediaplayer-URL - case 'embeddedObject'.
        $mediaplayerlink = '<p>Roboter kümmern sich um Haus und Garten, helfen in der Altenpflege und ziehen in den Krieg'
                . ' - Science-Fiction oder Realität?<br>'
                . '<a href="https://mediathek.mebis.bayern.de/?doc=embeddedObject&amp;id=BWS-04985575&amp;type=video&amp;start=0'
                . '&amp;title=Die20%Roboter20%kommen">Die Roboter kommen</a></p>';
        $filtered = $filter->filter($mediaplayerlink);
        $expected = '<p>Roboter kümmern sich um Haus und Garten, helfen in der Altenpflege und ziehen in den Krieg'
                . ' - Science-Fiction oder Realität?<br><div class="mbsembed-responsive mbsembed-responsive-16by9 '
                . 'mbsembed-wrapper"><iframe class="mbsembed-frame mbsembed-responsive-item" src="'
                . 'https://mediathek.mebis.bayern.de/?doc=embeddedObject&amp;id=BWS-04985575&amp;type=video&amp;start=0'
                . '&amp;title=Die20%Roboter20%kommen" allowfullscreen="allowfullscreen"></iframe></div><div class="pull-right '
                . 'mbsembed-link"><a class="internal" target="_blank" rel="noopener noreferrer" '
                . 'href="https://mediathek.mebis.bayern.de/?doc=record&amp;identifier=BWS-04985575">' 
                . get_string('mediatheksitelink', 'filter_mbsembed') . '</a></div></p>';
        $this->assertEquals($expected, $filtered);

        // Check mediathek MZ-DVD - case 'playerExternal'.
        $mzdvdlink = '<p>Wie die Digitalisierung unsere Arbeitswelt verändert<br>'
                . '<a href="https://mediathek.mebis.bayern.de/?doc=playerExternal&amp;identifier=BWS-05565908">'
                . 'Digitalisierung unserer Arbeitswelt</a></p>';
        $filtered = $filter->filter($mzdvdlink);
        $expected = '<p>Wie die Digitalisierung unsere Arbeitswelt verändert<br><div class="mbsembed-responsive '
                . 'mbsembed-responsive-16by9 mbsembed-wrapper"><iframe class="mbsembed-frame mbsembed-responsive-item" src="'
                . 'https://mediathek.mebis.bayern.de/?doc=playerExternal&amp;identifier=BWS-05565908" '
                . 'allowfullscreen="allowfullscreen"></iframe></div><div class="pull-right mbsembed-link"><a class="internal" '
                . 'target="_blank" rel="noopener noreferrer" href="https://mediathek.mebis.bayern.de/?doc=record&amp;'
                . 'identifier=BWS-05565908">' . get_string('mediatheksitelink', 'filter_mbsembed') . '</a></div></p>';
        $this->assertEquals($expected, $filtered);

        // Check Prüfungsarchiv - case 'embed'.
        $prüfungsarchiv = '<p>Abiturprüfung 2018 INFORMATIK<br>'
                . '<a href="https://mediathek.mebis.bayern.de/?doc=embed&amp;identifier=BY-00125077">Aufgaben</a></p>';
        $filtered = $filter->filter($prüfungsarchiv);
        $expected = '<p>Abiturprüfung 2018 INFORMATIK<br><div class="mbsembed-responsive mbsembed-responsive-16by9 '
                . 'mbsembed-wrapper"><iframe class="mbsembed-frame mbsembed-responsive-item" src="'
                . 'https://mediathek.mebis.bayern.de/?doc=embed&amp;identifier=BY-00125077&amp;referrer=moodle&amp;mode=display'
                . '&amp;user=' . urlencode($USER->username) . '" allowfullscreen="allowfullscreen"></iframe></div><div class='
                . '"pull-right mbsembed-link"><a class="internal" target="_blank" rel="noopener noreferrer"'
                . ' href="https://mediathek.mebis.bayern.de/?doc=embed&amp;identifier=BY-00125077&amp;referrer=moodle&amp;'
                . 'mode=display&amp;user=' . urlencode($USER->username) . '">'
                . get_string('pruefungsarchivsitelink', 'filter_mbsembed') . '</a></div></p>';
        $this->assertEquals($expected, $filtered);
    }
}
