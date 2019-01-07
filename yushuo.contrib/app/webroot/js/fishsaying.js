! function($) {
    //插件
    $.fn.extend({
        /**
         * 为空不提交表单，目标设置为焦点
         *
         **/
        is_empty: function() {
            var $this = $(this).first();
            if ($this.val()) {
                return false;
            } else {
                $this.focus();
                return true;
            }
        }
    });
    //方法
    $.extend({
        /**
         * 提示
         *
         * $.messager();
         **/

        messager : function(message, refer, callback) {
            if (!message) {
                return false;
            }
            $(".alert-info").remove();
            var $el = $("<div id=\"messager\"></div>");
            $(".alert-message").html($el).removeClass('hide');
            $el.html(message);
            $.lightning();
        },

        /**
         * 弹框单选打印
         *
         **/
        popupSelect : function() {
            var popup = $('#select_news'),
                selected = $('.ui-selected', popup);

            var graphic_text = $('#graphic_text', selected).text(),
                data = JSON.parse($('.data', selected).text()),
                graphic_img = $('#graphic_img', selected).attr("src"),
                graphic_alert = $('#show_graphic');

            if (selected.length) {
                $('#show_graphic_img').attr({
                    "src" : graphic_img
                });
                $('#show_graphic_text').text(graphic_text);
                $('input.input_auto_reply_message_id').val(data.id);
                graphic_alert.removeClass('hide');
                popup.modal('hide');
            }
        },

        /**
         * 闪退
         *
         **/
        lightning : function() {
            var alert = $('.alert');
            if (alert) {

                alert.delay('3000').animate({
                    queue : true
                }, 200, function() {
                    $(".alert").addClass('hide');
                });
            }
        },
        /**
         * 返回顶部
         *
         * $.returntop();
         **/

        returntop : function(btn) {
            var obj = $(btn).click(function(){
                $("html, body").animate({
                    scrollTop: 0
                },
                120)
            });
            $(window).on("scroll",function(){
                var topHeight = $(document).scrollTop(),
                winHeight = $(window).height();
                if (200 < topHeight) {
                    obj.fadeIn();
                } else {
                    obj.fadeOut();
                }
            })
        }
    });

}(window.jQuery);

