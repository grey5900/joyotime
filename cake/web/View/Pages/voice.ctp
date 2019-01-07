<div class="fs-voice-2 clearfix">
    <div class="voice-top">

        <div class="voice-top-left">
            <a href="http://staging.www.fishsaying.com" class="voice-top-logo <?php echo __('English')=='English'?'chn':'eng'; ?>">
                <img class="pc" src="/img/logo.voice2.0-<?php echo __('English')=='English'?'chn':'eng'; ?>.png" alt="<?php echo __('鱼说'); ?>"/>
                <div class="phone">
                    <img class="phone" src="/img/logo.icon.png" alt="<?php echo __('鱼说'); ?>"/>
                    <div>
                        <h1><?php echo __('鱼说'); ?></h1>
                        <span><?php echo __('景在眼里 故事在耳边'); ?></span>
                    </div>
                </div>
            </a>
            <div class="voice-top-sub">
                <?php echo __('景在眼里 故事在耳边'); ?>
            </div>
        </div>
        <div class="voice-top-right">
            <a class="item" href="<?php echo Configure::read('Download.ios')?>">
                <img src="/img/ios.voice2.0.png" alt=""/>
                <p><?php echo __('iPhone下载') ?></p>
            </a>
            <a class="item" href="<?php echo Configure::read('Download.android')?>">
                <img src="/img/android.voice2.0.png" alt=""/>
                <p><?php echo __('Android下载') ?></p>
            </a>
            <div class="rcode">
                <img src="/img/rcode.voice2.0.png" alt=""/>
            </div>
        </div>
        <a href="javascript:void(0)" class="btn v-btn" data-whatapp="rel" data-page="voice" data-id="downBtn" <?php echo $this->App->relWeixin(); ?>>
            <?php echo __('下载鱼说'); ?>
        </a>
    </div>
    <div class="voice-content">
        <div class="voice-play-box">
            <div id="jp_container" class="jp-video jp-video-box">
                <div class="jp-type-playlist">
                    <div id="jquery_jplayer" class="jp-jplayer"></div>
                    <div class="jp-gui">
                        <div class="jp-interface">
                            <div class="jp-progress">
                                <div class="jp-seek-bar">
                                    <div class="jp-play-bar"></div>
                                </div>
                            </div>
                            <div class="jp-current-time"></div>
                            <div class="jp-duration"></div>
                            <div class="jp-controls-holder">
                                <ul class="jp-controls">
                                    <li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li>
                                    <li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="jp-playlist" style="display: none;">
                        <ul>
                            <li></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="voice-msg">
            <div class="title"><?php echo $voice['title'] ?>
            </div>
            <div class="times">
                <i class="play-icon"></i><span class="hot paly-time-now">00:00</span>/<span class="paly-time-count">00:00</span>
                <?php echo $voice['play_total'] ?>
                <?php echo __('次播放'); ?>
            </div>
            <div class="user">
                <img class="user-img right-5" src="<?php if(isset($voice['user']['avatar']['x80'])){echo $voice['user']['avatar']['x80'];}else{ echo '/img/default.user.png';}?>" alt=""/>
                <span class="right-5 username"><?php echo $voice['user']['username'] ?></span>
                <script>
                    var str="<?php echo $voice['user']['username'] ?>";
                    if(str.length>15){
                        str=str.substr(0,10);
                        $('.username').html(str+"...");
                    }
                </script>
                <?php if($voice['user']['is_verified']==1){
                        echo  '<img class="user-vip" src="/img/vip.png" alt=""/>';
                } ?>
                <div class="score">
                    <span class="big" data-score="<?php echo $voice['score'] ?>"></span>
                </div>
            </div>
        </div>
    </div>
    <div class="voice-replay">
        <div class="bg-c"> 

        </div>
        <div class="top-bar">
            <span><?php echo __('扫描到手机'); ?>
                <a href="javascript:void(0)" class="ico-code">
                    <img src="/img/icon-code.png" alt=""/>
                </a></span>
            <div class="share">
                <a data-share="Sina" class="ico-share-weibo" href="javascript:void(0)"></a>
                <a data-share="Tqq" class="ico-share-QQweibo" href="javascript:void(0)"></a>
                <a data-share="Qzone" class="ico-share-Qzone" href="javascript:void(0)"></a>
                <a data-share="fb" class="ico-share-facebook" href="javascript:void(0)"></a>
            </div>
            <span class="r"><?php echo __('分享到'); ?></span>
        </div>
        <div class="line"></div>
        <ul class="re-list">

        </ul>
        <div class="bottom-bar">
            <a id="readmore" href="javascript:void(0)"><?php echo __('加载更多评论'); ?></a>
        </div>
    </div>
    <a href="javascript:void(0)" class="voice-download" data-page="voice" data-id="openBtn" data-whatapp="rel" <?php echo $this->App->relWeixin(); ?>>
        <?php echo __('打开鱼说客户端播放完整版本'); ?>
    </a>
    <div class="r-code">
        <div class="codebox">
            <img class="codeimg" src="http://chart.apis.google.com/chart?chs=110x110&cht=qr&chld=L|0&chl=<?php echo $this->Session->read('Api.Token.web_host')?>voice/<?php echo $voice['short_id'] ?>" alt=""/>
            <img class="down" src="/img/v3.0/down-trigon.png" alt=""/>
        </div>
    </div>
