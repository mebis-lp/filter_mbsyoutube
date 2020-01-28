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
 * @package filter_mbsyoutube
 * @category test
 * @copyright 2019 Franziska Hübler, 2019 Peter Mayer, ISB Bayern
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/filter/mbsyoutube/filter.php'); // Include the code to test.

/**
 * Test case for filter_mbsyoutube.
 *
 * @copyright 2019 Peter Mayer, ISB Bayern
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_mbsyoutube_testcase extends advanced_testcase {
    /**
     * Test case for filter_mbsyoutube.
     */
    public function test_links() {
        global $DB, $USER;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $systemcontext = context_system::instance();

        \core\plugininfo\media::set_enabled_plugins(''); // Disable core mediaplugin.

        // Enable filter mbsyoutube.
        $filterobject = new stdClass();
        $filterobject->filter = 'mbsyoutube';
        $filterobject->contextid = $systemcontext->id;
        $filterobject->active = 1;
        $filterobject->sortorder = 0;
        $DB->insert_record('filter_active', $filterobject);

        $filter = new filter_mbsyoutube($systemcontext, []);

        // Expected for the next few assertions.
        $expected = '<p>YouTube - URL als <a href="xyz"> eingefügt<br><div class="mbsyoutube-responsive mbsyoutube-responsive-16by9'
            . ' mbsyoutube-wrapper mbsyoutube-twoclickwarning-wrapper" style=""><div class="mbsyoutube-twoclickwarning-boxtext" hidden="hidden">'
            . '<strong>Privacy Policy</strong><br />Once the video plays, personal <a href="https://policies.google.com/privacy"'
            . ' target="_blank" style="color:#e3e3e3 !important;">information</a>, such as the IP address, will be sent to YouTube.</div>'
            . '<div class="mbsyoutube-twoclickwarning-buttonbox" hidden="hidden"><input type="button" class="mbsyoutube-twoclickwarning-button"'
            . ' value="Start videos ✓" hidden="hidden" /></div><div class="mbsyoutube-status-wrapper" id="yt__statwrap__phpunit__qcQ6x123KwU"'
            . ' hidden="hidden"><img class="mbsyoutube-img-logo" src="https://www.example.com/moodle/theme/mebis/pix/mebis-logo.png" /><br>'
            . '<input id="yt__play__phpunit__qcQ6x123KwU" type="button" class="mbsyoutube-twoclickwarning-button mbsyoutube-yt-play"'
            . ' value="Resume video" /><input id="yt__restart__phpunit__qcQ6x123KwU" type="button" class="mbsyoutube-twoclickwarning-button'
            . ' mbsyoutube-yt-restart" value="Restart video" hidden="hidden" /></div><div id="yt__phpunit__qcQ6x123KwU" class="mbsyoutube-frame'
            . ' mbsyoutube-responsive-item mbsyoutube-ytiframe" allowfullscreen="allowfullscreen" data-extern="{&quot;wmode&quot;:&quot;transparent&quot;'
            . ',&quot;modestbranding&quot;:1,&quot;rel&quot;:0,&quot;showinfo&quot;:0,&quot;iv_load_policy&quot;:3,&quot;autohide&quot;'
            . ':1,&quot;enablejsapi&quot;:1}" crossorigin="anonymous"></div><div class="mbsyoutube-bar-overlay" id="yt__baroverlay__phpunit__qcQ6x123KwU"'
            . ' hidden="hidden"></div></div></p><p>Das ist das Ende!</p>';

        // A a Tag with youtube url.
        $youtube = '<p>YouTube - URL als <a href="xyz"> eingefügt<br>'
            . '<a href="https://www.youtube.com/watch?v=qcQ6x123KwU">Link zum Video</a></p>'
            . '<p>Das ist das Ende!</p>';
        $filtered = $filter->filter($youtube);
        $this->assertEquals($expected, $filtered);

        // Youtube url as plain text.
        $youtube = '<p>YouTube - URL als <a href="xyz"> eingefügt<br>'
            . 'https://www.youtube.com/watch?v=qcQ6x123KwU</p>'
            . '<p>Das ist das Ende!</p>';
        $filtered = $filter->filter($youtube);
        $this->assertEquals($expected, $filtered);

        // A a Tag with youtube-nocookie url.
        $youtube = '<p>YouTube - URL als <a href="xyz"> eingefügt<br>'
            . '<a href="https://www.youtube-nocookie.com/watch?v=qcQ6x123KwU">Link zum Video</a></p>'
            . '<p>Das ist das Ende!</p>';
        $filtered = $filter->filter($youtube);
        $this->assertEquals($expected, $filtered);

        // Youtube-nocookie url as plain text.
        $youtube = '<p>YouTube - URL als <a href="xyz"> eingefügt<br>'
            . 'https://www.youtube-nocookie.com/watch?v=qcQ6x123KwU</p>'
            . '<p>Das ist das Ende!</p>';
        $filtered = $filter->filter($youtube);
        $this->assertEquals($expected, $filtered);

        // A a Tag with youtube embed url.
        $youtube = '<p>YouTube - URL als <a href="xyz"> eingefügt<br>'
            . '<a href="https://www.youtube.com/embed/qcQ6x123KwU">Link zum Video</a></p>'
            . '<p>Das ist das Ende!</p>';
        $filtered = $filter->filter($youtube);
        $this->assertEquals($expected, $filtered);

        // Youtube url as plain embed  text.
        $youtube = '<p>YouTube - URL als <a href="xyz"> eingefügt<br>'
            . 'https://www.youtube.com/embed/qcQ6x123KwU</p>'
            . '<p>Das ist das Ende!</p>';
        $filtered = $filter->filter($youtube);
        $this->assertEquals($expected, $filtered);

        // A a Tag with youtube-nocookie embed  url.
        $youtube = '<p>YouTube - URL als <a href="xyz"> eingefügt<br>'
            . '<a href="https://www.youtube-nocookie.com/embed/qcQ6x123KwU">Link zum Video</a></p>'
            . '<p>Das ist das Ende!</p>';
        $filtered = $filter->filter($youtube);
        $this->assertEquals($expected, $filtered);

        // Youtube-nocookie url as plain embed  text.
        $youtube = '<p>YouTube - URL als <a href="xyz"> eingefügt<br>'
            . 'https://www.youtube-nocookie.com/embed/qcQ6x123KwU</p>'
            . '<p>Das ist das Ende!</p>';
        $filtered = $filter->filter($youtube);
        $this->assertEquals($expected, $filtered);

        // Iframe with youtube embed url.
        $youtube = '<p>YouTube - URL als <a href="xyz"> eingefügt<br>'
            . '<iframe width="560" height="315" src="https://www.youtube.com/embed/qcQ6x123KwU"'
            . ' frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope;'
            . ' picture-in-picture" allowfullscreen></iframe></p>'
            . '<p>Das ist das Ende!</p>';
        $filtered = $filter->filter($youtube);
        $this->assertEquals($expected, $filtered);

        // Iframe with youtube-nocookie embed url.
        $youtube = '<p>YouTube - URL als <a href="xyz"> eingefügt<br>'
            . '<iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/qcQ6x123KwU"'
            . ' frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope;'
            . ' picture-in-picture" allowfullscreen></iframe></p>'
            . '<p>Das ist das Ende!</p>';
        $filtered = $filter->filter($youtube);
        $this->assertEquals($expected, $filtered);

        // Iframe with youtube watch url.
        $youtube = '<p>YouTube - URL als <a href="xyz"> eingefügt<br>'
            . '<iframe width="560" height="315" src="https://www.youtube.com/watch?v=qcQ6x123KwU"'
            . ' frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope;'
            . ' picture-in-picture" allowfullscreen></iframe></p>'
            . '<p>Das ist das Ende!</p>';
        $filtered = $filter->filter($youtube);
        $this->assertEquals($expected, $filtered);

        // Iframe with youtube-nocookie watch url.
        $youtube = '<p>YouTube - URL als <a href="xyz"> eingefügt<br>'
            . '<iframe width="560" height="315" src="https://www.youtube-nocookie.com/watch?v=qcQ6x123KwU"'
            . ' frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope;'
            . ' picture-in-picture" allowfullscreen></iframe></p>'
            . '<p>Das ist das Ende!</p>';
        $filtered = $filter->filter($youtube);
        $this->assertEquals($expected, $filtered);

        // A a Tag with youtube short url.
        $youtube = '<p>YouTube - URL als <a href="xyz"> eingefügt<br>'
            . '<a href="https://youtu.be/qcQ6x123KwU">Link zum Video</a></p>'
            . '<p>Das ist das Ende!</p>';
        $filtered = $filter->filter($youtube);
        $this->assertEquals($expected, $filtered);

        // Youtube short url as plain text.
        $youtube = '<p>YouTube - URL als <a href="xyz"> eingefügt<br>'
            . 'https://youtu.be/qcQ6x123KwU</p>'
            . '<p>Das ist das Ende!</p>';
        $filtered = $filter->filter($youtube);
        $this->assertEquals($expected, $filtered);

        // Expected for the next few assertions.
        $expected = '<p>YouTube - URL als <a href="xyz"> eingefügt<br><div class="mbsyoutube-responsive mbsyoutube-responsive-16by9'
            . ' mbsyoutube-wrapper mbsyoutube-twoclickwarning-wrapper" style=""><div class="mbsyoutube-twoclickwarning-boxtext" hidden="hidden">'
            . '<strong>Privacy Policy</strong><br />Once the video plays, personal <a href="https://policies.google.com/privacy"'
            . ' target="_blank" style="color:#e3e3e3 !important;">information</a>, such as the IP address, will be sent to YouTube.</div>'
            . '<div class="mbsyoutube-twoclickwarning-buttonbox" hidden="hidden"><input type="button" class="mbsyoutube-twoclickwarning-button"'
            . ' value="Start videos ✓" hidden="hidden" /></div><div class="mbsyoutube-status-wrapper" id="yt__statwrap__phpunit__qcQ6x123KwU"'
            . ' hidden="hidden"><img class="mbsyoutube-img-logo" src="https://www.example.com/moodle/theme/mebis/pix/mebis-logo.png" /><br>'
            . '<input id="yt__play__phpunit__qcQ6x123KwU" type="button" class="mbsyoutube-twoclickwarning-button mbsyoutube-yt-play"'
            . ' value="Resume video" /><input id="yt__restart__phpunit__qcQ6x123KwU" type="button" class="mbsyoutube-twoclickwarning-button'
            . ' mbsyoutube-yt-restart" value="Restart video" hidden="hidden" /></div><div id="yt__phpunit__qcQ6x123KwU" class="mbsyoutube-frame'
            . ' mbsyoutube-responsive-item mbsyoutube-ytiframe" allowfullscreen="allowfullscreen" data-extern="{&quot;wmode&quot;:&quot;transparent&quot;'
            . ',&quot;modestbranding&quot;:1,&quot;rel&quot;:0,&quot;showinfo&quot;:0,&quot;iv_load_policy&quot;:3,&quot;autohide&quot;'
            . ':1,&quot;enablejsapi&quot;:1,&quot;start&quot;:&quot;15&quot;}" crossorigin="anonymous"></div><div class="mbsyoutube-bar-overlay"'
            . ' id="yt__baroverlay__phpunit__qcQ6x123KwU"'
            . ' hidden="hidden"></div></div></p><p>Das ist das Ende!</p>';

        // Youtube short url with start parameter.
        $youtube = '<p>YouTube - URL als <a href="xyz"> eingefügt<br>'
            . 'https://youtu.be/qcQ6x123KwU?t=15</p>'
            . '<p>Das ist das Ende!</p>';
        $filtered = $filter->filter($youtube);
        $this->assertEquals($expected, $filtered);

        // Youtube short url with start parameter.
        $youtube = '<p>YouTube - URL als <a href="xyz"> eingefügt<br>'
            . 'https://youtu.be/qcQ6x123KwU?start=15</p>'
            . '<p>Das ist das Ende!</p>';
        $filtered = $filter->filter($youtube);
        $this->assertEquals($expected, $filtered);

        // Iframe with youtube watch url.
        $youtube = '<p>YouTube - URL als <a href="xyz"> eingefügt<br>'
            . '<iframe width="560" height="315" src="https://www.youtube.com/watch?v=qcQ6x123KwU&start=15"'
            . ' frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope;'
            . ' picture-in-picture" allowfullscreen></iframe></p>'
            . '<p>Das ist das Ende!</p>';
        $filtered = $filter->filter($youtube);
        $this->assertEquals($expected, $filtered);

        // Iframe with youtube watch url.
        $youtube = '<p>YouTube - URL als <a href="xyz"> eingefügt<br>'
            . '<iframe width="560" height="315" src="https://www.youtube-nocookie.com/watch?v=qcQ6x123KwU&start=15"'
            . ' frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope;'
            . ' picture-in-picture" allowfullscreen></iframe></p>'
            . '<p>Das ist das Ende!</p>';
        $filtered = $filter->filter($youtube);
        $this->assertEquals($expected, $filtered);

        // Expected for the next few assertions.
        $expected = '<p>YouTube - URL als <a href="xyz"> eingefügt<br><div class="mbsyoutube-responsive mbsyoutube-responsive-16by9'
            . ' mbsyoutube-wrapper mbsyoutube-twoclickwarning-wrapper" style=""><div class="mbsyoutube-twoclickwarning-boxtext" hidden="hidden">'
            . '<strong>Privacy Policy</strong><br />Once the video plays, personal <a href="https://policies.google.com/privacy"'
            . ' target="_blank" style="color:#e3e3e3 !important;">information</a>, such as the IP address, will be sent to YouTube.</div>'
            . '<div class="mbsyoutube-twoclickwarning-buttonbox" hidden="hidden"><input type="button" class="mbsyoutube-twoclickwarning-button"'
            . ' value="Start videos ✓" hidden="hidden" /></div><div class="mbsyoutube-status-wrapper" id="yt__statwrap__phpunit__qcQ6x123KwU"'
            . ' hidden="hidden"><img class="mbsyoutube-img-logo" src="https://www.example.com/moodle/theme/mebis/pix/mebis-logo.png" /><br>'
            . '<input id="yt__play__phpunit__qcQ6x123KwU" type="button" class="mbsyoutube-twoclickwarning-button mbsyoutube-yt-play"'
            . ' value="Resume video" /><input id="yt__restart__phpunit__qcQ6x123KwU" type="button" class="mbsyoutube-twoclickwarning-button'
            . ' mbsyoutube-yt-restart" value="Restart video" hidden="hidden" /></div><div id="yt__phpunit__qcQ6x123KwU" class="mbsyoutube-frame'
            . ' mbsyoutube-responsive-item mbsyoutube-ytiframe" allowfullscreen="allowfullscreen" data-extern="{&quot;wmode&quot;:&quot;transparent&quot;'
            . ',&quot;modestbranding&quot;:1,&quot;rel&quot;:0,&quot;showinfo&quot;:0,&quot;iv_load_policy&quot;:3,&quot;autohide&quot;'
            . ':1,&quot;enablejsapi&quot;:1,&quot;end&quot;:&quot;15&quot;}" crossorigin="anonymous"></div><div class="mbsyoutube-bar-overlay"'
            . ' id="yt__baroverlay__phpunit__qcQ6x123KwU"'
            . ' hidden="hidden"></div></div></p><p>Das ist das Ende!</p>';

        // Youtube short url with end parameter.
        $youtube = '<p>YouTube - URL als <a href="xyz"> eingefügt<br>'
            . 'https://youtu.be/qcQ6x123KwU?end=15</p>'
            . '<p>Das ist das Ende!</p>';
        $filtered = $filter->filter($youtube);
        $this->assertEquals($expected, $filtered);

        // Youtube short url with end parameter.
        $youtube = '<p>YouTube - URL als <a href="xyz"> eingefügt<br>'
            . 'https://youtu.be/qcQ6x123KwU?end=15</p>'
            . '<p>Das ist das Ende!</p>';
        $filtered = $filter->filter($youtube);
        $this->assertEquals($expected, $filtered);

        // Iframe with youtube watch url.
        $youtube = '<p>YouTube - URL als <a href="xyz"> eingefügt<br>'
            . '<iframe width="560" height="315" src="https://www.youtube.com/watch?v=qcQ6x123KwU&end=15"'
            . ' frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope;'
            . ' picture-in-picture" allowfullscreen></iframe></p>'
            . '<p>Das ist das Ende!</p>';
        $filtered = $filter->filter($youtube);
        $this->assertEquals($expected, $filtered);

        // Youtube-nocookie url as plain embed  text.
        $youtube = '<p>YouTube - URL als <a href="xyz"> eingefügt<br>'
            . 'https://www.youtube-nocookie.com/embed/qcQ6x123KwU?end=15</p>'
            . '<p>Das ist das Ende!</p>';
        $filtered = $filter->filter($youtube);
        $this->assertEquals($expected, $filtered);

        // Expected for the next few assertions.
        $expected = '<p>YouTube - URL als <a href="xyz"> eingefügt<br><div class="mbsyoutube-responsive mbsyoutube-responsive-16by9'
            . ' mbsyoutube-wrapper mbsyoutube-twoclickwarning-wrapper" style=""><div class="mbsyoutube-twoclickwarning-boxtext" hidden="hidden">'
            . '<strong>Privacy Policy</strong><br />Once the video plays, personal <a href="https://policies.google.com/privacy"'
            . ' target="_blank" style="color:#e3e3e3 !important;">information</a>, such as the IP address, will be sent to YouTube.</div>'
            . '<div class="mbsyoutube-twoclickwarning-buttonbox" hidden="hidden"><input type="button" class="mbsyoutube-twoclickwarning-button"'
            . ' value="Start videos ✓" hidden="hidden" /></div><div class="mbsyoutube-status-wrapper" id="yt__statwrap__phpunit__qcQ6x123KwU"'
            . ' hidden="hidden"><img class="mbsyoutube-img-logo" src="https://www.example.com/moodle/theme/mebis/pix/mebis-logo.png" /><br>'
            . '<input id="yt__play__phpunit__qcQ6x123KwU" type="button" class="mbsyoutube-twoclickwarning-button mbsyoutube-yt-play"'
            . ' value="Resume video" /><input id="yt__restart__phpunit__qcQ6x123KwU" type="button" class="mbsyoutube-twoclickwarning-button'
            . ' mbsyoutube-yt-restart" value="Restart video" hidden="hidden" /></div><div id="yt__phpunit__qcQ6x123KwU" class="mbsyoutube-frame'
            . ' mbsyoutube-responsive-item mbsyoutube-ytiframe" allowfullscreen="allowfullscreen" data-extern="{&quot;wmode&quot;:&quot;transparent&quot;'
            . ',&quot;modestbranding&quot;:1,&quot;rel&quot;:0,&quot;showinfo&quot;:0,&quot;iv_load_policy&quot;:3,&quot;autohide&quot;'
            . ':1,&quot;enablejsapi&quot;:1,&quot;start&quot;:&quot;5&quot;,&quot;end&quot;:&quot;15&quot;}" crossorigin="anonymous">'
            . '</div><div class="mbsyoutube-bar-overlay" id="yt__baroverlay__phpunit__qcQ6x123KwU"'
            . ' hidden="hidden"></div></div></p><p>Das ist das Ende!</p>';

        // Youtube short url with end parameter.
        $youtube = '<p>YouTube - URL als <a href="xyz"> eingefügt<br>'
            . 'https://youtu.be/qcQ6x123KwU?start=5&end=15</p>'
            . '<p>Das ist das Ende!</p>';
        $filtered = $filter->filter($youtube);
        $this->assertEquals($expected, $filtered);

        // Iframe with youtube watch url.
        $youtube = '<p>YouTube - URL als <a href="xyz"> eingefügt<br>'
            . '<iframe width="560" height="315" src="https://www.youtube.com/watch?v=qcQ6x123KwU&start=5&end=15"'
            . ' frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope;'
            . ' picture-in-picture" allowfullscreen></iframe></p>'
            . '<p>Das ist das Ende!</p>';
        $filtered = $filter->filter($youtube);
        $this->assertEquals($expected, $filtered);
    }
}
