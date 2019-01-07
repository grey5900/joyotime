/**
 * @author Grey
 */
!function($) {
    $.fn.extend({
        is_empty: function() {
            var $this = $(this).first();
            if ($this.val()) {
                return false;
            } else {
                $this.focus();
                return true;
            }
        }
    });
} (window.jQuery);
