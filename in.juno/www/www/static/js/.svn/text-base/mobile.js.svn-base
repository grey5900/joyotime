(function($){
    //插件
    $.fn.extend({
        /**
         * 为空不提交表单，目标设置为焦点
         *
         **/
        is_empty: function() {
          var $this = $(this).first();
            if ($this.val()) {
                return false;
            } else {
                $this.focus();
                return true;
            }
        },
        /**
         * 加一
         *
         **/
        plus: function(number) {
          var $this = $(this);
          if($this.first().html() == ''){
            $this.each(function(){
              $(this).html( number );
            })
          }else{
            var num = parseInt($this.first().html());
            $this.each(function(){
              $(this).html( num + number );
            })
          }
        },
        /**
         * modal
         *
         **/
        messagerModal: function(options) {
          var defaults = {
              top: 20,
              overlay: 0.7,
              closeButton: null
          };
          var overlay = $("<div id='messager_modal'></div>");
          $("body").append(overlay);
          options =  $.extend(defaults, options);
          return this.each(function() {
            var o = options;
            $(this).click(function(e) {
              var modal_id = $(this).attr("href");
                
              $("#messager_modal").click(function() {
                close_modal(modal_id);
              });
              $(o.closeButton).click(function() {
                   close_modal(modal_id);
              });

              var modal_height = $(modal_id).outerHeight();
              var modal_width = $(modal_id).outerWidth();
              $('#messager_modal').css({ 'display' : 'block', opacity : 0 });
              $('#messager_modal').fadeTo(100,o.overlay);
              $(modal_id).css({
                'display' : 'block',
                'position' : 'fixed',
                'opacity' : 0,
                'z-index': 11000,
                'left' : 50 + '%',
                'margin-left' : -(modal_width/2) + "px",
                'top' : o.top + "px"
              });
              $(modal_id).fadeTo(100,1);
              e.preventDefault();
            });
          });

          function close_modal(modal_id){
            $("#messager_modal").fadeOut(100);
            $(modal_id).css({ 'display' : 'none' });
          }
        }
    });
    //方法
    $.extend({

      /*
       *$.messagerModal("#test");
      */
        messagerModal : function(id) {
          var overlay = $("<div id='messager_modal'></div>");
          $("body").append(overlay);
          var modal_id = $(id);
          
          // $("#messager_modal").click(function() {
          //     close_modal(modal_id);
          // });
          $(".modal-close").click(function() {
               close_modal(modal_id);
          });
          $(".modal-shut").click(function() {
               close_modal(modal_id);
          });

          var modal_height = modal_id.outerHeight();
          var modal_width = modal_id.outerWidth();

          $('#messager_modal').css({ 'display' : 'block', opacity : 0.7 });
          $('#messager_modal').fadeTo(100,modal_id.overlay);
          $(modal_id).css({
            'display' : 'block',
            'position' : 'fixed',
            'opacity' : 0.7,
            'z-index': 11000,
            'left' : 50 + '%',
            'margin-left' : -(modal_width/2) + "px",
            'top' : "70px"
          });
          modal_id.fadeTo(100,1);

          function close_modal(modal_id){
            $("#messager_modal").fadeOut(200);
            modal_id.css({ 'display' : 'none' });
          }
        },

        messager : function(message,refer) {
          if(!message) {
            return false;
          }
            var $el = $("<div id=\"messager\"></div>");
            $("body").append($el);
            $el.html(message);
            var sTop = $(document).scrollTop(),
              sHeight = $(window).height(),
              sWidth = $(window).width(),
              elHeight = $el.outerHeight(),
              elWidth = $el.outerWidth(),
              elTop = (sHeight - elHeight) / 2 + sTop,
              elLeft = (sWidth - elWidth) / 2;
            $el.css({
                top: elTop + 10,
                left: elLeft,
                opacity: '0'
            })
            .animate({
                opacity: '1',
                top: elTop,
                queue: true
            }, 200)
            .delay('3000')
            .animate({
                opacity: '0',
                top: elTop + 10,
                queue: true
            }, 200,
            function() {
              $el.remove();
              if (refer) {
                if (refer == 'reload') {
                  window.location.reload();
                } else {
                  window.location.href = refer;
                }
              }
            });
        },
        checkAuth : function() {
            if( (typeof online_id === 'undefined') || online_id === null || online_id === 0 || online_id === ''){
                //window.mobile && window.mobile.login ? window.mobile.login() : $.messager('请先登录IN成都！');
                $.messager('请先登录IN成都！');
                return false;
            } else {
                return true;
            }
        }
    });
})(jQuery);


