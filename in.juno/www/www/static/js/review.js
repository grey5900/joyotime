$(function(){
    $("#reply_list").delegate('#reply_to_form','submit',function(){
        if($("#reply_to_form").find("#message").is_empty()){
            return false;
        }else{
            var dom = $(this);
            $.ajax({
                url: dom.attr("action"),
                type: "post",
                dataType: "json",
                data:dom. serialize(),
                async: false,
                complete: function(xhr, textStatus){},
                success: function(data, textStatus, xhr){
                    $.messager(data.msg);
                    if(data.code == 1){
                        //+1
                        $(".reply-num").plus(1);
                        //刷新回复列表
                        load_reply("/review_replies", $("#reply_pid").val(), 1);
                    }
                    $("#reply_to_form").hide();
                }
            });
        }
        return false;
    });

    $("#reply-write").charCount();

    $(".action-reply").on('click',function(){
        if(!$.checkAuth()) {
            return false; 
        } else {
         $("#reply-write").focus();   
        }
    });

    //回复的回复
    $(".reply_to").on('click',function(){
        if(!$.checkAuth()) { 
            return false; 
        } else {
            var $this = $(this),
                $reply = $('#review_post');
            	$reply.data('id', $this.data('id'));
                $reply.data('pid', $this.data('pid'));
                $reply.data('ruid', $this.data('ruid'));
                $reply.data('type', 'reply');
                $reply.data('ruser', $this.data('ruser'));
                $("#reply-write").attr('placeholder','回复 ' + $this.data('ruser')+'：');
                $("#reply-write").focus(); 
        }
    });

    //回复
    $('#review_post').on('click', function(){
        if(!$.checkAuth()){
            $.modalSignin();
            return false;
        }else{
            var $this = $(this);
            var id = $this.data('id');
            var pid = $this.data('pid');
            var uid = $this.data('ruid');
            var content = $('#reply-write').val();
            var type = $this.data('type');
            var user = $this.data('ruser');
            
            //AJAX请求回复操作
            var url = '/common/reply';
            var $submit = $(this);
            $submit.button('loading');
            if($("#reply-write").is_empty()){
                $submit.button('reset');
                return false;
            }else{
                $.ajax({
                    type:'POST',
                    url:url,
                    data:{pid:pid,id:id,uid:uid,content:encodeURIComponent(content),type:type},
                    dataType:'json',
                    success:function(json){
                        var $this = $('#review_post');
                        var $write = $("#reply-write");
                        if(json.code > 0){
                            $.messager(json.msg);
                        }else if(json.code == -1){
                           $.messager(json.msg);
                            $.modalSignin();
                        }else{
                            $.messager(json.msg,'',function(){
                                $submit.button('reset');
                            });
                            //显示最新的回复
                            var li = $('<li></li>').prependTo($("#reply-li"));
                            //头像
                            $("<a></a>").attr({href:"/user/" + online_id,"class":"avatar"}).append($("<img />").attr({src:online_avatar, alt: online_name })).appendTo(li);
                            //回复内容
                            var c = $("<div></div>").addClass("msg").append($("<a></a>").attr({"class":"user-link", href:"/user/" + online_id}).append( online_name ));
                            if(type == 'reply'){//回复的回复
                                c.append("回复").append($("<a></a>").attr({"class":"user-link",href:"/user/"+uid}).append(user));
                            }
                            c.append($("<span></span>").append(":"+content)).appendTo(li);
                            //功能区
                            var a_rep = $("<a></a>").append(" ").attr({"class":"reply_to",href:"javascript:;",'data-pid':json.id,'data-ruid': online_id ,'data-ruser': online_name ,'data-type':"reply"});
                            a_rep.bind('click', function(){reply_reply($(a_rep));});
                            var p_response = $("<p></p>").addClass("response").appendTo(li);
                            $("<span></span>").addClass("action").append(a_rep).appendTo(p_response);
                            $("<span></span>").append("刚刚").appendTo(p_response);
                            //清理数据
                            
                            var p = $('#reply_list').data('pid');
                            var t = $('#reply_list').data('type');
                            $this.data('pid',p);
                            $this.data('ruid','');
                            $this.data('ruser','');
                            $this.data('type',t);
                            $write.val('');
                            $write.attr('placeholder','回复内容...');
                            // + 1
                            $('#reply_count_'+p).plus(1);
                            $submit.button('reset');
                        }
                    }
                });
            }
        }
    });
});
