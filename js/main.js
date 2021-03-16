//version: 1.1.7

jQuery(function($) {
    $(document).ready(function(){

        $('.video-lightbox .et_pb_video_overlay').unbind('click');
        var etVideos = $('.video-lightbox .et_pb_video_overlay');
        
        $.each(etVideos, function() {
            var videoFrames = etVideos.parent().find('.et_pb_video_box iframe'),
            videoPaths = videoFrames.attr('src').replace('/embed/', '/watch?v=');
            videoPaths = videoPaths.replace('?feature=oembed', '');
            etVideos.magnificPopup({
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
    
    }); // end document ready
});
  