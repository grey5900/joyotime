<div class="col-md-12">
    <div class="fs-index-intor clearfix fs-voice">
        <div class="fs-apps-download">
            <div class="fs-logo-voice">
                <a href="/" class="fs-logo" target="_blank" title="<?php echo __('鱼说'); ?>"><?php echo __('鱼说'); ?></a>
                <p>
                    <?php echo __('“我们重新定义了<em>导游</em>”'); ?>
                </p>
            </div>
           <div class="fs-voice-btn">
                 <a href="<?php echo Configure::read('Download.ios')?>" class="btn fs-btn btn-black"> 
                    <i class="icon-iphone"></i> 
                    <?php echo __('iPhone版下载') ?>
                </a> 
                <a href="<?php echo Configure::read('Download.android')?>"  class="btn fs-btn btn-green"> 
                    <i class="icon-android"></i><?php echo __('Android版下载') ?>
                </a>
            </div>
        </div>
        <div class="fs-intor-voice pull-left">
            <div id="jp_container" class="jp-video jp-video-270p">
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
                    <div class="jp-playlist">
                        <ul>
                            <li></li>
                        </ul>
                    </div>
                </div>
            </div>
            <span class="text-link">
              <a href="/" target="_blank"><?php echo __('立即下载鱼说手机客户端播放完整版本'); ?></a> >>>
            </span>
        </div>
        <div class="fs-voice-btn360">
            <a href="javascript:;" class="btn fs-btn btn-green" data-id="downBtn" <?php echo $this->App->relWeixin(); ?>> 
               <?php echo __('立即下载鱼说播放完整版本') ?>
            </a>
        </div>
    </div>
</div>

<?php echo $this->element('pop_messager'); ?>
<?php echo $this->element('js_browser_decide'); ?>
<?php echo $this->element('js_browser_width'); ?>

<?php 
$this->end();
?>