/*
* FenPay Fileupload
* Based on JQuery UI Tag-it widget.
*
* @version v1.0 (05/2013)
*
* Copyright 2013, fenpay.com.
* Released under the MIT license.
*
* jQuery File Upload Plugin 5.31.1
* https://github.com/blueimp/jQuery-File-Upload
*
* Copyright 2010, Sebastian Tschan
* https://blueimp.net
*
* Licensed under the MIT license:
* http://www.opensource.org/licenses/MIT
*/
(function($) {
	$.widget('fenpay.image_uploder', {
		options: {
			attachmentField: 'data[AutoReplyMessageNews][image_attachment_id]',
			dataType: 'json',
		},
		_setOption : function(key, value) {
			this._super(key, value);
		},
		_setOptions : function(options) {
			this._super(options);
		},
		_create: function() {
			var self = this;
			this.element.fileupload({
				dataType: self.options.dataType,
				done: function (e, data) {
					$.each(data.result.files, function (index, file) {
						var cover = $('img#cover-img');
						cover.attr('src', file.thumbnail_url);

						var upload_btn = $('.fileupload .btn-inverse');
						upload_btn.hide();
						self._setAttachment(file);
						
						//self._registerRemove(file,cover,upload_btn);

						
						$('.fileupload .btn-danger')
						.show()
						.attr('data-url', file.delete_url)
						.attr('data-type', file.delete_type)
						.click(function(){
							var btn = $(this);
							var url = btn.attr('data-url');
							var type = btn.attr('data-type');
							if(url && type) {
								$.ajax({
									dataType: "json",
									url: url,
									type: type,
									success: function(resp) {
										cover.attr('src', 'http://www.placeholder-image.com/image/320x160');
										upload_btn.show();
										btn.hide();
									}
								});
							} else {
								console.log('delete failed, please check parameters.');
							}
							self._cleanAttachment();
						});
					});
				}
			});
		},
		_setAttachment: function(file) {
			$('input[name="'+this.options.attachmentField+'"]').val(file.image_attachment_id);
		},
		_cleanAttachment: function() {
			$('input[name="'+this.options.attachmentField+'"]').val('');
		},

		_registerRemove: function() {
			var del_btn = $('.fileupload .btn-danger');
			del_btn.removeClass('hide');
			del_btn.on('click',function(){
				var btn = $(this);
				var url = btn.attr('data-url');
				var type = btn.attr('data-type');
				if(url && type) {
					$.ajax({
						dataType: "json",
						url: url,
						type: type,
						success: function(resp) {
							console.log(file,cover);
							cover.attr('src', 'http://www.placeholder-image.com/image/320x160');
							upload_btn.show();
							btn.hide();
						}
					});
				} else {
					console.log('delete failed, please check parameters.');
				}
				that._cleanAttachment();
			})
		},
		_destroy: function() {}
	});
}(jQuery));