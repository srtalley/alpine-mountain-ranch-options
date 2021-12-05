//version: 1.3.3

jQuery(function($) {
    $(document).ready(function(){

        // Check for the AMR fixed header
        var amr_fixed_header = $('.amr-fixed-header');

        if(amr_fixed_header.length) {
            $(window).scroll(function () {
                if ($(window).scrollTop() > 50) { 
                    $('body').addClass('shrink-fixed-header');
                }
                else{
                    $('body').removeClass('shrink-fixed-header');
                }
            });
        }

        var is_Safari = isSafari();
        if(!is_Safari) {
             /**
             * Open Divi video modules for YouTube videos with overlays
             * in a lightbox
             */
            $('.video-lightbox .et_pb_video_overlay').unbind('click');
            var etVideos = $('.video-lightbox .et_pb_video_overlay');
            
            $.each(etVideos, function() {
                var videoFrames = $(this).parent().find('.et_pb_video_box iframe');
                var videoPaths = videoFrames.attr('src').replace('/embed/', '/watch?v=');
                videoPaths = videoPaths.replace('?feature=oembed', '');
                $(this).magnificPopup({
                    items: {
                        src: videoPaths,
                    },
                    type:'iframe',
                    mainClass: 'amr-video-lightbox',
                    iframe: {
                        markup: '<div class="mfp-iframe-scaler">'+
                        '<div class="mfp-close"></div>'+
                        '<iframe id="player" class="mfp-iframe" frameborder="0" allow="autoplay"></iframe>'+
                        '</div>', // HTML markup of popup, `mfp-close` will be replaced by the close button
                        patterns: {
                            youtube: {
                                index: 'youtube.com/',
                                id: 'v=',
                                src: 'https://www.youtube.com/embed/%id%?autoplay=1'
                            }
                        },
                    }
                });
            });
            /**
             * Open links to YouTube videos in a lightbox
             */
            $('.video-lightbox a').magnificPopup({
                type:'iframe',
                mainClass: 'amr-video-lightbox',
                iframe: {
                    markup: '<div class="mfp-iframe-scaler">'+
                    '<div class="mfp-close"></div>'+
                    '<iframe id="player" class="mfp-iframe" frameborder="0" allow="autoplay"></iframe>'+
                    '</div>', // HTML markup of popup, `mfp-close` will be replaced by the close button
                    patterns: {
                        youtube: {
                            index: 'youtube.com/',
                            id: 'v=',
                            src: 'https://www.youtube.com/embed/%id%?autoplay=1'
                        }
                    },
                }

            });
            /**
             * Open Divi video modules for MP4 videos with overlays
             * in a lightbox
             */
            $('.mp4-video-lightbox .et_pb_video_overlay').unbind('click');
            var etMP4Videos = $('.mp4-video-lightbox .et_pb_video_overlay');
            
            $.each(etMP4Videos, function() {
                var videoObject = $(this).parent().find('.et_pb_video_box video source');
                var videoPaths = videoObject.attr('src');

                $(this).magnificPopup({
                    items: {
                        src: videoPaths,
                    },
                    type:'iframe',
                    mainClass: 'amr-video-lightbox',
                    iframe: {
                        markup: '<div class="mfp-iframe-scaler">'+
                        '<div class="mfp-close"></div>'+
                        '<iframe id="player" class="mfp-iframe" frameborder="0" allow="autoplay"></iframe>'+
                        '</div>', // HTML markup of popup, `mfp-close` will be replaced by the close button
                    }
                });
            });
            /**
             * Open links to MP4 videos in a lightbox
             */
            $('.mp4-video-lightbox a, a.mp4-video-lightbox').magnificPopup({
                type:'iframe',
                mainClass: 'amr-video-lightbox',
                iframe: {
                    markup: '<div class="mfp-iframe-scaler">'+
                    '<div class="mfp-close"></div>'+
                    '<iframe id="player" class="mfp-iframe" frameborder="0" allow="autoplay"></iframe>'+
                    '</div>', // HTML markup of popup, `mfp-close` will be replaced by the close button
                    srcAction: 'iframe_src',
                }

            });

        } // if not safari
      
    
    }); // end document ready
    /**
     * Detect Safari
     */
    // function isIOS() {
    //     const iPad = !!(navigator.userAgent.match(/(iPad)/) || (navigator.platform === "MacIntel" && typeof navigator.standalone !== "undefined"));
    //     const iPhone = !!(navigator.userAgent.match(/(iPhone)/) || (navigator.platform === "MacIntel" && typeof navigator.standalone !== "undefined"));

    //     if (iPad || iPhone) {
    //         return true;
    //     } else {
    //         return false;
    //     }
    // }
    function isSafari() {
        var isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
        return isSafari;
    }
});
  