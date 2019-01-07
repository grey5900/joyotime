<div class="mask-img"></div>
<div class="s-1 text-center">
    <div class="">
        <p class="margin-index"><img class="index-logo" src="/img/v3.0/index-logo.png" alt="鱼说"/></p>
        <div class="text-img">
            <img src="/img/v3.0/text-<?php echo __('English')=='English'?'chn':'eng'; ?>.png" class="img-responsive" alt="景在眼里，故事在耳边"/>
        </div>
        <div class="line-divider">
        </div>
        <div class="lead"><?php echo __('鱼说，且听且行') ?></div>
        <p class="btn-hasicon-group">
            <a class="btn btn-lg btn-withe-block btn-hasicon ios-download" href="<?php echo Configure::read('Download.ios')?>" role="button" <?php echo $this->App->relWeixin(); ?>>
                <i class="fa fa-apple fa-2x"></i>
                <span><?php echo __('iPhone版下载') ?></span>
            </a>
            <a class="btn btn-lg btn-withe-blue btn-hasicon android-download" href="<?php echo Configure::read('Download.android')?>" role="button" <?php echo $this->App->relWeixin(); ?>>
                <i class="fa fa-android fa-2x"></i>
                <span><?php echo __('Android版下载') ?></span>
            </a>
        </p>
    </div>
    <div class="index-meta">
        <div class="right-code">
            <?php echo __('版权所有 ©成都鱼说科技有限公司  蜀ICP备13026841号') ?>
        </div>
        <div class="content-us">
            <a class="btn-contact-us" href="javascript:void(0)" data-toggle="modal" data-target="#contact-us"><?php echo __('联系我们') ?></a>|


            <a href="/langs/setLang/<?php echo __('English')=='English'?'eng':'chn'; ?>">
                <?php echo __('English') ?>
            </a>
        </div>
    </div>
    <div class="r-code">
        <div class="codebox">
            <img class="codeimg" src="/img/v3.0/code.png" alt=""/>
            <img class="down" src="/img/v3.0/down-trigon.png" alt=""/>
        </div>
    </div>
</div>
<?php echo $this->element('contact_us'); ?>
<?php echo $this->element('modelwindow'); ?>
<script type="text/javascript">
$(function(){
    checkbrowser();

    if(isIOS()) {
        $('.android-download').hide();
    } else if(isANDROID()) {
        $('.ios-download').hide();
    }
    $('.ios-download,.android-download').on('mouseenter',function(){
        if(!(isIOS() || isANDROID())){
            $('.r-code').css('left',$(this).offset().left+52+'px');
            $('.r-code').css('top',$(this).offset().top-130+'px');
            $('.r-code').addClass('fadeInCode');
        }
    });
    $('.ios-download,.android-download').on('mouseleave',function(){
        $('.r-code').removeClass('fadeInCode');
    });

    $("[rel='weixin']").on("click", function(){
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
        return true;
    });

    indexresize();
    $(window).resize(function(){
        indexresize();
    })

});
function indexresize(){
    var _conheight=$('.s-1 > div').height();
    if(_conheight < $(window).height()){
        $('.s-1').height($(window).height());
        var padding= ($(window).height()-_conheight)*0.4;
        $('.margin-index').css('margin-top',padding+ 'px');
    }else{
        $('.margin-index').css('margin-top','10px');
    }
}
</script>