$(function(){

  $('#event-enter-btn[class!=".disabled"]').on('click',function(){
    if(!$.checkAuth()) { return false; }
    var $this = $(this);
    var data_str = "";
    var $required = $('#mobile_apply_submit input[class=required]');
    var err = true;
    $.each($required, function () {
      if($(this).is_empty()){
        $.messager('亲，无法提交，必要信息未填写完整。');
        err = false;
        return false;
      }
    });
    if(!err) {
      return false;
    }
    $("input[type=text]", "form[name=signup_form]").each(function(){
      data_str += $(this).attr("name") + "：" + $(this).val() + "^_^";
    });

    $.ajax({
      url: '/event_auth/signup/'+$this.data('type')+'/'+$this.data('id') + '/?uid=' + online_id + '&' + (new Date().getTime()),
      type: 'post',
      dataType: 'json',
      data: {data: data_str},
      success: function(json, textStatus, xhr) {
        if(json.code > 0 ){
          $.messager(json.message);
        } else {
          if($this.val() != '提交报名表') {
            $('#event-enter-btn')
            .find('h2')
            .text('已报名')
            .end()
            .addClass('disabled')
            .off();
            $('#event-enter-btn-num').plus(1);
          } else {
            location.href = '/event_new/m_index/' + $this.data('id') +'/?uid='+online_id;
          }
        }
      }
    });
    return false;
  });


  $(".expand").each(function() {
    $(this).on("click", function() {
      $(this).toggleClass("on");
    });
  });

  $("#placelist").each(function() {
    var $list = $("#placelist > dd:gt(3)"),
        $viewall = $("#viewall"),
        $more = $("#more");
        $list.hide();
        text = $more.text();
    $viewall.click(function() {
      if ($list.is(":visible")) {
        $list.hide();
        $more.text(text);
      } else {
        $list.show();
        $more.text('收起全部');
      }
      return false;
    });
  });

  $.getScript('/static/js/jquery.infinitescroll.min.js',function(){
    $('#users').infinitescroll({
      navSelector : '.pagination',
      nextSelector : '#rolling_next_page',
      itemSelector : '.user-list',
      loading : {
        finished : undefined,
        finishedMsg : "没有更多了...",
        img : "/img/empty.gif",
        msg : null,
        msgText : "",
        selector : null,
        speed : 'fast',
        start : undefined
      }
    });
    $('#feeds').infinitescroll({
      navSelector : '.pagination',
      nextSelector : '#rolling_next_page',
      itemSelector : '.feed-list',
      loading : {
        finished : undefined,
        finishedMsg : "没有更多了...",
        img : "/img/empty.gif",
        msg : null,
        msgText : "",
        selector : null,
        speed : 'fast',
        start : undefined
      }
    });
    $('#district_event').infinitescroll({
      navSelector : '.pagination',
      nextSelector : '#rolling_next_page',
      itemSelector : '.district-event-list',
      loading : {
        finished : undefined,
        finishedMsg : "没有更多了...",
        img : "/img/empty.gif",
        msg : null,
        msgText : "",
        selector : null,
        speed : 'fast',
        start : undefined
      }
    });
  });

  //投票 
  $.getScript('/static/js/bootstrap.min.js',function(){ 
    $('.gopoll').on('click' , function() {
      if(!$.checkAuth()) { return false; }
      var $this = $(this),
          oid = $this.data('oid'),
          itemtype = $this.data('itemtype'),
          itemid = $this.data('itemid');
          $this.button("loading");
      $.ajax({
          url: '/vote/vote/',
          type: 'POST',
          dataType: 'json',
          cache: false,
          data: {oid:oid,itemtype:itemtype,itemid:itemid,uid:online_id},
          success: function(json, textStatus, xhr) {
            var votes = $('#votes_'+oid).html();
            if (json.code == 1) {
              $.messager(json.msg);
              $this.button("reset");
              $('#votes_'+oid).html(parseInt(votes)+1);
            } else {
              $.messager(json.msg);
              if(over != 1){
                $this.button("reset");
              }             
            }
          }
        });
      return false;
    });
  });

})

function goBack(){
	if(history.length === 1){
    	window.location = "#"
    } else {
    	console.log(history.length);
        history.back();
    }
}
