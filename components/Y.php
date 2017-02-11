<?php
namespace app\components;

use app\models\User;
use Yii;
use kop\y2sp\ScrollPager;
use yii\captcha\Captcha;
use yii\helpers\Html;


class Y {
    public static $no_avatar = 'no-avatar.png';
    public static $no_trans_photo = '/img/no-photo.jpg';

    public static function getAvatarSize() {
        return ['width' => 110, 'height' => 110];
    }

    public static function getAvatarDir() {
        $dir = Y::implodePieces(['@webroot', '@avatar']);
        if(!is_dir($dir))
            mkdir($dir);

        return $dir;
    }

    public static function getAvatarFile($contact_id, $noavatar = true) {
        $glob_pattern = Y::getAvatarDir() .DIRECTORY_SEPARATOR .Y::getStrpadFromId($contact_id) .'.*';
        $av = glob($glob_pattern);

        if (!empty($av))
            return basename($av[0]);
        elseif ($noavatar)
            return Y::$no_avatar;
        else
            return false;
    }

    public static function getAvatarUrl() {
        return Y::implodePieces(['@web', '@avatar', '/']);
    }

    public static function getGalleryDir($trans_id) {
        $dir = Y::implodePieces(['@webroot', '@photo', Y::getStrpadFromId($trans_id), '/']);
        if(!is_dir($dir))
            mkdir($dir);

        return $dir;
    }

    public static function getGalleryUrl($trans_id) {
        return Y::implodePieces(['@web', '@photo', Y::getStrpadFromId($trans_id) .'/']);
    }

    public static function getGalleryFiles($trans_id) {
        $dir = Y::getGalleryDir($trans_id);
        $files = glob($dir .'*.*');
        return empty($files) ? [] : array_map(function($val) {return basename($val);}, $files);
    }

    public static function getTempDir() {
        $dir = Y::implodePieces(['@webroot', '@imgTmp']);
        if(!is_dir($dir))
            mkdir($dir);

        return $dir;
    }

    public static function getTempUrl() {
        return Y::implodePieces(['@web', '@imgTmp']);
    }


    /**
     * @param $pieces - an array, every piece can be either string or alias
     * @return string - imploded pieces, aliases handled with getAlias.
     */
    public static function implodePieces($pieces) {
        $result = '';

        foreach ($pieces as $piece)
            if (!empty($piece))
                $result .= ($piece[0] == '@') ? Yii::getAlias($piece) : $piece;

        return str_replace('\\', '/', $result);
    }


    /**
     * @param $id
     * @return str_pad($id, 7, '0', STR_PAD_LEFT)
     */
    public static function getStrpadFromId($id) {
        return str_pad($id, 7, '0', STR_PAD_LEFT);
    }
    public static function getUserPaymentId($id) {
        $user = User::findOne($id);
        $register_date = date("dmy",$user->registered_at);

        return str_pad(sprintf("%03d", $id), 9, $register_date, STR_PAD_LEFT);
    }
    public static function getPaymentId($id) {
        $user = User::findOne($id);
        $date = date("dmy");

        return str_pad(sprintf("%03d", $id), 9, $date, STR_PAD_LEFT);
    }


    public static function getPayDetails() {
        return [
            User::ACCOUNT_BUSINESS => [
                'summa' => 50,
                'd' => 30,          // parameters for mktime()
                'm' => false,       // Business Account costs 50 euro for 30 days
                'y' => false        // from payment date
            ],
        ];
    }


    ////////////////////////////////////////////////////////////////////////////
    //                  OTHER
    // Remove all leading and trailing slashes, add  trailing slash
    public static function normalizeSubDir($subdir) {
        if (!is_string($subdir) || empty($subdir))
            return '';
        else
            return trim($subdir, '/\\') .'/';        
    }
	
    public static function disableLogs() {
        foreach (Yii::$app->log->targets as $target) {
            $target->enabled = false; 
        }
    }

    public static function getArErrors($model) {
        $model_errs = '';

        foreach ($model->errors as $attr)
            foreach ($attr as $err_mess)
                $model_errs .= $err_mess ."<br />";

        return $model_errs;
    }


    public static function getPagerSettings($settings = []) {
        $set = [
            'class' => \kop\y2sp\ScrollPager::className(),
            'noneLeftText' => '',
            'negativeMargin' => 10,
            'container' => '.list-view',
            'item' => '.item',
            'enabledExtensions' => [
                ScrollPager::EXTENSION_SPINNER,
                ScrollPager::EXTENSION_NONE_LEFT,
                ScrollPager::EXTENSION_PAGING,
            ],
        ];

        return array_merge($set, $settings);
    }


    public static function getYearDropDownHtml($start = 2000, $end = null, $order = 'DESC') {
        $html = '';
        if (empty($end))
            $end = date('Y');

        if (strtoupper($order) == 'ASC') {
            for ($n = $start; $n <= $end; $n++) {
                $link = Html::a($n, '#', ['data-id' => $n]);
                $html .= '<li>' .$link .'</li>';
            }
        }
        else {
            for ($n = $end; $n >= $start; $n--) {
                $link = Html::a($n, '#', ['data-id' => $n]);
                $html .= '<li>' .$link .'</li>';
            }
        }

        return $html;
    }


    public static function getMonthDropDownHtml() {
        $html = '';
        for ($n = 1; $n <= 12; $n++) {
            $link = Html::a(Yii::t('site', 'MONTH' .$n), '#', ['data-id' => $n]);
            $html .= '<li>' .$link .'</li>';
        }

        return $html;
    }


    public static function showCaptcha() {
        return Captcha::checkRequirements();
    }
}