</div>
<iframe id="reIframe" style="display: none;" src="" frameborder="0"></iframe>
        <?php echo $this->element('pop_messager'); ?>
<?php echo $this->element('js_browser_width'); ?>

<?php 
$this->end();
?>

<script type="text/javascript">
$(function(){
    <?php if(empty($hash)): ?>
    var APP_PLAYER = "fishsaying://voice/<?php echo $voice['short_id'] ?>";
    <?php else: ?>
    var APP_PLAYER = "fishsaying://voice/<?php echo $voice['short_id'] ?>?hash=<?php echo $hash; ?>";
    <?php endif; ?>
    var WEB_PLAYER_IOS="<?php echo Configure::read('Download.ios')?>";
    var WEB_PLAYER_ANDROID="<?php echo Configure::read('Download.android')?>";
    checkbrowser();

    var mp3url="<?php echo $this->Voice->shareHash($hash) ? $voice['voice'] : $voice['trial_voice'] ?>";
    if(mp3url.indexOf("avthumb")==-1)
    {
        if(isANDROID() || isIOS()){
            $('[data-id="openBtn"]').html("<?php echo __('打开鱼说发现更多精彩'); ?>");
        }
    }
    if(isANDROID()){
        $('#reIframe').attr('src',APP_PLAYER);
    }
    if(isIOS()){
        $('#reIframe').attr('src',APP_PLAYER);
    }
    if($.isWeibo()){
        $("[data-whatapp]").attr('rel','weibo');
    }
    $('[data-id="downBtn"]').on('click',function(){
        var _target= $(this).attr('rel');
        if(_target=='weixin'){
            if(isIOS()){
                $('#model-download-weixin').find('[data-sele="title"]').html('如何下载鱼说');
                $('#model-download-weixin').find('[data-sele="msg"]').html('微信屏蔽了向外跳转的链接，请点击右上角的【分享】图标，在浏览器中打开。');
                $('#model-download-weixin').find('[data-sele="img1"]').attr('src','/img/2.0/g1.png');
                $('#model-download-weixin').find('[data-sele="msg1"]').html('点击右上角【分享】');

                $('#model-download-weixin').find('[data-sele="img2"]').attr('src','/img/2.0/g2.png');
                $('#model-download-weixin').modal();
                return false;
            }
            if(isANDROID()){
                $('#model-download-weixin').find('[data-sele="title"]').html('如何下载鱼说');
                $('#model-download-weixin').modal();
                return false;
            }
        }
//        if(_target=='weibo'){
//            if(isIOS()){
//                $('#model-download-weixin').find('[data-sele="title"]').html('如何下载鱼说');
//                $('#model-download-weixin').find('[data-sele="msg"]').html('新浪微博屏蔽了向外跳转的链接，请点击右上角的 ··· 图标，在浏览器中打开。');
//                $('#model-download-weixin').find('[data-sele="img1"]').attr('src','/img/xinlang-ios-t1.png');
//                $('#model-download-weixin').find('[data-sele="img2"]').attr('src','/img/xinlang-ios-t2.png');
//                $('#model-download-weixin').find('[data-sele="msg1"]').html("点击右上角···图标");
//                $('#model-download-weixin').modal();
//                return false;
//            }
//            if(isANDROID()){
//                $('#model-download-weixin').find('[data-sele="title"]').html('如何下载鱼说');
//                $('#model-download-weixin').find('[data-sele="msg"]').html('新浪微博屏蔽了向外跳转的链接，请点击右上角的 ··· 图标，在浏览器中打开。');
//                $('#model-download-weixin').find('[data-sele="img1"]').attr('src','/img/xinlang-and-t1.png');
//                $('#model-download-weixin').find('[data-sele="img2"]').attr('src','/img/xinlang-and-t2.png');
//                $('#model-download-weixin').find('[data-sele="msg1"]').html("点击右上角···图标");
//                $('#model-download-weixin').modal();
//                return false;
//            }
//        }
        if(isANDROID()){
            window.location =WEB_PLAYER_ANDROID;
        }else if(isIOS()){
            window.location =WEB_PLAYER_IOS;
        }
    });

    $('[data-id="openBtn"]').on('click',function(){
        var _target= $(this).attr('rel');
        if(_target=='weixin'){
            if(isIOS()){
                $('#model-download-weixin').find('[data-sele="title"]').html('打不开链接？');
                $('#model-download-weixin').find('[data-sele="msg"]').html('微信屏蔽了向外跳转的链接，请点击右上角的【分享】图标，在浏览器中打开。');
                $('#model-download-weixin').find('[data-sele="img1"]').attr('src','/img/2.0/g1.png');
                $('#model-download-weixin').find('[data-sele="msg1"]').html('点击右上角【分享】');

                $('#model-download-weixin').find('[data-sele="img2"]').attr('src','/img/2.0/g2.png');
                $('#model-download-weixin').modal();
                return false;
            }
            if(isANDROID()){
                $('#model-download-weixin').find('[data-sele="title"]').html('打不开链接？');
                $('#model-download-weixin').modal();
                return false;
            }
        }
        if(_target=='weibo'){
            if(isIOS()){
                $('#model-download-weixin').find('[data-sele="title"]').html('打不开链接？');
                $('#model-download-weixin').find('[data-sele="msg"]').html('新浪微博屏蔽了向外跳转的链接，请点击右上角的 ··· 图标，在浏览器中打开。');
                $('#model-download-weixin').find('[data-sele="img1"]').attr('src','/img/xinlang-ios-t1.png');
                $('#model-download-weixin').find('[data-sele="img2"]').attr('src','/img/xinlang-ios-t2.png');
                $('#model-download-weixin').find('[data-sele="msg1"]').html("点击右上角···图标");
                $('#model-download-weixin').modal();
                return false;
            }
            if(isANDROID()){
                $('#model-download-weixin').find('[data-sele="title"]').html('打不开链接？');
                $('#model-download-weixin').find('[data-sele="msg"]').html('新浪微博屏蔽了向外跳转的链接，请点击右上角的 ··· 图标，在浏览器中打开。');
                $('#model-download-weixin').find('[data-sele="img1"]').attr('src','/img/xinlang-and-t1.png');
                $('#model-download-weixin').find('[data-sele="img2"]').attr('src','/img/xinlang-and-t2.png');
                $('#model-download-weixin').find('[data-sele="msg1"]').html("点击右上角···图标");
                $('#model-download-weixin').modal();
                return false;
            }
        }
        $('#reIframe').attr('src',APP_PLAYER);
    });

    $('.ico-code').on('mouseenter',function(){
        if(!(isIOS() || isANDROID())){
            $('.r-code').show();
            $('.r-code').css('top',$(this).offset().top-110+'px');
            $('.r-code').css('left',$(this).offset().left-50+'px');
        }
    });
    $('.ico-code').on('mouseleave',function(){
        $('.r-code').hide();
    });

    $('[data-share="Qzone"]').on('click',function(){
        var title="<?php echo $voice['title'] ?>";
        var summary="<?php echo $voice['user']['username'] ?>"
        var _url="<?php echo $this->Session->read('Api.Token.web_host')?>voice/<?php echo $voice['short_id'] ?>";
        $.shareQzone(title,_url,summary,'<?php echo $voice['cover']['x640'] ?>.jpg');
    });

    $('[data-share="Sina"]').on('click',function(){
        var title="<?php echo $voice['title'] ?> <?php echo $this->Session->read('Api.Token.web_host')?>voice/<?php echo $voice['short_id'] ?> (分享自 @鱼说FishSaying)";
        var _url="<?php echo $this->Session->read('Api.Token.web_host')?>voice/<?php echo $voice['short_id'] ?>";
        $.shareSina(title,_url,'<?php echo $voice['cover']['x640'] ?>.jpg');
    });
    $('[data-share="Tqq"]').on('click',function(){
        var title="<?php echo $voice['title'] ?> <?php echo $this->Session->read('Api.Token.web_host')?>voice/<?php echo $voice['short_id'] ?> (分享自 @鱼说FishSaying)";
        var _url="<?php echo $this->Session->read('Api.Token.web_host')?>voice/<?php echo $voice['short_id'] ?>";
        $.shareTqq(title,_url,'<?php echo $voice['cover']['x640'] ?>.jpg');
    });
    $('[data-share="fb"]').on('click',function(){
        var title="<?php echo $voice['title'] ?> <?php echo $voice['user']['username'] ?>";
        var _url="<?php echo $this->Session->read('Api.Token.web_host')?>voice/<?php echo $voice['short_id'] ?>";
        $.shareFacebook(title,_url,'<?php echo $voice['cover']['x640'] ?>.jpg');
    });


    $(".big").each(function(){
        var _score = $(".big").attr('data-score');
        _score= (_score*2).toString();
        var strs = new Array();
        strs = _score.split(".");
        if(strs.length>0){
            $(this).html(strs[0].toString()+'.');
            if(strs.length>1){
                $(this).after(strs[1].substring(0,1));
            }else{
                $(this).after('0');
            }
        }
    });

    var _commentspage=0;
    var _total=0;
    var _totalpage=0;
    function getComments(){
        _commentspage++;
        var _voice_id="<?php echo $voice['_id'] ?>";
        $.ajax({
            type: "POST",
            url: "/comments/getlist",
            data: {voice_id:_voice_id,page:_commentspage},
            dataType: "json",
            success: function (json) {
                if(json.result){
                    _total=json.data.total;
                    if(_total <= 20){
                        _totalpage=1;
                    }else{
                        _totalpage=Math.ceil(_total/20);
                    }
                    if(json.data.total>=1){
                        $(json.data.items).each(function(index) {
                            var val = json.data.items[index];
                            var _userimg;
                            if(!val.user.avatar){
                                _userimg="/img/default.user.png";
                            }else{
                                _userimg=val.user.avatar.x80;
                            }
                            var _username=val.user.username;
                            var _score=val.score;
                            var _content=val.content;
                            var _time=val.created.sec;
                            var _item='<li><img class="re-user-img" src="'+_userimg+'" alt=""/><div class="re-box"><span class="re-user-name">'+_username+'</span><br /><i class="ico-star-'+_score+'"></i><p class="re-msg">'+_content+'</p></div><span class="re-time">'+_time+'</span><div class="line"></div></li>';
                            if((_commentspage>=_totalpage) && (index >= (json.data.items.length-1))){
                                _item='<li><img class="re-user-img" src="'+_userimg+'" alt=""/><div class="re-box"><span class="re-user-name">'+_username+'</span><br /><i class="ico-star-'+_score+'"></i><p class="re-msg">'+_content+'</p></div><span class="re-time">'+_time+'</span></li>';
                                $('.bottom-bar').hide();
                            }
                            $('.re-list').append(_item);
                        });
                    }else{
                        $('#readmore').attr('isLock','true');
                        $('#readmore').html("<?php echo __('暂无评论'); ?>")
                    }

                }else{
                    $.messager(json.message)
                }
            },
            error:function(){

            }
        })
    }
    $('#readmore').on('click',function(){
        if($(this).attr('isLock')!='true'){
            getComments();
        }
    });
    getComments();
    if ($(window).width() >= 768) {
        var myCanvas = document.getElementById("myCanvas");
        if (myCanvas.getContext)
        {
            var img=new Image();
            img.src="/voices/cover/<?php echo $voice['short_id'] ?>/x640";

            img.onload=function(){
                var canvas = document.getElementById( 'myCanvas' );
                var imgw=img.width;
                var imgh=img.height;
                var w=$(window).width();
                var h=$(window).height();
                var iw,ih,posx,posy;
                if(w>=h){
                    iw=640;
                    ih=640*(h/w);
                    posx=0;
                    posy=(640-ih)/2;
                }else{
                    iw=640*(w/h);
                    ih=640;
                    posx=(640-iw)/2;
                    posy=0;
                }
                canvas.style.width  = w + "px";
                canvas.style.height = h + "px";
                canvas.width = w;
                canvas.height = h;
                var context = canvas.getContext("2d");
                if((imgw<640) || (imgh<640)){
                    context.drawImage( img, 0, 0 ,imgw, imgh, 0, 0, w, h);
                }else{
                    context.drawImage( img,posx,posy ,iw, ih, 0, 0, w, h);
                }
                stackBlurCanvasRGB('myCanvas',0, 0, canvas.width, canvas.height,40);
            };
        }
        else
        {

        }

    }
})
</script>
<script>
    var dataForWeixin={
        appId:"",
        MsgImg:"<?php echo $voice['cover']['x640'] ?>.jpg",
        url:"<?php echo $this->Session->read('Api.Token.web_host')?>voice/<?php echo $voice['short_id'] ?>",
        title:"<?php echo $voice['title'] ?>",
        desc:"<?php echo $voice['user']['username'] ?>",
        fakeid:"",
        callback:function(){}
    };
    (function(){
        var onBridgeReady=function(){
            //发送给朋友
            WeixinJSBridge.on('menu:share:appmessage', function(argv){
                WeixinJSBridge.invoke('sendAppMessage',{
                    "appid":dataForWeixin.appId,
                    "img_url":dataForWeixin.MsgImg,
                    "img_width":"120",
                    "img_height":"120",
                    "link":dataForWeixin.url,
                    "desc":dataForWeixin.desc,
                    "title":dataForWeixin.title+"(分享自 @鱼说FishSaying)"
                }, function(res){(dataForWeixin.callback)();});
            });
            //发送到朋友圈
            WeixinJSBridge.on('menu:share:timeline', function(argv){
                (dataForWeixin.callback)();
                WeixinJSBridge.invoke('shareTimeline',{
                    "img_url":dataForWeixin.MsgImg,
                    "img_width":"120",
                    "img_height":"120",
                    "link":dataForWeixin.url,
                    "desc":dataForWeixin.desc,
                    "title":dataForWeixin.title+"(分享自 @鱼说FishSaying)"
                }, function(res){});
            });
            //分享到微博
            WeixinJSBridge.on('menu:share:weibo', function(argv){
                WeixinJSBridge.invoke('shareWeibo',{
                    "content":dataForWeixin.title+"(分享自 @鱼说FishSaying)",
                    "url":dataForWeixin.url
                }, function(res){(dataForWeixin.callback)();});
            });
        };
        if(document.addEventListener){
            document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
        }else if(document.attachEvent){
            document.attachEvent('WeixinJSBridgeReady'   , onBridgeReady);
            document.attachEvent('onWeixinJSBridgeReady' , onBridgeReady);
        }
    })();
</script>