/*
* FenPay Tags
* Based on JQuery UI Tag-it widget.
*
* @version v1.0 (05/2013)
*
* Copyright 2013, fenpay.com.
* Released under the MIT license.
*
* Dependencies:
*   jQuery v1.4+
*   jQuery UI v1.8+
*   jQuery.tag-it http://aehlke.github.com/tag-it/
*   
* Examples:
*	If you want to custom format of input hidden id field, 
*	you could specify it as below, NOTE the '{0}' is placeholder for replacement:
*   
*   $('#keywords').tags({formatId: 'data[AutoReplyMessageTag][{0}][AutoReplyTag][id]'});
*/
(function($) {
	"use strict";
	$.extend(
	    $.fn.select2.defaults, {
            formatNoMatches: function () { return "此指令已经存在，不许重复"; },
            formatInputTooShort: function (input, min) { var n = min - input.length; return "请输入" + n + "个以上字符";},
            formatInputTooLong: function (input, max) { var n = input.length - max; return "请删掉" + n + "个字符";},
            formatSelectionTooBig: function (limit) { return "你只能选择最多" + limit + "项"; },
            formatLoadMore: function (pageNumber) { return "加载结果中..."; },
            formatSearching: function () { return "搜索中..."; 
        }
	});
	
	if (!String.prototype.format) {
        String.prototype.format = function() {
            var args = arguments;
            
            return this.replace(/{(\d+)}/g, function(match, number) {
                return typeof args[number] != 'undefined'? args[number]: match;
            });
        };
	}
	$.widget('fenpay.tags', {
		options: {
			placeholderText : '多个TAG用空格或逗号分隔...',
			width: '280',
			availableTags: [],
			limit: 10,
			ajax: false,
			minimumInputLength: 1,
			initSelection: false,
		},
		_setOption : function(key, value) {
			this._super(key, value);
		},
		_setOptions : function(options) {
			this._super(options);
		},
		_create: function() {
			var self = this;
			var opts = {
				placeholder: this.options.placeholderText,
				tags: this.options.availableTags,
				width: this.options.width,
				tokenSeparators: [",", " "],
				maximumSelectionSize: this.options.limit,
				minimumInputLength: this.options.minimumInputLength
			};
			if(self.options.ajax) {
				opts.ajax = self.options.ajax;
			}
			if(self.options.initSelection) {
				opts.initSelection = self.options.initSelection;
			}
			
			self.element.select2(opts);
		},
		_destroy: function() {}

	});
}(jQuery));