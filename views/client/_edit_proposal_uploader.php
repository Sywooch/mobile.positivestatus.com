<?php
    // Parameters:
    // $model  - new Trans();
    use app\models\Trans;
    use yii\helpers\Json;
    use yii\helpers\Html;
    use yii\helpers\Url;

    app\assets\KartikFileinputAsset::register($this);
    $trans_id = $model->isNewRecord ? $model->user_id : $model->id;
    $tmp = $model->isNewRecord ? 1 : 0;
?>


    <div class="files">
        <div class="floatl borde">
            <p> <?= Yii::t('client', 'UPLOADER_TEXT1') ?> </p>
            <div class="file-upload">
                <label id="uploader_dummy_button">
                    <!--input type="file" name="file"-->
                    <span> <?= Yii::t('site', 'UPLOAD') ?> </span>
                </label>
            </div>

            <p> <?= Yii::t('client', 'UPLOADER_TEXT2') ?> </p>
            <div style="text-align:center"><img src="/img/down_arrow.png" /></div>
            <!--input type="text" id="filename" class="filename" disabled-->
        </div>

        <div id="mainphoto_cnt" class="floatl foto2">
            <?php $mainphoto_url = Trans::getMainPhotoUrl($trans_id, 'sm_', $tmp); ?>
            <?php echo empty($mainphoto_url) ? '' : Html::img($mainphoto_url); ?>
        </div>
    </div>  <!--class="files"  -->

    <div id="fileinput_cnt" class="fotos">
        <?= Html::fileInput('trans_photos[]', '', array('multiple' => true, 'id' => 'fileinput')) ?>
    </div>  <!--class="fotos" -->



<?php if (!Yii::$app->request->isPjax) : ?>
<?php $this->beginCss(); ?>
<style>
    .file-preview-frame {padding: 0; box-shadow: none; margin: 0 3px 0 0; display: block; height: auto;}
    .file-preview-frame img {margin: 0; }
    .file-actions {margin: 0;}
    .checkbox label {padding-left: 0;}
    .checkbox {bottom: 0 !important;}
</style>
<?php $this->endCss(); ?>
<?php endif; ?>


<?php
    // Fileinput parameters for js-code below (initialPreview & initialPreviewConfig)
    $fi_params = Trans::createPreviewConfig($trans_id, $tmp);
?>


