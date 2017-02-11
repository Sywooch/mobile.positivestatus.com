<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/main.css',
        'css/style2.css',
        'css/responsive2.css',
        'css/responsive-nav.css',
        'css/jslider.css',
        'css/jquery.datetimepicker.css'     // Profile
    ];
    public $js = [
        'js/start.js',
        'js/adap_m.js',
        'js/main.js',
        'js/jshashtable-2.1_src.js',
        'js/jquery.numberformatter-1.2.3.js',
        'js/tmpl.js',
        'js/jquery.dependClass-0.1.js',
        'js/draggable-0.1.js',
        'js/jquery.dd.min.js',
        'js/jquery.scrollTo.min.js',
        'js/responsive-nav.js',
        'js/jquery.slider.js',
        'js/jquery.sticky.js',      // Index
        'js/fotorama.js',                   // Pay, Profile
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset',
        //'yii\bootstrap\BootstrapAsset',
    ];
}
