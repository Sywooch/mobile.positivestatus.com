<?php

namespace app\assets;

use yii\web\AssetBundle;

class GalleryAsset extends AssetBundle
{
    public $basePath = '@webroot/fileupload';
    public $baseUrl = '@web/fileupload';
    public $css = [
        'css/blueimp-gallery.min.css',
        'css/jquery.fileupload.css',
        'css/jquery.fileupload-ui.css'
    ];
    public $js = [
        'js/vendor/jquery.ui.widget.js',
        'js/tmpl.min.js',
        'js/load-image.min.js',
        'js/canvas-to-blob.min.js',
        'js/jquery.blueimp-gallery.min.js',
        'js/jquery.iframe-transport.js',
        'js/jquery.fileupload.js',
        'js/jquery.fileupload-process.js',
        'js/jquery.fileupload-image.js',
        'js/jquery.fileupload-audio.js',
        'js/jquery.fileupload-video.js',
        'js/jquery.fileupload-validate.js',
        'js/jquery.fileupload-ui.js',
        'js/main.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
