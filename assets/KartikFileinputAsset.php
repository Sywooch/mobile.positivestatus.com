<?php

namespace app\assets;

use yii\web\AssetBundle;

class KartikFileinputAsset extends AssetBundle
{
    public $sourcePath = '@vendor//kartik-v/bootstrap-fileinput';
    public $css = [
        'css/fileinput.min.css'
    ];
    public $js = [
        'js/plugins/canvas-to-blob.min.js',
        'js/fileinput.min.js',

    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset'
    ];
}
