<?php

/* @var $this yii\web\View */

use kartik\file\FileInput;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Dashboard';
?>
<div class="site-index">
    <div class="body-content">
        <section class="content">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Choose an Image file(.png, .jpg)</h3>
                </div>
                <div class="box-body">
                    <?php
                    echo FileInput::widget([
                        'name' => 'file[]',
                        'options' => [
                            'multiple' => true,
                            'accept' => 'image/*',
                            'id' => 'products_image_id'
                        ],
                        'pluginOptions' => [
                            'showCaption' => false,
                            'showRemove' => false,
                            'showUpload' => false,
                            'browseClass' => 'btn btn-primary',
                            'browseIcon' => '<i class="glyphicon glyphicon-plus-sign"></i> ',
                            'browseLabel' => 'Upload Image',
                            'allowedFileExtensions' => ['jpg', 'png', 'gif'],
                            'previewFileType' => ['jpg', 'png', 'gif'],
                            'initialPreviewAsData' => true,
                            'overwriteInitial' => false,
                            "uploadUrl" => Yii::$app->request->baseUrl. '/index.php?r=site/upload',
//                            'uploadUrl' => Url::to(['site/upload'], true),
                            'theme' => "explorer",
                            'uploadExtraData' => ['uploadToken' => UPLOAD_TOKEN ],
                            'msgUploadBegin' => Yii::t('app', 'Please wait, system is uploading the files'),
                            'msgFilesTooMany' => 'Maximum 15 products Images are allowed to be uploaded.',
                            'dropZoneClickTitle' => '',
                            "uploadAsync" => true,
                            "browseOnZoneClick" => true,
                            'fileActionSettings' => [
                                'showZoom' => false,
                                'showRemove' => true,
                                'showUpload' => false,
                            ],
                            'validateInitialCount' => true,
                            'maxFileCount' => MAX_FILE,
                            'maxFileSize' => MAX_FILE_SIZE, //1mb
                        ],
                        'pluginEvents' => [
                            'filebatchselected' => 'function(event, files) {
                                          $(this).fileinput("upload");
                            
                                          }',
                            'fileuploaded' => 'function(event, data, previewId, index){
                                           //alert( data.response.initialPreviewConfig[0].key);
//                                             const files = data.response.previewUrls
//                                             showImage(files)
                                          }',
                        ],
                    ]);
                    ?>
                </div>
            </div>
        </section>
    </div>
</div>

<!--    <div class="attachment-block clearfix">-->
<!--        <img class="attachment-img" src="../dist/img/photo1.png" alt="Attachment Image">-->
<!---->
<!--        <div class="attachment-pushed">-->
<!--            <h4 class="attachment-heading"><a href="http://www.lipsum.com/">Lorem ipsum text generator</a></h4>-->
<!--        </div>-->
<!--    </div>-->

<?php
$script = <<< JS
   // initialize array    
var uploaded_images = [];
// function copyToClipboard(element) {
//     var temp = $("<input>");
//     $("body").append(temp);
//     temp.val($(element).html()).select();
//     document.execCommand("copy");
//     temp.remove();
// }
$(document).on('click', '.btnCopy', function(){
      var temp = $("<input>");
    $("body").append(temp);
    // console.log($(this).closest('div.row_link').find('input').val())
    temp.val($(this).closest('div.row_link').find('input').val()).select();
    document.execCommand("copy");
    temp.remove();
})
// $('.btnCopy').click(function() {
//     var temp = $("<input>");
//     $("body").append(temp);
//     console.log($(this).parent('div.row_link').find('input'))
//     temp.val($(this).parent('div.row_link').find('input').html()).select();
//     document.execCommand("copy");
//     temp.remove();
//
// })

// function showImage(images) {
//   let html = "<ul class='attachment-block clearfix'>"
//      images.map(name => 
//         html += '<li data-url=' + name + '>' +
//                 '<img class="attachment-img" src='+ name +' alt="Attachment Image"> ' +
//                  '<div class="attachment-pushed"><span> Click here to copy link</span></div>' +
//                   '</li>'
//         )
//      html += "</ul>"
//      $(".link-image").html(html);
// }
JS;
$this->registerJs($script);