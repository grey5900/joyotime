//name:jDialog
//author: jaskang
//createtime:2014-3-29 20:49:58

var jDialogId = [];
(function ($) {
    $.jDialog={

    }
    $.jDialog = function (options) {

        var id = parseInt(Math.floor(Math.random() * 1000 + 1));
        while ($.inArray(id, jDialogId) != -1) {
            id = parseInt(Math.floor(Math.random() * 1000 + 1));
        }
        jDialogId.push(id);

        var defaults = {
            title:"",
            content: "这是一个JasUI-Dialog插件",
            width: 350,
            height: 0,
            timer: 0,
            showbuttons:false,
            buttons: [],
            okval: "确认",
            ok: function () { return false;},
            cancelval: "取消",
            cancel: function () { return false; },
            showclose:true,
            close: function () { },
            theme:""
        };
        var options = $.extend(defaults, options);
        var _objdiv = "<div id='j-dialog-" + id + "' class='j-dialog ";
        if (options.theme != "") {
            _objdiv = _objdiv + "j-dialog-" + options.theme + "'>";
        } else {
            _objdiv = _objdiv + "'>";
        }
        _objdiv = _objdiv + "<div class='j-dialog-header'>";
        if (options.showclose) {
            _objdiv = _objdiv + "<a href='javascript:void(0)' class='j-close j-dialog-close'></a>"
        }
        if (options.title != "") {
            _objdiv = _objdiv + "<h5 class='j-dialog-title'>" + options.title + "</h5>";
        }
        _objdiv = _objdiv + "</div>";
        _objdiv = _objdiv + "<p class='j-dialog-content'>" + options.content + "</p>";

        if (options.showbuttons) {
            _objdiv = _objdiv + "<div class='j-dialog-footer'>";
            $.each(options.buttons,function(i,value) {
                _objdiv = _objdiv + "<a class='j-button' data-id='" + i + "'>" + value.title + "</a>";
            })
            _objdiv = _objdiv + "<a class='j-button j-button-primary j-dialog-ok'>" + options.okval + "</a>";
            _objdiv = _objdiv + "<a class='j-button j-dialog-cancel'>" + options.cancelval + "</a>";
            _objdiv = _objdiv + "</div>";
        };

        _objdiv=_objdiv+"</div>";
        $("body").append(_objdiv);
        var _obj = $('#j-dialog-' + id)
        if (options.height>0) {
            _obj.css("height", options.height);
        }
        _obj.css("width", options.width);
        _obj.css("margin-top", '-' + (options.height / 2) + 'px');
        _obj.css("margin-left", '-' + (options.width / 2) + 'px');
        _obj.animate({ top: '30%',opacity:1 }, 0);
        if (options.showclose) {
            _obj.find('.j-dialog-close').on('click', function () {
                $.jDialogRemove(id, options.close);
            })
            _obj.find('.j-dialog-ok').on('click', function () {
                if (!options.ok()) {
                    $.jDialogRemove(id, options.close);
                }
            })
            _obj.find('.j-dialog-cancel').on('click', function () {
                if (!options.cancel()) {
                    $.jDialogRemove(id, options.close);
                }
            })
        }
        if (options.showbuttons) {
            $.each(options.buttons, function (i, value) {
                _obj.find("[data-id=" + i + "]").on('click', function () {
                    if (!value.callback()) {
                        $.jDialogRemove(id, options.close);
                    }
                })
            })
        };
        if (options.timer> 0) {
            setTimeout(function () {
                $.jDialogRemove(id,options.close);
            }, options.timer);
        }
        return id;
    },
        $.jDialogRemove = function (id, callback) {
            if ($.inArray(id, jDialogId)!=-1) {
                jDialogId.splice($.inArray(id, jDialogId), 1);
                $('#j-dialog-' + id).animate({ top: '0', opacity: 0 }, 500, function () {
                    $('#j-dialog-' + id).remove();
                    if (callback) {
                        callback();
                    }
                });
            }
        },
        $.jTip = function (options) {
            var defaults = {
                content: "这是一个JasUI-Dialog插件",
                width: 200,
                timer: 0,
                showclose: false,
                close: function () { },
                theme: ""
            };
            var options = $.extend(defaults, options);
            $.jDialog(options);
        },
        $.jFloatText = function (txt,color,posX,posY) {
            var $i = $("<b>").text(txt);
            var x = '50%', y = '40%';
            var _color = '#E94F06';
            if (color) {
                _color= color;
            }
            if (posX) {
                x = posX;
            }
            if (posY) {
                y = posY;
            }
            $i.css({ top: 200, left: x, position: "absolute", color: "#E94F06" });
            $("body").append($i);
            $i.animate({ top: 20, opacity: 0}, 1500, function () {
                $i.remove();
            });
        }
})(jQuery);