$(function() {
    $.lightning();
    
    // 删除
    var remove_url = '',
        linkRemove = '';
    $('a.remove-link').click(function(){
        linkRemove = $(this);
        remove_url = linkRemove.attr('data-remove');
    });
    $('[data-id="remove"]').click(function(){
        $.getJSON(remove_url, function(data) {
            $.messager(data.message);
            linkRemove.closest("tr").remove();
        });
    })
    
    $.returntop('button.btn-primary');
    
    // 录制解说
    $('form#voicesAddForm').submit(function(){
        
        var voicesTitle = $(this).find('#voicesTitle'),
            voicesCover = $(this).find('#voicesCover'),
            voicesVoice = $(this).find('#voicesVoice'),
            voicesLength =$(this).find('#voicesLength'),
            voiceLong = $(this).find('#RecordCommentLongitude'),
            voiceLat = $(this).find('#RecordCommentLatitude'),
            voiceAddess = $(this).find('#RecordCommentAddress'),
            voiceDuration = $(this).find('#voiceDuration'),
            voice = voicesVoice.val(),
            btn = $(this).find("button[type=submit]"),
            cropW = $(this).find('#w'),
            cropH = $(this).find('#h'),
            cropL = $(this).find('#l'),
            cropT = $(this).find('#t');
        // if(voicesTitle.is_empty() || $(this).find('#RecordCommentLongitude').is_empty() || voicesLength.is_empty()) {
            // console.log(voicesTitle);
            // return false;
          // }
        if (voicesTitle.is_empty() || voicesTitle.length > 15 ) {
            voicesTitle.focus();
            $.messager('填写解说标题，不能超过15个字符');
            return false;
        }
         
        if(voicesCover.is_empty() || cropW.is_empty() || cropH.is_empty() || cropL.is_empty() || cropT.is_empty() || cropW.val() < 640 ){
            $.messager('请上传图片,封面图选区尺寸须大于640*640');
            return false;
        }   
        if (voicesVoice.is_empty()){
            $.messager('请上传音频');
            return false;
        } 
//        else if (!/.(m4a)$/.test(voice)) {
//            $.messager('请上传m4a格式音频');
//            return false;
//        }
        if(voiceLong.is_empty() || voiceAddess.is_empty() || voiceLat.is_empty()) {
            $.messager('请填写地址经纬度');
            return false;
          }
        if(voiceDuration.is_empty()) {
            $.messager('无法得到音频文件的时长,音频解析失败,请检查音频是否为AAC编码！');
            return false;
          }
         
        // if (isNaN(voicesLength.val()) || voicesLength.val() >= 180) {
            // voicesLength.focus();
            // $.messager('请输入180以内数字');
            // return false;
        // }
        //btn.button('loading');
        $('#target').loadingOverlay();
    });
    
    // 编辑解说
    
    $('form#voicesEditForm').submit(function(){
        
        var voicesTitle = $(this).find('#voicesTitle'),
            voicesCover = $(this).find('#voicesCover'),
            voicesVoice = $(this).find('#voicesVoice'),
            voicesLength =$(this).find('#voicesLength'),
            voiceLong = $(this).find('#RecordCommentLongitude'),
            voiceLat = $(this).find('#RecordCommentLatitude'),
            voiceAddess = $(this).find('#RecordCommentAddress'),
            voiceDuration = $(this).find('#voiceDuration'),
            voice = voicesVoice.val(),
            btn = $(this).find("button[type=submit]"),
            cropW = $(this).find('#w'),
            cropH = $(this).find('#h'),
            cropL = $(this).find('#l'),
            cropT = $(this).find('#t');
            
        if (voicesTitle.is_empty() || voicesTitle.length > 15 ) {
            voicesTitle.focus();
            $.messager('填写解说标题，不能超过15个字符');
            return false;
        }
          
        // if (voicesVoice.is_empty()){
            // $.messager('请上传音频');
            // return false;
        // } else if (!/.(m4a)$/.test(voice)) {
            // $.messager('请上传m4a格式音频');
            // return false;
        // }
        // if(voicesCover.is_empty() || cropW.is_empty() || cropH.is_empty() || cropL.is_empty() || cropT.is_empty() || cropW.val() < 640 ){
            // $.messager('请上传图片,封面图选区尺寸须大于640*640');
            // return false;
        // } 
        if(voiceLong.is_empty() || voiceAddess.is_empty() || voiceLat.is_empty()) {
            $.messager('请填写地址经纬度');
            return false;
          }
        if(voiceDuration.is_empty()) {
            $.messager('无法得到音频文件的时长,音频解析失败,请检查音频是否为AAC编码！');
            return false;
          }
        //btn.button('loading');
        $('#target').loadingOverlay();
    });
    
    
    $('.invalid-reason').tooltip('hide');
    
    // jsPlayer
    
    $('[data-play="modal"]').on('click',function(){
        var $this = $(this);
        var $voice = $this.data('voice');
        var playerId = $this.data('player-id');
        
        $('#modalPlay').modal('show');
        $('#modalPlay .modal-body').prepend($('<div id="'+playerId+'" class="jp-jplayer"></div>'));
        $("#"+playerId).jPlayer({
            ready: function (event) {
                $(this).jPlayer("setMedia", {
                    m4a: $voice
                });
            },
            swfPath: "/js",
            supplied: "m4a",
            wmode: "window",
            smoothPlayBar: true,
            keyEnabled: true
        });
    });
    
    $('[data-id="colsePlay"]').on('click',function(){
        $('#modalPlay').modal('hide');
        $(".jp-jplayer").jPlayer("clearMedia" );
    });
    $('[data-info="modal"]').on('click',function(){
        $('#modalInfo').modal('show');
    });
    
    var _area = $('#voicesTitle');
    var _info = _area.next();
    var _max = _area.attr('maxlength');
    var _val;
    _area.bind('keyup change', function() {
        if (_info.find('span').size() < 1) {
            _info.append(_max);
        }
        _val = $(this).val();
        _cur = getByteLen(_val);
        if (_cur == 0) {
            _info.text(_max);
        } else if (_cur < _max) {
            _info.text(_max - _cur);
        } else {
            _info.text(0);
            $(this).val(getByteVal(_val,_max)); 
        }
    });
});

function getByteLen(val) {
    var len = 0;
    for (var i = 0; i < val.length; i++) {
        if (val[i].match(/[^x00-xff]/ig) != null) //全角
            len += 2;
        else
            len += 1;
    }
    return len;
}
function getByteVal(val, max) {
    var returnValue = '';
    var byteValLen = 0;
    for (var i = 0; i < val.length; i++) {
        if (val[i].match(/[^x00-xff]/ig) != null)
            byteValLen += 2;
        else
            byteValLen += 1;
        if (byteValLen > max)
            break;
        returnValue += val[i];
    }
    return returnValue;
}

