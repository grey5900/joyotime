<?php 
$this->Voice->init($voice);
?>
<script type="text/javascript">
$(document).ready(function(){
    function browserWidth () {
        var _width = $(window).width();
        var widthVoice,heightVoice;
        if (_width < 768) {
            widthVoice = _width+'px';
            heightVoice =_width+'px';
//            $('.voice-top-logo img').attr('src','/img/logo.phone.voice2.0.png');
        } else {
            widthVoice = '316px';
            heightVoice = '316px';
        }
        
        var Playlist = new jPlayerPlaylist({
            jPlayer: "#jquery_jplayer",
            cssSelectorAncestor: "#jp_container"
        }, 
        [{
            title:"<?php echo $voice['title'] ?>",
            artist:"<i class='ico-star-<?php echo ceil($voice['score']); ?>'></i>",
            mp3:"<?php echo $this->Voice->shareHash($hash) ? $voice['voice'] : $voice['trial_voice'] ?>",
            poster: "<?php echo $voice['cover']['x640'] ?>"
        }],
        {
            swfPath: "/js",
            supplied: "webmv, ogv, m4v, oga, mp3",
            smoothPlayBar: true,
            size: {
                width: widthVoice,
                height: heightVoice,
                cssClass: "jp-video-box"
            },
            keyEnabled: true,
            audioFullScreen: true
        });
    }
    browserWidth();


    function playerResize(){
        if ($(window).width() < 768) {
            $('.voice-content .jp-jplayer').width($(window).width());
            $('.voice-content .jp-jplayer').height($(window).width());
            $('.voice-content #jp_poster_0').width($(window).width());
            $('.voice-content #jp_poster_0').height($(window).width());
            $('.voice-content .jp-controls-holder').css('top','-'+($(window).width()+80)/2+'px');
        }else{
            $('.voice-content .jp-jplayer').width(316);
            $('.voice-content .jp-jplayer').height(316);
            $('.voice-content #jp_poster_0').width(316);
            $('.voice-content #jp_poster_0').height(316);
            $('.voice-content .jp-controls-holder').css('top','-'+(316+80)/2+'px');
        }
    }
    $(window).resize(function() {
        playerResize();
    });
    playerResize();
    var t;
    function timedCount()
    {
        if ( $(".jp-current-time").length > 0 ) {
            $('.paly-time-now').html($(".jp-current-time").html());
        }
        if ( $(".jp-duration").length > 0 ) {
            $('.paly-time-count').html($(".jp-duration").html());
        }
    }
    setInterval(timedCount,1000);
});
</script>