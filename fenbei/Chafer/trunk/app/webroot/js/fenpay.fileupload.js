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

(function ($) {
    'use strict';
	$.widget('fenpay.image_uploder', {
        options: {
            attachmentField: 'data[AutoReplyMessageNews][image_attachment_id]',
            dataType: 'json',
            uploadButton: '.fileinput-button',
            delButton: '.fileinput-del'
        },
		_create: function() {
            var self = this;
            var uploadButton =  $(self.options.uploadButton),
                delButton =  $(self.options.delButton);

            var loading = $(".fileupload-loading");
                
            this.element.fileupload({
                disableImageResize: false,
                maxFileSize: 2000000,
                minFileSize: 0,
                acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
				dataType: self.options.dataType,
				done: function (e, data) {
					$.each(data.result.files, function (index, file) {
				        var cover = $('#cover-img'),
						    def = $('.img-default');
						    
						def.addClass('hide');
						cover.removeClass('hide').attr('src', file.url);

						uploadButton.hide();
						self._setAttachment(file);
						
                        delButton.removeClass('hide')
						.attr('data-url', file.delete_url)
						.attr('data-type', file.delete_type)
						.on('click',function(){
							var url = delButton.attr('data-url');
							var type = delButton.attr('data-type');

							if(url && type) {
								$.ajax({
									dataType: "json",
									url: url,
									type: type,
									success: function(resp) {
									    def.removeClass('hide');
										cover.addClass('hide');
										uploadButton.show();
										delButton.addClass('hide');
									   }
								    });
								    return true;
    							} else {
    								$.messager('删除失败。');
    							}
    							self._cleanAttachment();
    							return true;
						  });
					});
				},
				processstart: function (e) {
                    loading.removeClass('hide');
                },
                progress: function (e, data) {
                    loading.addClass('hide');
                },
				progressdone: function(e,data) {
				},
				fail: function(e,data) {
				    // data.errorThrown
                    // data.textStatus;
                    // data.jqXHR;
				    $.messager('如果无法上传。请试着用Firefox、Chrome等非IE浏览器试试。');  
				},
				processfail: function (e, data) {
                    $.messager('上传失败。上传大小不能超过2MB，必须jpg, jpeg, png格式');
                    loading.addClass('hide');
                }
			});
		},
		_setAttachment: function(file) {
			$('input[name="'+this.options.attachmentField+'"]').val(file.image_attachment_id);
		},
		_cleanAttachment: function() {
			$('input[name="'+this.options.attachmentField+'"]').val('');
		},
		_destroy: function() {}
	});
}(jQuery));