
function preview(img, selection) {
    if(!selection.width || !selection.height)
        return;
    var cur_w = $("#photo").width;
    var cur_h = $("#photo").height;
    var scaleX = 160  / selection.width;
    var scaleY = 160 / selection.height;
//  var size = Math.round((cur_w < cur_h) ? scaleX * cur_w : scaleX * cur_h);
    $('#preview img').css({
        width : Math.round(scaleX * 360) + 'px',
        height : Math.round(scaleY * 360) + 'px',
        marginLeft : '-' + Math.round(scaleX * selection.x1)+ 'px',
        marginTop : '-' + Math.round(scaleY * selection.y1)+ 'px'
        
    });
    $('#x1').val(selection.x1);  
    $('#y1').val(selection.y1);  
    $('#x2').val(selection.x2);  
    $('#y2').val(selection.y2);  
    $('#w').val(selection.width);  
    $('#h').val(selection.height); 
}
