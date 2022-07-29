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
      
        //check if an anchor was clicked and scroll to the proper place
        $('a[href*=\\#]').on('click', function () {
            if(this.hash != '') {
                if(this.pathname === window.location.pathname){
                    // smooth_scroll_to_anchor_top($(this.hash));
                } 
            }
        });
    }); // end document ready


    $(window).load(function(){
        //Scrolling animation for anchor tags
        if(window.location.hash) {
        //   smooth_scroll_to_anchor_top($(window.location.hash));
        }
        setTimeout(function() {
        //   setup_collapsible_submenus();
        }, 700);
    
    }); // end window load 

    // scroll to the top of the anchor with an offset on desktops
    function smooth_scroll_to_anchor_top(anchor){
        if($(anchor) != 'undefined' ) {
        var window_media_query_980 = window.matchMedia("(max-width: 980px)")
        if(window_media_query_980.matches) {
            var offset_amount = 0;
        } else {

            var top_header_height = parseInt($('#top-header').outerHeight(true)) || 0;
            var main_header_height = parseInt($('#main-header').outerHeight(true)) || 0;
            var admin_bar_height = parseInt($('#wpadminbar').outerHeight(true)) || 0;
            var owner_portal_sticky_bar_height = parseInt($('#owners-portal-menu.et_pb_sticky').outerHeight(true)) || 0;

            var offset_amount = top_header_height + main_header_height + admin_bar_height + owner_portal_sticky_bar_height;
        }

        $('html,body').animate({scrollTop:($(anchor).offset().top - offset_amount) + 'px'}, 1000);
        }
    } // end function

    function setup_collapsible_submenus() {
        // mobile menu
        $('#mobile_menu .menu-item-has-children > a').after('<span class="menu-closed"></span>');
        $('#mobile_menu .menu-item-has-children > a').each(function() {
            $(this).next().next('.sub-menu').toggleClass('hide',1000);
        });
        $('#mobile_menu .menu-item-has-children > a + span').on('click', function(event) {
            event.preventDefault();
            $(this).toggleClass('menu-open');
            $(this).next('.sub-menu').toggleClass('hide',1000);
        });
    }

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
  