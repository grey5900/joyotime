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
* 	If you want to custom format of input hidden id field, 
* 	you could specify it as below, NOTE the '{0}' is placeholder for replacement:
*   
*   $('#keywords').tags({formatId: 'data[AutoReplyMessageTag][{0}][AutoReplyTag][id]'});
*/
(function($) {
	if (!String.prototype.format) {
	  String.prototype.format = function() {
	    var args = arguments;
	    return this.replace(/{(\d+)}/g, function(match, number) { 
	      return typeof args[number] != 'undefined'
	        ? args[number]
	        : match
	      ;
	    });
	  };
	}
	
	$.widget('fenpay.tags', {
		options: {
			placeholderText : '不同图文可以关联相同的标签,多个标签用空格分隔。',
			formatId: 'data[AutoReplyMessageTag][{0}][AutoReplyTag][id]',
			formatName: 'data[AutoReplyMessageTag][{0}][AutoReplyTag][name]',
			availableTags: [],
			checkUrl: '/auto_reply_tags/check/',
			autocomplete: {delay: 0, minLength: 2, autoFocus: true},
			allowRepeat: true	// whether allow tag existed in database to add, default is true
		},
		_setOption : function(key, value) {
			this._super(key, value);
		},
		_setOptions : function(options) {
			this._super(options);
		},
		_create: function() {
			var self = this;
			this.element.tagit({
		        placeholderText: this.options.placeholderText,
		        availableTags: this.options.availableTags,
				autocomplete: this.options.autocomplete,
		        beforeTagAdded: function(event, ui) {
		        	self._check(event, ui);
		        }
		    });
		},
		_check: function(event, ui) {
			var self = this;
			if(ui.duringInitialization == true) {
                var span = $('.tagit-label', ui.tag);
            	var tag = JSON.parse(span.text());
            	span.text(tag.name);
            	self._input.id(event, ui, tag.id, self);
            	self._input.name(event, ui, tag.name, self);
            	ui.tag.removeClass('hide');
            	$('li.tagit-new').before(ui.tag);
            	return true;
            } else {
	            var tag = $('.tagit-label', ui.tag).text();
	            $.ajax({
	                url: self.options.checkUrl+tag,
	                type: 'GET',
	                dataType: 'json',
	                success: function(resp) {
	                	var container = $('li.tagit-new').parent('ul').parent();
	                	if(self.options.allowRepeat) {
	                		if(resp.id) {
		                    	self._input.id(event, ui, resp.id, self);
		                    }
	                	} else {
	                		if(resp.id) {
	                			$('.help-inline', container).remove();
	                			container.append('<span class="help-inline error-message">指令已存在</span>');
	                			return false;
	                		}
	                	}
	                	// add tag to DOM
	                	$('li.tagit-new').before(ui.tag);
	                	// add input.name to DOM
	                	self._input.name(event, ui, tag, self);
	                	// clean input value
	                	$('li.tagit-new input').val('');
	                	// clean error information if existed.
	                	$('.help-inline', container).remove();
	                	return true;
	                }
				});
            }
		},
		_input: {
			id: function(event, ui, id, self) {
				return $('<input>')
						.attr('type', 'hidden')
						.attr('name', self.options.formatId.format(event.timeStamp))
						.val(id)
						.appendTo(ui.tag);
			},
			name: function(event, ui, name, self) {
				return $('<input>')
						.attr('type', 'hidden')
						.attr('name', self.options.formatName.format(event.timeStamp))
						.val(name)
						.appendTo(ui.tag);
			},
		},
		_destroy: function() {
			 
		}
	});
}(jQuery));