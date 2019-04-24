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

/**
 * Filter class mbsembed.
 *
 * @package    filter_mbsembed
 * @copyright  2017 Andreas Wagner, ISB Bayern
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_mbsembed extends moodle_text_filter {

    /**
     * Filter the text and replace the links to the mediathek with an
     * suitable iframe.
     *
     * Please note that we replace links NOT urls. If it should be possible to
     * convert a url, you have to filter the text with filter_urltolink before
     * applying this filter.
     *
     * @param string $text some HTML content
     * @param array $options options passed to the filters
     * @return string the HTML content after the filtering has been applied
     */
    public function filter($text, array $options = []) {

        if (!is_string($text) or empty($text)) {
            return $text;
        }

        if (stripos($text, '</a>') === false) {
            // Performance shortcut - if not </a> tag, nothing can match.
            return $text;
        }

        // Embed Mediathek item (embeddedObject), inclusive MZ-DVD (playerExternal) and Prüfungsarchiv (embed).
        $regex = "%<a[^>]?href=\"(https://mediathek.mebis.bayern.de/(index.php)?\?doc="
                . "(embeddedObject|provideVideo|playerExternal|embed)(.*?))\".*?</a>%is";
        $newtext = preg_replace_callback($regex, array(&$this, 'filter_mbsembed_callback'), $text);

        return $newtext;
    }

    /**
     * Callback to embed a Mediathek iframe.
     *
     * @param array $match
     * @return string HTML fragment
     */
    protected function filter_mbsembed_callback($match) {
        global $USER;

        $link = htmlspecialchars_decode($match[1]);
        $paramdoc = $match[3];
        $mediasiteurl = ''; // URL to Mediathek site, e.g. https://mediathek.mebis.bayern.de/?doc=record&identifier=BWS-04985575.
        $mediathekurl = ''; // Mediaplayer-URL, e. g. https://mediathek.mebis.bayern.de/?doc=embeddedObject&id=BWS-04985575&type=video&start=0&title=Die%20Roboter%0kommen.

        // Parse url params.
        $urlparams = parse_url($link, PHP_URL_QUERY);
        $paramsarray = explode("&", $urlparams);
        $paramskeyedarray = [];
        foreach ($paramsarray as $param) { // Each parameter.
            $split = explode("=", $param); // Split in key and value.
            $paramskeyedarray[$split[0]] = $split[1];
        }

        switch ($paramdoc) {
            case 'embeddedObject':
                // Mediathek Mediaplayer-URL.
                $mediathekurl = $link;
                // Build Mediathek site URL.
                $paramskeyedarray['doc'] = str_replace('embeddedObject', 'record', $paramskeyedarray['doc']);
                $mediasiteurl = 'https://mediathek.mebis.bayern.de/?doc=' . urlencode($paramskeyedarray['doc']) . '&identifier=' .
                        urlencode($paramskeyedarray['id']);
                $mediasitelink = html_writer::link($mediasiteurl,
                        get_string('mediatheksitelink', 'filter_mbsembed'),
                        ['class' => 'internal', 'target' => '_blank', 'rel' => 'noopener noreferrer']);
                break;
            case 'provideVideo':
                // URL for H5p is given.
                // Build Mediathek site URL.
                $paramskeyedarray['doc'] = str_replace('provideVideo', 'record', $paramskeyedarray['doc']);
                $mediasiteurl = 'https://mediathek.mebis.bayern.de/?doc=' . urlencode($paramskeyedarray['doc']) . '&identifier=' .
                        urlencode($paramskeyedarray['identifier']);
                $mediasitelink = html_writer::link($mediasiteurl,
                        get_string('mediatheksitelink', 'filter_mbsembed'),
                        ['class' => 'internal', 'target' => '_blank', 'rel' => 'noopener noreferrer']);
                // Build Mediathek Mediaplayer-URL.
                $mediathekurl = str_replace('provideVideo', 'embeddedObject', $link);
                $mediathekurl = str_replace('identifier', 'id', $mediathekurl);
                break;
            case 'playerExternal':
                // URL for MZ-DVD is given.
                $mediathekurl = $link;
                // Build Mediathek site URL.
                $paramskeyedarray['doc'] = str_replace('playerExternal', 'record', $paramskeyedarray['doc']);
                $mediasiteurl = 'https://mediathek.mebis.bayern.de/?doc=' . urlencode($paramskeyedarray['doc']) . '&identifier=' .
                        urlencode($paramskeyedarray['identifier']);
                $mediasitelink = html_writer::link($mediasiteurl,
                        get_string('mediatheksitelink', 'filter_mbsembed'),
                        ['class' => 'internal', 'target' => '_blank', 'rel' => 'noopener noreferrer']);
                break;
            case 'embed':
                // Prüfungsarchiv-URL is given.
                $mediathekurl = $link . '&referrer=moodle&mode=display&user=' . urlencode($USER->username);
                $mediasiteurl = $mediathekurl;
                $mediasitelink = html_writer::link($mediasiteurl,
                        get_string('pruefungsarchivsitelink', 'filter_mbsembed'),
                        ['class' => 'internal', 'target' => '_blank', 'rel' => 'noopener noreferrer']);
                break;
            case 'default':
                return $match[0];
        }

        $iframeparams = [
            'class' => 'mbsembed-frame mbsembed-responsive-item',
            'src' => $mediathekurl,
            'allowfullscreen' => 'allowfullscreen'
        ];

        $iframe = html_writer::tag('iframe', '', $iframeparams);
        $iframediv = html_writer::tag('div', $iframe,
                ['class' => 'mbsembed-responsive mbsembed-responsive-16by9 mbsembed-wrapper']);
        $mediasitediv = html_writer::tag('div', $mediasitelink, ['class' => 'pull-right mbsembed-link']);
        return $iframediv.$mediasitediv;
    }

}
