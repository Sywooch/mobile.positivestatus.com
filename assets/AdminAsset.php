<?php

    namespace app\assets;

    use yii\web\AssetBundle;

class AdminAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/style2.css',
        'css/responsive-nav.css',
        'css/admin.css',
    ];
    public $js = [
        'js/main.js',
        'js/responsive-nav.js',
    ];
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset',
    ];
}