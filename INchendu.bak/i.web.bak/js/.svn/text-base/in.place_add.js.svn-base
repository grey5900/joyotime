﻿$(function(){
    //商家入驻表单
    $('#place-add-form').validate({
        rules: {
            placename: {
                required: true,
            },
            catelog: {
                required: true
            },
            address: {
                minlength: 4,
                required: true
            },
            phone: {
                number: true,
                required: true
            },
            verifycode: {
                required: true
            }
        },
        messages: {
            placename: {
                required: '请输入商家名称',
            },
            catelog: {
                required: '请选择商家类型'
            },
            address: {
                minlength: '请补充更详细的地址',
                required: '请输入商家地址'
            },
            phone: {
                number: '请输入正确的电话号码',
                required: '请输入商家电话'
            },
            verifycode: {
                required: '请输入验证码'
            }
        },
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
        success: function(element) {
            if (element.type === 'radio') {
                this.findByName(element.name)
                    .closest(".control-group")
                    .removeClass("error success valid")
                    .addClass("success");
            } else {
                $(element)
                    .closest(".control-group")
                    .removeClass("error success valid")
                    .addClass("success");
            }
        },
        submitHandler: function(form) {
            $.ajax({
                type: "POST",
                url: $(form).attr("action"),
                data: $(form).serialize(),
                dataType: "json",
                success: function(data) {
                    if(data.code == 1) {
                        // alert("商家登记成功，请等待管理员审核。");
                        $("#messager").messager({message:'商家登记成功，请等待管理员审核。'});
                    } else {
                        //alert("商家登记错误。");
                        $("#messager").messager({message:'商家登记错误。'});
                    }
                }
            });
            return false;
        }
    });

	//限制数字
	$("#phone,#average")
		.on("keyup",function(){
			$(this).val($(this).val().replace(/\D|^0/g,''));  
		})
		.on("paste",function(){
			$(this).val($(this).val().replace(/\D|^0/g,''));  
		})
		.css("ime-mode", "disabled");

	var marker = new soso.maps.Marker({
		position : new soso.maps.LatLng(30.6581000, 104.0660000),
		draggable: true
	});
	var geocoder = new soso.maps.Geocoder();
	$("#map").mapper({
		zoomLevel : 12,
		marker : marker,
        after : function(){
			soso.maps.Event.addListener(marker, 'dragend', function() {
				var position = marker.getPosition();
				$("#coordinate").val(position);
				geocoder.geocode({'location': position},function(results, status){
			        if (status == soso.maps.GeocoderStatus.OK) {
			        	var address = results.addressComponents.district + results.addressComponents.streetNumber;
			            $("#address").val(address);
			        }
				});
				
				
			});
        }
	});



});
