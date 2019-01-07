!function ($) {

    "use strict";
    var _w,_h;

    var Fileupload = function (element, options) {
        
        this.$element = $(element);
        this.type = this.$element.data('uploadtype') || (this.$element.find('.thumbnail').length > 0 ? "image" : "file")
    
        this.$input = this.$element.find(':file');
        if (this.$input.length === 0) return;
    
        this.name = this.$input.attr('name') || options.name;
    
        this.$hidden = this.$element.find('input[type=hidden][name="'+this.name+'"]');
        if (this.$hidden.length === 0) {
          this.$hidden = $('<input type="hidden" />');
          this.$element.prepend(this.$hidden)
        }
    
        this.$preview = this.$element.find('.fileupload-preview');
        var height = this.$preview.css('height');
        
        //this.$element.find('.fileupload-preview.fileupload-exists.thumbnail').css('display','inline-block');
        if (this.$preview.css('display') != 'inline' && height != '0px' && height != 'none') this.$preview.css('line-height', height)
    
        this.original = {
          'exists': this.$element.hasClass('fileupload-exists'),
          'preview': this.$preview.html(),
          'hiddenVal': this.$hidden.val()
        };
        
        this.$remove = this.$element.find('[data-dismiss="fileupload"]');
    
        this.$element.find('[data-trigger="fileupload"]').on('click.fileupload', $.proxy(this.trigger, this))
    
        this.listen()
    }

    Fileupload.prototype = {

        listen: function() {
            this.$input.on('change.fileupload', $.proxy(this.change, this));
            $(this.$input[0].form).on('reset.fileupload', $.proxy(this.reset, this));
            if (this.$remove) this.$remove.on('click.fileupload', $.proxy(this.clear, this))
         },

        change: function(e, invoked) {
            if (invoked === 'clear') return;
    
            var file = e.target.files !== undefined ? e.target.files[0] : (e.target.value ? { name: e.target.value.replace(/^.+\\/, '') } : null)
        
            if (!file) {
                //this.$preview.hide()
                $('#crop').imgAreaSelect({hide:true});
                //this.clear()
                return
            }
    
            this.$hidden.val('');
            this.$hidden.attr('name', '');
            this.$input.attr('name', this.name);
           
          
    
            if (this.type === "image" && this.$preview.length > 0 && (typeof file.type !== "undefined" ? file.type.match('image.*') : file.name.match(/\.(m4a|gif|png|jpe?g)$/i)) && typeof FileReader !== "undefined") {
                $('#crop').imgAreaSelect({hide:true});
                var $readyPreview = $('#readyPreview');
                    $readyPreview.addClass('hide');
                var reader = new FileReader();
                var preview = this.$preview;
                var element = this.$element;
    
                reader.onload = function(e) {
                    preview.html('<img id="crop" src="' + e.target.result + '" ' + (preview.css('max-height') != 'none' ? 'style="max-height: ' + preview.css('max-height') + ';"' : '') + ' />');
                    element.addClass('fileupload-exists').removeClass('fileupload-new');
                    element.find('.help-block').removeClass('hide');
                    element.find('.cropHelp').removeClass('hide');
                    element.find('.cropText').addClass('hide');
                };
    
                var img = new Image();
                var _URL = window.URL || window.webkitURL;
    
                img.onload = function() {
                    _w = this.width;
                    _h = this.height;
    
                    if ( _w < 640 || _h < 640 ) {
                        element.find('.help-block p').removeClass('hide');
                        element.find('.cropHelp').addClass('hide');
                        $('#submitImage').addClass('hide');
                        element.find('.cropText').addClass('hide');
                        $('#imageCover').val('').focus();
                        preview.hide();
                        return false;
                    } else {
                        preview.show();
                        element.find('.help-block p').addClass('hide');
                        $('#crop').imgAreaSelect({
                            aspectRatio: '1:1',
                            handles: true,
                            fadeSpeed: 200,
                            resizeable:false,
                            enable:true,
                            show: true,
                            parent:$('.page-wrapper'),
                            x1: 0, y1: 0, x2: 100, y2: 100,
                            onInit: previewInit,
                            onSelectChange: previewSelectChange,
                            onSelectEnd:function (img,selection) {
                                var $l = $('#l').val(), $t = $('#t').val(), $w = $('#w').val(), $h = $('#h').val();

                                $('#submitImage').data("options",{"top":$t,"left":$l,"width":$w,"height":$h});

                                element.find('.cropHelp').addClass('hide');
                                if ($w < 640 || $h < 640) {
                                    element.find('.cropText input').removeClass('rt');
                                    element.find('.cropText').removeClass('hide');
                                    $('#submitImage').addClass('hide');
                                    return false;
                                } else {
                                    $('#submitImage').removeClass('hide');
                                    element.find('.cropText').removeClass('hide');
                                    element.find('.cropText input').addClass('rt');
                                    return false;
                                }

                            }
                        });
                    }
                };
              
                img.src = _URL.createObjectURL(file);
                return reader.readAsDataURL(file);
            }
    
        },

        clear: function(e) {
          this.$hidden.val('');
          this.$hidden.attr('name', this.name);
          this.$input.attr('name', '');
          //ie8+ doesn't support changing the value of input with type=file so clone instead
          if (navigator.userAgent.match(/msie/i)){
              var inputClone = this.$input.clone(true);
              this.$input.after(inputClone);
              this.$input.remove();
              this.$input = inputClone;
          }else{
              this.$input.val('');
          }
    
          this.$preview.html('');
          this.$element.addClass('fileupload-new').removeClass('fileupload-exists');
          this.$element.find('.cropText').addClass('hide');
          this.$element.find('.cropHelp').addClass('hide');
          if (e) {
    
            this.$input.trigger('change', [ 'clear' ]);
    
            e.preventDefault();
          }
        },

        reset: function(e) {
          this.clear();
          this.$hidden.val(this.original.hiddenVal);
          this.$preview.html(this.original.preview);
    
          if (this.original.exists) {
              this.$element.addClass('fileupload-exists').removeClass('fileupload-new');
          } else {
              this.$element.addClass('fileupload-new').removeClass('fileupload-exists');
          }
        },

        trigger: function(e) {
          this.$input.trigger('click');
          e.preventDefault();
        }
    };


    $.fn.fileupload = function (options) {
        return this.each(function () {
          var $this = $(this) , data = $this.data('fileupload');
          if (!data) $this.data('fileupload', (data = new Fileupload(this, options)));
          if (typeof options == 'string') data[options]();
        });
    };

    $.fn.fileupload.Constructor = Fileupload;
    
    // 选择上传

    $(document).on('click.fileupload.data-api', '[data-provides="fileupload"]', function (e) {
        var $this = $(this);
        
        
        if ($this.data('fileupload')) return;

        $this.fileupload($this.data());
    
        var $target = $(e.target).closest('[data-dismiss="fileupload"],[data-trigger="fileupload"]');
        
        if ($target.length > 0) {
            $target.trigger('click.fileupload');
            e.preventDefault();
        }
    });


    function onSelectEndfunc(element) {
        var $l = $('#l').val(), $t = $('#t').val(), $w = $('#w').val(), $h = $('#h').val();

        $('#submitImage').data("options",{"top":$t,"left":$l,"width":$w,"height":$h});

        element.find('.cropHelp').addClass('hide');
        if ($w < 640 || $h < 640) {
            element.find('.cropText input').removeClass('rt');
            element.find('.cropText').removeClass('hide');
            $('#submitImage').addClass('hide');
            return false;
        } else {
            $('#submitImage').removeClass('hide');
            element.find('.cropText').removeClass('hide');
            element.find('.cropText input').addClass('rt');
            return false;
        }

    }
    // 图片裁剪
  
    function previewInit(img, selection,e) {
      
        if (!selection.width || !selection.height)
        return;
        
        var ias = $('#crop').imgAreaSelect({ fadeSpeed: 400, handles: true, instance: true });
        var boxW = img.width;
        var boxH = img.height;
        var scaleX = _w / boxW;
        var scaleY = _h / boxW;
        
        var PIT = _w + _h;
        var pit = boxW + boxH;
        var scales = PIT / pit;
        
        ias.setOptions({minWidth:640 / scales,minHeight:640 / scales});
        ias.animateSelection(0, 0, 640 / scales, 640 / scales, 'slow',scales);

        var $l = $('#l').val(0),
            $t = $('#t').val(0),
            $w = $('#w').val(640),
            $h = $('#h').val(640);


        $('#submitImage').data("options",{"top":$t,"left":$l,"width":$w,"height":$h});

        var element=$('div.fileupload');
        element.find('.cropHelp').addClass('hide');
        if ($w < 640 || $h < 640) {
            element.find('.cropText input').removeClass('rt');
            element.find('.cropText').removeClass('hide');
            $('#submitImage').addClass('hide');
            return false;
        } else {
            $('#submitImage').removeClass('hide');
            element.find('.cropText').removeClass('hide');
            element.find('.cropText input').addClass('rt');
            return false;
        }
    }

    function previewSelectChange(img, selection) {
        if (!selection.width || !selection.height)
        return;
        var boxW = img.width;
        var boxH = img.height;
        var scaleX = _w / boxW;
        var scaleY = _h / boxW;
    
        var PIT = _w + _h;
        var pit = boxW + boxH;
        var scales = PIT / pit;
        
        var $l = $('#l').val(Math.round(scales * selection.x1)), 
            $t = $('#t').val(Math.round(scales * selection.y1)), 
            $w = $('#w').val(Math.round(scales * selection.width)), 
            $h = $('#h').val(Math.round(scales * selection.height));
    }
    
    $.extend($.imgAreaSelect.prototype, {
        animateSelection: function (x1, y1, x2, y2, duration,scales) {
            var fx = $.extend($('<div/>')[0], {
                ias: this,
                start: this.getSelection(),
                end: { x1: x1, y1: y1, x2: x2, y2: y2}
            });
        
            $(fx).animate({
                cur: 1
            },
            {
                duration: duration,
                step: function (now, fx) {
                    var start = fx.elem.start, end = fx.elem.end,
                        curX1 = Math.round(start.x1 + (end.x1 - start.x1) * now),
                        curY1 = Math.round(start.y1 + (end.y1 - start.y1) * now),
                        curX2 = Math.round(start.x2 + (end.x2 - start.x2) * now),
                        curY2 = Math.round(start.y2 + (end.y2 - start.y2) * now);
                    fx.elem.ias.setSelection(curX1, curY1, curX2, curY2);
                    fx.elem.ias.update();

                    var $l = $('#l').val(), $t = $('#t').val(), $w = $('#w').val(), $h = $('#h').val();
                    $('#l').val() == Math.round(scales * curX1);
                    $('#t').val() == Math.round(scales * curY1);
                    $('#w').val() == Math.round(scales * curX2);
                    $('#h').val() == Math.round(scales * curY2);
                    
                    $('#submitImage').data("options",{"top":$t,"left":$l,"width":$w,"height":$h});
                    
                }
            });
        }
    });

  
    // 上传图片

    $('#submitImage').click(function(e) {
        var $this = $(this);
        
        $('#promptImage').text('');
        if ($('#imageCover').val().length > 0) {
            
            var url = '/uploads/cover';
            var name = $('#imageCover').attr("name");
            
            var width = $this.data('options').width;
            var height = $this.data('options').height;
            var top = $this.data('options').top;
            var left = $this.data('options').left;
            
            if(width >= 640 ) {
                $(document).find('.fileupload-preview').addClass('loading');
                $(document).find('.fileupload-preview').append('<img class="loading-icon" src="/img/load.gif">');
                $this.button('loading');
                ajaxFileUpload(name,top,left,width,height,url);
            } else {
                $.messager('请上传图片,封面图选区尺寸须大于640*640');
                return false;
            }
        } else {
            $.messager('请选择图片');
        }
        e.preventDefault();
    });

    function ajaxFileUpload(name,top,left,width,height,url) {
        $.ajaxFileUpload({
            url : url,
            data : {"name":name,"width":width,"height":height,"top":top,"left":left},
            secureuri : false,
            fileElementId : 'imageCover',
            dataType : 'json',
            success : function(data, status) {
                var $preview = $(document).find('.fileupload-preview');
                var $rePreview = $('#readyPreview');
                if(data.result == true) {
                    //$preview.removeClass('loading');
                    //$preview.find('.loading-icon').remove();

                    $preview.hide();
                    $('#crop').imgAreaSelect({hide:true});
                    $('#uploadButtonHide').html('');
                    $rePreview.find('img').attr('src',data.url);
                    $rePreview.attr('class','thumbnail');
                    $('#voicesCover').val(data.file);
                    $('#promptImage').text('上传成功'); 
                    $('#submitImage').addClass('hide').button('reset');
                } else {
                   $('#promptImage').text(data.message); 
                }
            },
            error : function(data, status, e) {
                $.messager(data.message);
                $('#submitImage').button('reset');
            }
        });
    }  

}(window.jQuery);


