$.AdminLTESidebarTweak = {};

$.AdminLTESidebarTweak.options = {
    EnableRemember: true,
};

$(function () {
    "use strict";

    $("body").on("collapsed.pushMenu", function(){
        if($.AdminLTESidebarTweak.options.EnableRemember){
            var toggleState = 'opened';
            if($("body").hasClass('sidebar-collapse')){
                toggleState = 'closed';
            }
            document.cookie = "toggleState="+toggleState;
        }
    });

    $("body").on("expanded.pushMenu", function(){
        if($.AdminLTESidebarTweak.options.EnableRemember){
            var toggleState = 'closed';
            if(!$("body").hasClass('sidebar-collapse')){
                toggleState = 'opened';
            }
            document.cookie = "toggleState="+toggleState;
        }
    });

    if($.AdminLTESidebarTweak.options.EnableRemember){
        var re = new RegExp('toggleState' + "=([^;]+)");
        var value = re.exec(document.cookie);
        var toggleState = (value != null) ? unescape(value[1]) : null;
        if(toggleState == 'closed'){
            $("body").addClass('sidebar-collapse');
        }
    }


    var fileUpload = $('.upload-image-preview');

    fileUpload.each(function(i, obj) {
        let objId = $(obj).attr('id');
        let imgPreview = $(obj).attr('value');
        if (imgPreview) {
            $('#'+objId).after("<img style='background: none' alt='' class='upload-image-previewer' src='"+imgPreview+"'  />")
        }else {
            $('#'+objId).after("<img alt='' class='upload-image-previewer' src='"+imgPreview+"' />")
        }
    });

    fileUpload.change(function(){
        $(this).next('img.upload-image-previewer').remove();
        var file = this.files[0];
        var preview = $('<img alt="" class="upload-image-previewer" />');
        if (file && file.type.match('image.*')) {
            var reader  = new FileReader();
            reader.onloadend = function () {
                preview.attr('src', reader.result);
            };
            reader.readAsDataURL(file);
        }else {
            preview.attr('src', 'img/add-image.png');
        }
        $(this).after(preview);
    });
});