<?php if (!Yii::$app->request->isPjax) : ?>
<?php $this->beginJs(); ?>
<script>
    var fileinput_params = {
        initialPreview: <?= Json::encode($fi_params['initialPreview']) ?>,
        initialPreviewConfig: <?= Json::encode($fi_params['initialPreviewConfig']) ?>,
        overwriteInitial: false,
        initialPreviewShowDelete: true,
        deleteUrl: "<?= Url::to(['/auxx/delete-photo']) ?>",
        deleteExtraData: {trans_id: "<?= $trans_id ?>", tmp: "<?= $tmp ?>"},
        dropZoneTitle: "",
        uploadUrl: "<?= Url::to(['/auxx/upload-photos']) ?>",
        uploadExtraData: {trans_id: "<?= $trans_id ?>", tmp: "<?= $tmp ?>"},
        allowedPreviewMimeTypes: ["image/jpeg", "image/pjpeg", "image/png"],
        allowedFileExtensions: ['jpg', 'jpeg', 'png'],
        allowedFileTypes: ['images'],
        showRemove: false,
        showUpload: false,
        showClose: false,
        showCaption: false,
        maxFileSize: 5*1024,
        minFileCount: 0,
        maxFileCount: 30,
        maxImageWidth: <?= Yii::$app->params['photoBig']['width'] ?>,
        maxImageHeight: <?= Yii::$app->params['photoBig']['height'] ?>,
        resizeImage: true,
        uploadAsync: false,
        validateInitialCount: true,
        layoutTemplates: {
            main2: '{preview}\n<div class="kv-upload-progress hide"></div>\n',
            preview: '<div class="file-preview {class}">\n' +
            '    <div class="{dropClass}">\n' +
            '    <div class="file-preview-thumbnails fotos">\n' +
            '    </div>\n' +
            '    <div class="clearfix"></div>' +
            '    <div class="file-preview-status text-center text-success"></div>\n' +
            '    <div class="kv-fileinput-error"></div>\n' +
            '    </div>\n' +
            '</div>',
            footer: '<div class="file-thumbnail-footer">\n' +
            '    {actions}\n' +
            '</div>',
            actions: '<div class="file-actions">\n' +
            '    <div class="file-footer-buttons">\n' +
            '        {delete}' +
            '    </div>\n' +
            '    <div class="clearfix"></div>\n' +
            '</div>',
            actionDelete: '<i class="abcd kv-file-remove" title="{removeTitle}"{dataUrl}{dataKey}></i>\n',
        },
        previewTemplates: {
            generic: '<div class="floatl car_f file-preview-frame" id="{previewId}" data-fileindex="{fileindex}">\n' +
            '   {footer}\n' +
            '   {content}\n' +
            '   <div class="checkbox">\n' +
            '       <input type="radio" value="foto" name="foto" id="radio_{previewId}">\n' +
            '       <label for="radio_{previewId}">\n' +
            '           <span class="pseudo-checkbox white"></span>\n' +
            '           <span class="label-text"> <?= Yii::t('client', 'MAIN_PHOTO') ?> </span>\n' +
            '       </label>\n' +
            '   </div>\n' +
            '</div>',
            image: '<div class="floatl car_f file-preview-frame" id="{previewId}" data-fileindex="{fileindex}">\n' +
            '   {footer}\n' +
            '   <img src="{data}" title="{caption}" alt="{caption}" />\n' +
            '   <div class="checkbox">\n' +
            '       <input type="radio" value="foto" name="foto" id="radio_{previewId}">\n' +
            '       <label for="radio_{previewId}">\n' +
            '           <span class="pseudo-checkbox white"></span>\n' +
            '           <span class="label-text"> <?= Yii::t('client', 'MAIN_PHOTO') ?> </span>\n' +
            '       </label>\n' +
            '   </div>\n' +
            '</div>',
        }
    };

    $("#fileinput").fileinput(fileinput_params)
        .on("filebatchselected", function(e, files) {
            $("#fileinput_cnt div.alert").remove();
            $(this).fileinput("upload");
        })
        .on("filepredelete", function(e, key, jqXHR) {
            if (!confirm("<?= Yii::t('client', 'DELETE_PHOTO') ?>?"))
                return {data: "dummy data"};
        })
        .on("filebatchuploadsuccess", function(e, data) {
            if (data.response.message !== undefined)
                alert(data.response.message);
        })
        .on("filebatchuploadcomplete", function(e, files, extra) {
            $("#fileinput_cnt button.kv-file-remove").removeClass("disabled");

            var mp_filename = getMainphotoFilename();
            if (mp_filename == '')
                selectMainImage();
            else
                $("#fileinput_cnt img[data-filename='" +mp_filename +"']").next("div.checkbox").children("[type=radio]").prop("checked", true);
        })
        .on("fileuploaderror", function(e, data) {
            if (data.id !== undefined) {
                $("#"+data.id).remove();
            }
        })
        .on('filedeleted', function(event, key) {
            if ($("#fileinput_cnt img").length == 0) {
                $("#mainphoto_cnt").html("");
                return true;
            }

            if (getMainphotoFilename() == key) {
                $("#mainphoto_cnt").html("");
                selectMainImage(key);
            }
        });

    function selectMainImage(key) {
        // On photo deletion (on('filedeleted')) image is still exists here. So we can't use
        // $("#fileinput_cnt img").eq(0) if we want to delete first image. So we have to compare
        // key and img.attr("data-filename") first to be sure we delete NOT first image
        var img = $("#fileinput_cnt img").eq(0);        // first image
        if (key != undefined && key == img.attr("data-filename"))
            img = $("#fileinput_cnt img").eq(1);        // second image

        if (img.length == 1)
            img.next("div.checkbox").children("[type=radio]").prop("checked", true).trigger("change");
    }

    // Hide buttons
    $("#fileinput").hide();

    // Dummy click
    $("#uploader_dummy_button, #mobile_uploader_dummy_button").on("click", function(e) {
        e.preventDefault();
        $("#fileinput").click();
    });


    ////////////////////////////////////////////////////////////////////////////////////////
    //                          MAINPHOTO
    // Set Main photo radio checked
    var first_image = $("#fileinput_cnt img").eq(0);
    if (first_image.length == 1 && basename(first_image.attr("src")).substring(0,5) == "main_") {
        first_image.next("div.checkbox").children("[type=radio]").prop("checked", true);
    }

    // Change Mainphoto
    $("body").on("change", "#fileinput_cnt [type=radio]", function() {
        var checked_img = $(this).closest("div.checkbox").prev("img");
        var new_filename = checked_img.attr("data-filename");
        var main_img = $("#mainphoto_cnt img");

        $.ajax({
            url: "<?= Url::to(['/auxx/set-main-photo']) ?>",
            type: "post",
            dataType: "html",
            data: {
                trans_id: "<?= $trans_id ?>",
                tmp: "<?= $tmp ?>",
                new_filename: new_filename
            },
            error: function(a,b,c) {alert(b)},
            success: function(response) {
                if (response == "ok") {
                    // Set Mainphoto
                    var filename = basename(checked_img.attr("src"));
                    var url = checked_img.attr("src").replace(filename, "");
                    if (main_img.length == 0)
                        $("<img />").attr("src", url +"main_" +new_filename).appendTo($("#mainphoto_cnt"));
                    else
                        main_img.attr("src", url +"main_" +new_filename);
                }
            }
        });
    })


    function getMainphotoFilename() {
        if ($("#mainphoto_cnt img").length == 0)
            return '';

        // src = /img/photo/0000044/main_sm_56e0398b52dde.jpg
        var arr = $("#mainphoto_cnt img").attr("src").split("/");
        return arr[arr.length-1].slice(5);      // sm_56e0398b52dde.jpg only
    }
</script>
<?php $this->endJs(); ?>
<?php endif; ?>