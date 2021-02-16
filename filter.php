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
 * @package    filter_mbsyoutube
 * @copyright  2017 Andreas Wagner, 2019 Peter Mayer, ISB Bayern
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Filter class mbsyoutube.
 *
 * @package    filter_mbsyoutube
 * @copyright  2020 Peter Mayer, ISB Bayern
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_mbsyoutube extends moodle_text_filter {
    /**
     * @var array $youtubevideoids Array of all YouTube Video IDs of course page.
     */
    private $youtubevideoids = [];

    /**
     * @var int $courseid Course ID of the recent course.
     */
    private $courseid;

    /**
     * @var bool $hasuseraccepted Has a user consented to the transfer of data.
     */
    private $hasuseraccepted;

    /**
     * Filter the text and replace links to youtube.com with an DSGVO coform style.
     *
     * Please note that we replace links, urls AND iFrames. In order to support all
     * kinds of YouTube embedding.
     *
     * @param string $text some HTML content
     * @param array $options options passed to the filters
     * @return string the HTML content after the filtering has been applied
     */
    public function filter($text, array $options = []) {
        global $PAGE;

        if (!is_string($text) or empty($text)) {
            return $text;
        }

        // When adding a new regex command, there must be added a new if clause in the callback function, too.
        $regexyoutube = '/('
            . '((<video[^>]+><source[^>]?src=")(((http|ftp|https):\/\/){0,1}(\bwww\.youtube\b(\b\-nocookie\b)?\b\.com\b)'
            . '(\/watch\?v=)([\w\d\-]+)([\w@\?^=%&\/~+#\-;]+)?)'
            . '(">[^<]+<\/video>)?)'
            . '|((<a[^>]?href=")?(((http|ftp|https):\/\/){0,1}(\bwww\.youtube\b(\b\-nocookie\b)?\b\.com\b)(\/watch\?v=)'
            . '([\w\d\-]+)([\w@\?^=%&\/~+#\-;]+)?)("?[^<]+<\/a>)?)'
            . '|(<iframe(.*)src="((http|ftp|https):\/\/{0,1}(\bwww\.youtube\b(\b\-nocookie\b)?\b\.com\/embed\/\b)'
            . '([\w\d\-]+)([\w@\?^=%&\/~+#\-;]+)?)"(.*)>(.*)<\/iframe>)'
            . '|(<a[^>]?href=")?(((http|ftp|https):\/\/){0,1}(\bwww\.youtube\b(\b\-nocookie\b)?\b\.com\b)(\/embed\/)'
            . '([\w\d\-]+)([\w@\?^=%&\/~+#\-;]+)?("?[^<]+<\/a>)?)'
            . '|(<iframe(.*)src="((http|ftp|https):\/\/{0,1}(\bwww\.youtube\b(\b\-nocookie\b)?\b\.com\b)(\/watch\?v=)'
            . '([\w\d\-]+)([\w@\?^=%&\/~+#\-;]+)?)"(.*)>(.*)<\/iframe>)'
            . ')/';
        $regexyoutubeshorturl = '/('
            . '((<a[^>]?href=")((http|https):\/\/youtu.be\/([\w\d\-_]+)([\w@\?^=%&\/~+#\-]+)?)"?[^<]+<\/a>)'
            . '|((<video[^>]+><source[^>]?src=")((http|https):\/\/youtu.be\/([\w\d\-_]+)([\w@\?^=%&\/~+#\-]+)?)"?[^<]+<\/video>)'
            . '|((http|https):\/\/youtu.be\/([\w\d\-_]+)([\w@\?^=%&\/~+#\-]+)?)'
            . ')/';

        $patternsandcallbacks = [
            $regexyoutube => "filter_mbsyoutube::youtube_callback",
            $regexyoutubeshorturl => "filter_mbsyoutube::youtube_shorturl_callback",
        ];

        $newtext = preg_replace_callback_array(
            $patternsandcallbacks,
            $text,
            -1,
            $count
        );

        if (PHPUNIT_TEST) {
            return $newtext;
        }
        $youtubevideoids = $this->youtubevideoids;

        if (count($youtubevideoids) > 0) {

            if (!$this->get_hasuseraccepted()) {
                $params = ['courseid' => $this->get_courseid()];
                $PAGE->requires->js_call_amd('filter_mbsyoutube/sethasuseraccepted', 'init', [$params]);
            } else {
                $url = new moodle_url('https://www.youtube.com/iframe_api');
                $PAGE->requires->js($url, false);
                $PAGE->requires->js_call_amd('filter_mbsyoutube/youtube_api', 'init');
            }
        }
        return $newtext;
    }

    /**
     * Extracts the html attributes from string.
     * @param string $matchingtag
     * @return string $styles
     */
    protected function get_style_attributs($matchingtag) {

        if (PHPUNIT_TEST) {
            return '';
        }

        $styles = '';
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($matchingtag);
        libxml_clear_errors();

        if (isset($elem)) {
            $styles = $elem->getAttribute('style');
            if (strlen($styles)) {
                $styles = $styles . ' padding:0; margin: 5px;';
            }
        }
        return $styles;
    }


    /**
     * Get the courseid from context
     *
     * @return int $courseid
     */
    protected function get_courseid() {
        if (isset($this->courseid)) {
            return $this->courseid;
        }

        list($context, $course, $cm) = get_context_info_array($this->context->id);

        // Set course id to a default value in case of no courseid exists. E.g. in system context.
        if (!isset($course->id)) {
            $course = new \stdClass();
            $course->id = 0;
        }

        $this->courseid = $course->id;
        return $this->courseid;
    }

    /**
     * Get hasuseraccepted from cache
     *
     * @return bool $hasuseraccepted
     */
    protected function get_hasuseraccepted() {
        global $USER;

        if (PHPUNIT_TEST) {
            $this->hasuseraccepted = true;
            return $this->hasuseraccepted;
        }

        if (isset($this->hasuseraccepted)) {
            return $this->hasuseraccepted;
        }

        $courseid = $this->get_courseid();
        $cache = \cache::make('filter_mbsyoutube', 'mbsexternalsourceaccept');
        $this->hasuseraccepted = $cache->get($USER->id . "_" . $courseid . "_YouTube");

        return $this->hasuseraccepted;
    }

    /**
     * Callback to set a YouTube url to match DSGVO.
     *
     * @param array $match
     * @return string Url
     */
    protected function youtube_callback($match) {
        $hasuseraccepted = $this->get_hasuseraccepted();
        $styles = $this->get_style_attributs($match[1]);

        // The following arrays containing all possible offset of the $match array.
        $videoffsets = [10, 21, 30, 41, 51];
        $urloffsets = [4, 15, 26, 35, 46];

        foreach ($videoffsets as $videoffset) {
            if (isset($match[$videoffset])) {
                $vid = $match[$videoffset];
            }
        }
        foreach ($urloffsets as $urloffset) {
            if (isset($match[$urloffset])) {
                $params = parse_url($match[$urloffset]);
            }
        }

        $urlparam = self::build_url_querystring($params);
        array_push($this->youtubevideoids, $vid);
        $iframe = $this->render_two_click_version_youtube($vid, $hasuseraccepted, $urlparam['paramarr'], $styles);

        return $iframe;
    }

    /**
     * Callback to set a YouTube url to match DSGVO from a shorten url.
     *
     * @param array $match
     * @return string $ytwrapper YouTube Video wrapper element.
     */
    protected function youtube_shorturl_callback($match) {

        $hasuseraccepted = $this->get_hasuseraccepted();

        $styles = $this->get_style_attributs($match[1]);

        // The order of the if clauses has to be the same as the order of the regex commands.
        if (is_string($match[6]) && strlen($match[6]) > 1) {
            $vid = $match[6];
            $params = parse_url($match[4]);
        } else if (is_string($match[12]) && strlen($match[12]) > 1) {
            $vid = $match[12];
            $params = parse_url($match[10]);
        } else if (is_string($match[16]) && strlen($match[16]) > 1) {
            $vid = $match[16];
            $params = parse_url($match[14]);
        }
        $urlparam = self::build_url_querystring($params);

        array_push($this->youtubevideoids, $vid);

        $ytwrapper = $this->render_two_click_version_youtube($vid, $hasuseraccepted, $urlparam['paramarr'], $styles);
        return $ytwrapper;
    }

    /**
     * Generates the two click behaviour from a youtube url.
     *
     * @param string $videoid
     * @param bool $hasuseraccepted True if provider (YouTube) is accepted
     * @param array $urlparam Contains all parameters to post to iframe.
     * @param string $styles
     * @return string HTML markup
     */
    private function render_two_click_version_youtube($videoid, $hasuseraccepted = false, $urlparam = [], $styles = '') {
        global $OUTPUT;

        $data = new stdClass();
        $data->mbswrapperstyles = $styles;
        $data->videoid = $videoid;

        if (PHPUNIT_TEST) {
            $data->uniqid = 'phpunit';
        } else {
            $data->uniqid = uniqid();
        }

        $data->popupnurl = new moodle_url('/filter/mbsyoutube/video_popup.php', ['vid' => $videoid]);
        $data->mbsplayerdetails = json_encode($urlparam);
        $data->mbstwoclickboxtext = get_string('mbstwoclickboxtext', 'filter_mbsyoutube');
        $data->mbsopenpopup = get_string('mbsopenpopup', 'filter_mbsyoutube');
        $data->mbswatchvideo = get_string('mbswatchvideo', 'filter_mbsyoutube');
        $data->mbsresumevideobtn = get_string('mbsresumevideobtn', 'filter_mbsyoutube');
        $data->mbsrestartvideobtn = get_string('mbsrestartvideobtn', 'filter_mbsyoutube');
        $data->mebislogourl = new moodle_url('/theme/mebis/pix/mebis-logo.png');

        if ($hasuseraccepted) {
            $data->optionacceptedhidden = ' hidden="hidden"';
            $data->optionnotacceptedhidden = "";
        } else {
            $data->optionacceptedhidden = "";
            $data->optionnotacceptedhidden = ' hidden="hidden"';
        }

        return $OUTPUT->render_from_template('filter_mbsyoutube/mbsyoutubetwoclick', $data);
    }

    /**
     * Gets all URL query parameters and returns allowed parameters as url string.
     *
     * @param array $params
     * @return string URL parameters string
     */
    private static function build_url_querystring($params) {

        $preconfparam = [
            'wmode' => 'transparent',  // Has to be on first place.
            'modestbranding' => 1,
            'rel' => 0,
            'showinfo' => 0,
            'iv_load_policy' => 3,
            'autohide' => 1,
            'enablejsapi' => 1
        ];

        if (isset($params['query'])) {
            $query = html_entity_decode($params['query']);
            $urlparams = [];
            parse_str($query, $urlparams);
            $allowedparams = ['start', 'end', 't'];
            $urlparams = array_intersect_key($urlparams, array_flip($allowedparams));
            $urlparams = array_merge($preconfparam, $urlparams);
            if (isset($urlparams['t'])) {
                $urlparams['start'] = $urlparams['t'];
                unset($urlparams['t']);
            }
            $urlparamret['paramstr'] = "?" . http_build_query($urlparams, '', "&");
            $urlparamret['paramarr'] = $urlparams;
        } else {
            $urlparamret['paramstr'] = "";
            $urlparamret['paramarr'] = $preconfparam;
        }
        return $urlparamret;
    }
}