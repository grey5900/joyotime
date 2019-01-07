<script type="text/javascript">
$(document).ready(function(){
    function browserWidth () {
        var _width = $(window).width();
        var widthVoice,heightVoice;
        if (_width < 992) {
            widthVoice = '290px',
            heightVoice = '290px';
        } else {
            widthVoice = '480px',
            heightVoice = '480px';
        }
        
        var Playlist = new jPlayerPlaylist({
            jPlayer: "#jquery_jplayer",
            cssSelectorAncestor: "#jp_container"
        }, 
        [{
            title:"<?php echo $voice['title'] ?>",
            artist:"<i class='ico-star-<?php echo ceil($voice['score']); ?>'></i>",
            mp3:"<?php echo $voice['trial_voice']?>",
            poster: "<?php echo $voice['cover']['x640'] ?>"
        }], 
        {
            swfPath: "/js",
            supplied: "webmv, ogv, m4v, oga, mp3",
            smoothPlayBar: true,
            size: {
                width: widthVoice,
                height: heightVoice,
                cssClass: "jp-video-270p"
            },
            keyEnabled: true,
            audioFullScreen: true
        });
    }
    browserWidth();
})
</script>