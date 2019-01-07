$(function(){

    $("#review-form").on('focusin mousedown', function() {
        if (!$.checkAuth()) {
            return false;
        } else {
            return true;
        }
    });
    $("#tip-post").on('submit',function(){
        var $this = $(this),
            $submit = $this.find('input[type=submit]');
        if($this.find('#place').val()=='' && is_have_place) {
            $.messager('请选择地点');
            return false;
        }
        $submit.button('loading');
        $.ajax({
            url: $this.attr('action') + $this.find('#place').val(),
            type: 'POST',
            dataType: 'json',
            data: $this.serialize(),
            success: function(json, textStatus, xhr) {
                if ( json.code == 0 ) {
                    $.messager(
                        json.msg,
                        $this.data('refer'),
                        function(){
                            $submit.button('reset');
                        });
                } else {
                    $.messager(json.msg,'',function(){
                        $submit.button('reset');
                    });
                }
            },
            error: function(xhr, textStatus, errorThrown) {
                alert('请检查您的网络');
            }
        });
        return false;
    });

    $.getScript('/static/js/jquery.raty.min.js',function(){
        $("#star-ranker .ranker").raty({
            hints : ['很差', '差', '一般', '好', '很好'],
            half : false,
            target : "#star-ranker .hint",
            targetKeep : true
        });
    });

    $.getScript('/static/js/jquery.ajaxfileupload.min.js',function(){

        if ($.browser.mozilla) {
            $("#cover").on('click',function(e){
                if($(e.target).is('label')){
                    $("#imgbtn").trigger('click');
                }
            });
        }

        $('#cover').delegate('#fileupload-exists','click',function(){
            var cover = $("#cover");
            cover.empty();
            var upFile = $("<input type='file' class='imgbtn' name='Filedata' id='imgbtn' size='8' />");
            $(upFile).appendTo(cover);
        });
        $('#cover').delegate('#imgbtn','change',function(){
            var $this = $(this);
            if($this.val() != ''){
                var cover =  $('#cover');
                $.ajaxFileUpload({
                    url: "/upload/upload_image",
                    secureuri: false,
                    fileElementId:'imgbtn',
                    data:{"uploadFile":$this.attr("name"),"uid":online_id},
                    dataType:"json",
                    success:function(data, status){
                        var data = eval("("+data+")");
                        if(data.code == 1){
                            var img = data.msg;
                            cover.html("")
                            cover.attr("id", "cover");
                            var imgvrew = $("<img />").attr({src:img});
                            var form_data = $("<input type='hidden' name='photo' id='photo'/>").attr("value",img);
                            var a_del = $("<a href='javascript:void(0)' class='close' id='fileupload-exists'>&times;</a>");
                            cover.append(a_del);
                            cover.append(imgvrew).append(form_data);
                        }else{
                            $.messager(data.msg);
                        }
                    }
                });
                $this.val('');
            }
        })
    })
})