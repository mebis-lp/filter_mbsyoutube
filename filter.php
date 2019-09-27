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
     * Filter the text and replace links to youtube.com with an DSGVO coform style.
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

        $regexmediathek = '%<a[^>]?href=\"(https://mediathek.mebis.bayern.de/(index.php)?\?doc='
            . '(embeddedObject|provideVideo|playerExternal|embed)(.*?))\".*?</a>%is';
        $regexmbsembedyoutube = '/(((<a[^>]?href=")((http|ftp|https):\/\/){0,1}(\bwww\.youtube\b(\b\-nocookie\b)?\b\.com\b)(\/watch\?v=)([\w\d]+)'
            .'([\w@?^=%&\/~+#-]+)?"?[^<]+<\/a>)|((http|ftp|https):\/\/){0,1}(\bwww\.youtube\b(\b\-nocookie\b)?\b\.com\b)'
            .'(\/watch\?v=)([\w\d]+)([\w@?^=%&\/~+#-]+)?)/';
        $regexmbsembedyoutubeshorturl = '/(((<a[^>]?href=")(http|https):\/\/youtu.be\/([\w.,@?\^=%&:\/~+#\-]+)"?[^<]+<\/a>)'
            .'|((http|https):\/\/youtu.be\/([\w.,@?\^=%&:\/~+#\-]+)))/';
        $regexmbsembedyoutubefallback = '/(\bwww.youtube.com\/\b)/';

        $patternsandcallbacks = [
            $regexmbsembedyoutube => "filter_mbsembed::filter_mbsembed_youtube_callback",
            $regexmbsembedyoutubeshorturl => "filter_mbsembed::filter_mbsembed_youtube_shorturl_callback",
            $regexmbsembedyoutubefallback => "filter_mbsembed::filter_mbsembed_youtube_fallback_callback",
            $regexmediathek => "filter_mbsembed::filter_mbsembed_callback"
        ];

        $newtext = preg_replace_callback_array (
            $patternsandcallbacks,
            $text,
            -1,
            $count
        );

        if($count > 0) {
        //     echo "<script>'use strict';

        //     function onetime(node, type, callback) {

        //         // create event
        //         node.addEventListener(type, function(e) {
        //             // remove event
        //             e.target.removeEventListener(e.type, arguments.callee);
        //             // call handler
        //             return callback(e);
        //         });
            
        //     }

        //     onetime(document.getElementsByClassName('mbstest'), 'click', handler);

        //     // handler function
        //     function handler(e) {
        //         alert('You will only see this once!');
        //     }

        //     console.log('HALLO');
        //     document.addEventListener('DOMContentLoaded', function() {
        //         // Activate only if not already activated
        //         if (window.hideYTActivated) return;
        //         // Activate on all players
        //         let onYouTubeIframeAPIReadyCallbacks = [];
        //         for (let playerWrap of document.querySelectorAll('.mbsembed-twoclickwarning-wrapper')) {
        //             let playerFrame = playerWrap.querySelector('iframe');
                    
        //             let tag = document.createElement('script');
        //             tag.src = 'https://www.youtube.com/iframe_api';
        //             let firstScriptTag = document.getElementsByTagName('script')[0];
        //             firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
        //             console.log(tag);
        //             let onPlayerStateChange = function(event) {
        //                 if (event.data == YT.PlayerState.ENDED) {
        //                     playerWrap.classList.add('ended');
        //                 } else if (event.data == YT.PlayerState.PAUSED) {
        //                     playerWrap.classList.add('paused');
        //                 } else if (event.data == YT.PlayerState.PLAYING) {
        //                     playerWrap.classList.remove('ended');
        //                     playerWrap.classList.remove('paused');
        //                 }
        //             };
                    
        //             let player;
        //             onYouTubeIframeAPIReadyCallbacks.push(function() {
        //                 player = new YT.Player(playerFrame, {
        //                     events: {
        //                         'onStateChange': onPlayerStateChange
        //                     }
        //                 });
        //             });
                  
        //             playerWrap.addEventListener('click', function() {
        //                 let playerState = player.getPlayerState();
        //                 if (playerState == YT.PlayerState.ENDED) {
        //                     player.seekTo(0);
        //                 } else if (playerState == YT.PlayerState.PAUSED) {
        //                     player.playVideo();
        //                 }
        //             });
        //         }
                
        //         window.onYouTubeIframeAPIReady = function() {
        //             for (let callback of onYouTubeIframeAPIReadyCallbacks) {
        //                 callback();
        //             }
        //         };
                
        //         window.hideYTActivated = true;
        //     });</script>";
         }
        // print_object($newtext);
        return $newtext;
    }

    /**
     * Callback to set a YouTube url to match DSGVO.
     *
     * @param array $match
     * @return string Url
     */
    protected function filter_mbsembed_youtube_callback($match) {

        if(is_string($match[9])){
            $vid = $match[9];
        } else if(is_string($match[16])) {
            $vid = $match[16];
        }

        $iframe = filter_mbsembed::filter_mbsembed_create_two_click_version_youtube("https://www.youtube-nocookie.com/embed/".$vid."?modestbranding=1&rel=0&showinfo=0&iv_load_policy=3&autohide=1");
        return $iframe;
    }

    /**
     * Callback to set a YouTube url to match DSGVO from a shorten url.
     *
     * @param array $match
     * @return string $ytwrapper YouTube Video wrapper element.
     */ 
    protected function filter_mbsembed_youtube_shorturl_callback($match) {

        if(is_string($match[5])){
            $vid = $match[5];
        } else if(is_string($match[8])) {
            $vid = $match[8];
        }

        $ytwrapper = filter_mbsembed::filter_mbsembed_create_two_click_version_youtube("https://www.youtube-nocookie.com/embed/".$vid."?modestbranding=1&rel=0&showinfo=0&iv_load_policy=3&autohide=1");
        return $ytwrapper;
    }

    /**
     * Callback to set a YouTube url to match DSGVO from a shorten url.
     *
     * @return string Url
     */
    protected function filter_mbsembed_youtube_fallback_callback() {
        return "www.youtube-nocookie.com/";
    }

    /**
     * Generates the two click behaviour from a youtube url.
     *
     * @param string $videourl
     * @return string HTML markup
     */
    private function filter_mbsembed_create_two_click_version_youtube($videourl) {
        $iframeparams = [
            'class' => 'mbsembed-frame mbsembed-responsive-item',
            'src' => '',
            'allowfullscreen' => 'allowfullscreen',
            'hidden' => 'hidden',
            'data-extern' => $videourl
        ];
        $iframe = html_writer::tag('iframe', '', $iframeparams);
       
        $divtag1param = [
            'class' => 'mbsembed-twoclickwarning-boxtext'
        ];
        $divtag1 = html_writer::tag('div', get_string('mbstwoclickboxtext', 'filter_mbsembed'), $divtag1param);

        $inputtagparam = [
            'type' => 'button',
            'class' => 'mbsembed-twoclickwarning-button mbstest',
            'onclick' => 'this.parentElement.parentElement.children[0].setAttribute("hidden", true);
            this.parentElement.parentElement.children[1].setAttribute("hidden", true);
            this.parentElement.parentElement.children[2].setAttribute("src", this.parentElement.parentElement.children[2].getAttribute("data-extern"));
            this.parentElement.parentElement.children[2].removeAttribute("hidden");',
            'value' => get_string('mbswatchvideo', 'filter_mbsembed')
        ];
        $inputtag = html_writer::empty_tag('input', $inputtagparam);

        $divtag2param = [
            'class' => 'mbsembed-twoclickwarning-buttonbox'
        ];
        $divtag2 = html_writer::tag('div', $inputtag, $divtag2param);

        $wrappertag = html_writer::tag('div',$divtag1.$divtag2.$iframe,['class' => 'mbsembed-responsive mbsembed-responsive-16by9 mbsembed-wrapper mbsembed-twoclickwarning-wrapper']);

        return $wrappertag;
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
