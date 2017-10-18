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
 * Filter class
 *
 * @package    filter_mbsembed
 * @copyright  2017 Andreas Wagner, ISB Bayern
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

class filter_mbsembed extends moodle_text_filter {

    /**
     * Filter the text and replace the links to the mediathek with an
     * suitable iframe.
     *
     * Please note that we replace links NOT urls. If it should be possible to
     * convert a url, you have to filter the text with filter_urltolink before
     * applying this filter.
     *
     * @param string $text
     * @param array $options
     * @return string
     */
    public function filter($text, array $options = array()) {

        if (!is_string($text) or empty($text)) {
            return $text;
        }

        if (stripos($text, '</a>') === false) {
            // Performance shortcut - if not </a> tag, nothing can match.
            return $text;
        }

        // Check, whether user has embedded the page url from mediathek.
        $regex = "%<a.*?href=\"(https://mediathek.mebis.bayern.de/(index.php)*?\?doc=record(.*?))\".*?</a>%is";
        $text = preg_replace_callback($regex, array(&$this, 'fix_page_url_callback'), $text);

        // Embed mediathek item.
        $regex = "%<a.*?href=\"(https://mediathek.mebis.bayern.de/\?doc=embeddedObject(.*?))\".*?</a>%is";
        $newtext = preg_replace_callback($regex, array(&$this, 'filter_mbsembed_callback'), $text);

        return $newtext;
    }

    /**
     * Build the correct url, if user has choosen the page url of item from the mediathek.
     *
     * @param array $match
     */
    protected function fix_page_url_callback($match) {

        $mediatheklink = $match[0];
        $mediatheklink = str_replace('index.php', '', $mediatheklink);
        $mediatheklink = str_replace('record', 'embeddedObject', $mediatheklink);
        $mediatheklink = str_replace('identifier', 'id', $mediatheklink);

        return $mediatheklink;
    }

    /**
     * Callback to embed an iframe.
     *
     * @param array $match
     */
    protected function filter_mbsembed_callback($match) {

        $iframeparams = [
            'class' => 'mbsembed-frame mbsembed-responsive-item',
            'src' => $match[1],
            'allowfullscreen' => 'allowfullscreen'
        ];

        $iframe = html_writer::tag('iframe', '', $iframeparams);
        return html_writer::tag('div', $iframe, ['class' => 'mbsembed-responsive mbsembed-responsive-4by3 mbsembed-wrapper']);
    }

}
