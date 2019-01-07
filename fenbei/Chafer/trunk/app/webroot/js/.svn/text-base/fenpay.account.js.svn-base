$(function(){
    $(".form-horizontal").validate({
        errorClass: "alert-danger alert",
        errorElement: "span",
        highlight: function(element) {
            if (element.type === 'radio') {
                this.findByName(element.name)
                    .closest(".control-group")
                    .removeClass("valid success valid")
                    .addClass("error");
            } else {
                $(element)
                    .closest(".control-group")
                    .removeClass("valid success valid")
                    .addClass("error");
            }
        },
        unhighlight: function(element) {
            if (element.type === 'radio') {
                this.findByName(element.name)
                    .closest(".control-group")
                    .removeClass("error success valid")
                    .addClass("valid");
            } else {
                $(element)
                    .closest(".control-group")
                    .removeClass("error success valid")
                    .addClass("valid");
            }
        },
        rules: {
            'data[AutoReplyFixcode][message_ids][]': 'required'
        },
        messages: { 
        }
    });
})