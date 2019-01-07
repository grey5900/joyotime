/*
 * FenPay charCount
 *
 *
 *
 */

(function($) {
    'use strict';

    

    $.fn.charCount = function(options) {
       
        var defaults = {
                maxChars : 200,
                maxCharsWarning : 190,
                msgFontSize : '14px',
                msgFontColor : '#aaa',
                msgFontFamily : 'Arial',
                msgTextAlign : 'top',
                msgWarningColor : '#aaa',
                msgAppendMethod : 'insertAfter',
                msg : '',
                msgPlacement : 'prepend',
                numFormat : 'CURRENT/MAX'
            };
        options = $.extend(defaults, options);
        
        return this.each(function() {
           var $this = $(this);
            var jqEasyCounterMsg = $("<div class=\"jqEasyCounterMsg\">&nbsp;</div>");
            var jqEasyCounterMsgStyle = {
                'font-size' : options.msgFontSize,
                'font-family' : options.msgFontFamily,
                'color' : options.msgFontColor,
                'opacity' : 0,
                'display' : 'inline-block'
            };

            jqEasyCounterMsg.css(jqEasyCounterMsgStyle);
            jqEasyCounterMsg[options.msgAppendMethod]($this);

            $this
                .bind('keydown keyup keypress', doCount).bind('focus paste', function() {
                        setTimeout(doCount, 10);
                    }).bind('blur', function() {
                        jqEasyCounterMsg.stop().fadeTo('fast', 0);
                        return false;
                    });

            function doCount() {

                var val = $this.val(),
                    length = val.length,
                    html;
                if (length >= options.maxChars) {
                    val = val.substring(0, options.maxChars);
                }

                if (length > options.maxChars) {
                    var originalScrollTopPosition = $this.scrollTop();
                    $this.val(val.substring(0, options.maxChars));
                    $this.scrollTop(originalScrollTopPosition);
                }

                if (length >= options.maxCharsWarning) {
                    jqEasyCounterMsg.css({
                        "color" : options.msgWarningColor
                    });
                } else {
                    jqEasyCounterMsg.css({
                        "color" : options.msgFontColor
                    });
                }

                if (options.msgPlacement == 'prepend') {
                    html = options.msg + options.numFormat;
                } else {
                    html = options.numFormat + options.msg;
                }

                html = html.replace('CURRENT', $this.val().length);
                html = html.replace('MAX', options.maxChars);
                html = html.replace('REMAINING', options.maxChars - $this.val().length);

                jqEasyCounterMsg.html(html);
                jqEasyCounterMsg.stop().fadeTo('fast', 1);
            }
        });
    };
})(jQuery);
