$(function(){
    $voicesVoice = $("input#submitVoice");
    $voicesVoice.uploadify({
        'buttonText': '选择音频', 
        'swf'       : '/js/uploadify.swf',
        //'debug' : true,
        'height'   : 36,
        'width'   : 130,
        'uploader'  : '/uploads/voice',
        'formData': { 'session': $('.mainway').data('session')}, 
        'multi'     : false,
        'method'    : 'post',  
        'queueSizeLimit' : 1,
        'simUploadLimit' : 1,
        'cancelImg' : '/img/uploadify-cancel.png',
        'fileTypeExts': '*.m4a; *.mp3; *.wav',
        'preventCaching' : true,
        'removeTimeout' : 3,
        'onSelect' : function(file) {
            
            $('#promptVocie').text('');
        },
        'onComplete'  : function(event, ID, fileObj, response, data) {
          console.log(response);
        },
        'onUploadSuccess' : function(file, json, response) {
            var data = eval('('+json+')');

            if( data.result == true) {
                $('#'+file.id).find('.data').html('上传完毕');
                $('#promptVocie').text("上传成功");
                $('#voicesVoice').val(data.file);
                $('#voicesVoice').attr('length',data.length);
            } else {
                $('#'+file.id).find('.data').html('上传失败');
                $('#promptVocie').text(data.message);
            }
        },
        'onError':function(event,queueId,fileObj,errorObj){
            $.messager(errorObj.info);
        },
        'onSelectError': function(errorObj) {
           console.log(errorObj);
        }

    });

})
