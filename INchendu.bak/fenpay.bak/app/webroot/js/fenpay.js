! function($) {    //插件    $.fn.extend({        /**         * 删除         *         * $.dedetetr(url,id);         **/        deleteTr : function() {            var $del = $(this);            var id = $del.data('id'), url = $del.data('url') + id;            var dangerBtn = $('.modal a.btn-danger');            dangerBtn.click(function() {                $.ajax({                    dataType : "json",                    url : url,                    type : 'GET',                    success : function(resp) {                        $del.closest("tr").remove();                        $del.closest("div.well").remove();                        $.messager(resp.message);                        return false;                    }                });            });            $('.modal a.btn-cancel').click(function() {                dangerBtn.unbind('click');            });        }    });    //方法    $.extend({        /**         * 提示         *         * $.messager();         **/        messager : function(message, refer, callback) {            if (!message) {                return false;            }            $(".alert-info").remove();            var $el = $("<div id=\"messager\"></div>");            $(".alert-message").html($el).removeClass('hide');            $el.html(message);            $.lightning();        },        /**         * 弹框单选打印         *         **/        popupSelect : function() {            var popup = $('#select_news'),                selected = $('.ui-selected', popup);            var graphic_text = $('#graphic_text', selected).text(),                data = JSON.parse($('.data', selected).text()),                graphic_img = $('#graphic_img', selected).attr("src"),                graphic_alert = $('#show_graphic');            if (selected.length) {                $('#show_graphic_img').attr({                    "src" : graphic_img                });                $('#show_graphic_text').text(graphic_text);                $('input.input_auto_reply_message_id').val(data.id);                graphic_alert.removeClass('hide');                popup.modal('hide');            }        },        /**         * 闪退         *         **/        lightning : function() {            var alert = $('.alert');            if (alert) {                alert.delay('3000').animate({                    queue : true                }, 200, function() {                    $(".alert").addClass('hide');                });            }        }    });}(window.jQuery);$(function() {    $('[rel="selectList"]').selectable();    $('[rel="substr"]').truncate();    $(document).on('click', '[data-del="tr"]', function() {        var $this = $(this);        $this.deleteTr();    });    $(document).on('dblclick', '[data-select="dbclick"]', function() {        $.popupSelect();    });    $('[data-select="multiple"]').multiselect({            includeSelectAllOption: true,            enableFiltering: true,            buttonClass: 'hide',            maxHeight: 200,            buttonText: function(options) {                var ts = new Date().getTime();                $('input.input_auto_reply').remove();                options.each(function(i) {                    ts += i;                    var data = JSON.parse($(this).attr('data'));                                        var input = $('<input />');                    input                    .attr('type', 'hidden')                    .attr('name', 'data[AutoReplyLocationMessage]['+ts+'][auto_reply_location_id]')                    .attr('value', data.id)                    .attr('class', 'input_auto_reply_location_id input_auto_reply');                                        input                    .attr('type', 'hidden')                    .attr('name', 'data[AutoReplyLocationMessage]['+ts+'][auto_reply_message_id]')                    .attr('class', 'input_auto_reply_message_id input_auto_reply');                });                }        });    $(document).on('click', '[data-select="done"]', function() {        if ($('.ui-selected').length == 0) {            $.messager('请选择一个图文素材。');            return false;        }        $.popupSelect();    });    $.lightning();});