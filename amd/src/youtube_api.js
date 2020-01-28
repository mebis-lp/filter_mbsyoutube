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
 * Javascript controller YouTube Videos.
 *
 * @module     filter_mbsyoutube/youtubevideos
 * @package    filter_mbsyoutube
 * @copyright  2019 Peter Mayer, ISB Bayern, peter.mayer@isb.bayern.de
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery'], function ($) {

    return {
        init: function (args) {

            var videos = {};
            $('.mbsyoutube-ytiframe').each(function () {
                var playerid = this.id.split("__");
                var videoid = playerid[2];
                var videouniqid = playerid[1];
                videos = getJsonObjectFromIdAttribut(videoid, 'data-extern', videouniqid);
            });

            $(document).ready(function () {
                loadPlayer();
            });

            /**
             * Sets the YouTube API to Dome and initiats the players
             */
            function loadPlayer() {
                if (typeof (YT) == 'undefined' || typeof (YT.Player) == 'undefined') {

                    var tag = document.createElement('script');
                    tag.src = "https://www.youtube.com/iframe_api";
                    var firstScriptTag = document.getElementsByTagName('script')[0];
                    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

                    window.onYouTubePlayerAPIReady = function () {
                        $.each(videos, function (index, item) {
                            onYouTubePlayer(item['videoid'], item['ytparam'], index);
                        });
                    };

                } else {

                    $.each(videos, function (index, item) {
                        onYouTubePlayer(item['videoid'], item['ytparam'], index);
                    });

                }
            }

            var player = {};

            /**
             * Initiates one YouTube player
             * @param string videoid
             * @param array ytparam
             */
            function onYouTubePlayer(videoid, ytparam, uniqeid) {
                player[uniqeid] = new YT.Player('yt__' + uniqeid + '__' + videoid, {
                    videoId: videoid,
                    playerVars: ytparam,
                    events: {
                        'onStateChange': onPlayerStateChange,
                        'onError': catchError
                    }
                });

                // Adds eventlistener to transparent layer to start/stop vdideo.
                $('#yt__baroverlay__' + uniqeid + "__" + videoid).click(function () {
                    var playerid = this.id.split("__");
                    var videouniqid = playerid[2];
                    var state = player[videouniqid].getPlayerState();
                    if (state == YT.PlayerState.PLAYING) {
                        player[videouniqid].pauseVideo();
                    } else {
                        player[videouniqid].playVideo();
                    }
                });
            }

            /**
             * Callback Player State event listener.
             * @param object event
             */
            function onPlayerStateChange(event) {

                var frameid = event.target.a.id;
                var ids = frameid.split("__");
                var videoid = ids[2];
                var uniqeid = ids[1];

                $('#yt__statwrap__' + uniqeid + '__' + videoid).removeAttr('hidden');
                $('#yt__restart__' + uniqeid + '__' + videoid).removeAttr('hidden');
                $('#yt__restart__' + uniqeid + '__' + videoid).hide();
                $('#yt__baroverlay__' + uniqeid + '__' + videoid).removeAttr('hidden');
                $('#yt__baroverlay__' + uniqeid + '__' + videoid).hide();

                if (event.data == YT.PlayerState.PLAYING) {
                    videos = getJsonObjectFromIdAttribut(videoid, 'data-extern', uniqeid);
                    args = videos[uniqeid]['ytparam'];
                    if (args['end'] == '' || args['end'] == 0) {
                        delete args['end'];
                    }
                    if (player[uniqeid].getCurrentTime() > args['end'] || player[uniqeid].getCurrentTime() < args['start']) {
                        player[uniqeid].loadVideoById({
                            videoId: videoid,
                            startSeconds: args['start'],
                            endSeconds: args['end']
                        });
                    }
                    $('#' + frameid).show();
                } else if (event.data == YT.PlayerState.ENDED) {
                    $('#yt__play__' + uniqeid + '__' + videoid).hide();
                    $('#yt__restart__' + uniqeid + '__' + videoid).show();
                    $('#yt__statwrap__' + uniqeid + '__' + videoid).show();
                    $('#yt__baroverlay__' + uniqeid + '__' + videoid).hide();
                    $('#' + frameid).hide();
                } else if (event.data == YT.PlayerState.PAUSED) {
                    $('#yt__baroverlay__' + uniqeid + '__' + videoid).show();
                    $('#yt__restart__' + uniqeid + '__' + videoid).hide();
                    $('#yt__statwrap__' + uniqeid + '__' + videoid).show();
                }
            }

            /**
             * Catches Errors.
             * @param object event
             */
            function catchError(event) {
                if (event.data == 100) {
                    console.log("Error - The video is not accessable!");
                }
            }

            /**
             * Makes a json object from an data-attribut value of a tag.
             * @param string videoid
             * @param string attribut
             */
            function getJsonObjectFromIdAttribut(videoid, attribut, uniqid) {
                var jsonobj = $.parseJSON($('#yt__' + uniqid + '__' + videoid).attr(attribut));
                videos[uniqid] = {};
                videos[uniqid]['ytparam'] = {};
                videos[uniqid]['videoid'] = videoid;
                $.each(jsonobj, function (index, value) {
                    videos[uniqid]['ytparam'][index] = value;
                });
                return videos;
            }

            // OnClick event for start playing.
            $(".mbsyoutube-yt-play").click(function (e) {
                var buttonid = e.target.id;
                var buttonarr = buttonid.split("__");
                var uniqid = buttonarr[2];
                var videoid = buttonarr[3];
                $('#yt__' + uniqid + '__' + videoid).delay(200).fadeIn(400);
                player[uniqid].playVideo();
            });

            // OnClick event for restart video after endded.
            $(".mbsyoutube-yt-restart").click(function (e) {
                var buttonid = e.target.id;
                var buttonarr = buttonid.split("__");
                var uniqid = buttonarr[2];
                var videoid = buttonarr[3];
                $('#yt__' + uniqid + '__' + videoid).delay(200).fadeIn(400);

                videos = getJsonObjectFromIdAttribut(videoid, 'data-extern', uniqid);
                args = videos[uniqid]['ytparam'];

                player[uniqid].loadVideoById({
                    videoId: videoid,
                    startSeconds: args['start'],
                    endSeconds: args['end']
                });
            });
        }
    };
});