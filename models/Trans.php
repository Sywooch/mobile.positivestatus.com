<?php

namespace app\models;

use app\components\Y;
use Yii;
use app\components\PhotoResizer;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\FileHelper;
use yii\db\Query;


/**
 * This is the model class for table "{{%trans}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $date_int
 * @property integer $cat_id
 * @property integer $brand_id
 * @property integer $model_id
 * @property integer $year
 * @property integer $month
 * @property integer $capacity
 * @property integer $transmiss_id      Коробка передач
 * @property integer $drive_id          Привод
 * @property integer $interior_id       Интерьер
 * @property integer $climate_id        Климат
 * @property integer $fuel_id           Топливо
 * @property integer $category_id       Категория
 * @property integer $wheel_id          Колесная формула
 * @property integer $emission_id       Класс экологической безопасности
 * @property integer $sticker_id        Экологическая наклейка
 * @property integer $cab_id            Кабина
 * @property integer $axle_id           Оси
 * @property integer $bunk_id           Количество спальных мест
 * @property integer $hydraulic_id      Гидравлическое устройство
 * @property integer $length_id         Габаритная длина автомобиля
 * @property integer $licweight_id      Разрешенный вес
 * @property integer $load_id           Грузоподъемность
 * @property integer $seat_id           Количество мест
 * @property integer $length            Длина (для лодок)
 * @property integer motohours          Моточасы
 * @property integer $weight            Вес
 * @property integer $mileage           Пробег
 * @property integer $price_net
 * @property integer $nds
 * @property integer $price_brut
 * @property string $currency
 * @property string $youtube
 * @property string $text_ru
 * @property string $text_de
 * @property integer $click
 * @property integer $pause
 * @property integer $power
 * @property string $mobile_key
 * @property string $details
 */
class Trans extends \yii\db\ActiveRecord {
    // To store slider values
    public $slider_years;       // 2000;2010
    public $slider_prices;      // 2000;25000
    public $country_code;
    public $nds_only;

    public $dummy_property;
    public $contacts = [];
    private $_attrs_for_details = ['contacts'];
    
    public static function tableName() {
        return '{{%trans}}';
    }

    public function scenarios() {
        return array_merge(
            parent::scenarios(),
            ['filter' => ['slider_years', 'slider_prices', 'country_code', 'nds_only',
                'cat_id', 'brand_id', 'model_id', 'transmiss_id', 'fuel_id', 'interior_id',
                'category_id', 'wheel_id', 'climate_id', 'emission_id', 'sticker_id', 'cab_id',
                'axle_id', 'bunk_id', 'hydraulic_id', 'length_id', 'licweight_id', 'load_id', 'seat_id',
                'length', 'motohours'
            ]]
        );
    }

    public function rules() {
        return [
            [['user_id', 'price_net', 'nds', 'price_brut'], 'required'],
            ['cat_id', 'required', 'message' => Yii::t('client', 'SELECT_CATID')],
            [['user_id', 'date_int', 'cat_id', 'brand_id', 'model_id', 'year', 'month', 'capacity', 
                'transmiss_id', 'drive_id', 'interior_id', 'climate_id', 'fuel_id', 'category_id', 
                'wheel_id', 'click', 'pause', 'mileage', 'price_net', 'nds', 'price_brut',
                'weight', 'power', 'emission_id', 'sticker_id', 'cab_id', 'axle_id', 'bunk_id',
                'hydraulic_id', 'length_id', 'licweight_id', 'load_id', 'seat_id', 'length', 'motohours'], 'integer'],
            [['youtube', 'text_ru', 'text_de', 'currency'], 'string'],
            [['youtube', 'text_ru', 'text_de'], 'filter', 'filter' => ['yii\helpers\Html', 'encode']],
            [['slider_years', 'slider_prices', 'country_code', 'nds_only'], 'safe'],
            ['contacts', 'arrayValidator', 'skipOnEmpty' => true],
        ];
    }

    public function arrayValidator($attribute, $params) {
        foreach ($this->$attribute as $k => $v) {
            $this->{$attribute}[$k] = substr(trim($v), 0, 20);
        }

        $this->$attribute = array_diff($this->$attribute, ['']);
    }

    public function attributeLabels() {
        return [
            'date_int' => Yii::t('site', 'DATE'),
            'date' => Yii::t('site', 'DATE'),
            'cat_id' => Yii::t('admin', 'LABEL_CATID'),
            'brand_id' => Yii::t('admin', 'LABEL_BRANDID'),
            'model_id' => Yii::t('admin', 'LABEL_MODEL'),
            'year' => Yii::t('site', 'YEAR'),
            'month' => Yii::t('site', 'MONTH'),
            'capacity' => Yii::t('client', 'CAPACITY'),
            'transmiss_id' => Yii::t('client', 'TRANSMISS_ID'),
            'drive_id' => Yii::t('client', 'DRIVE_ID'),
            'interior_id' => Yii::t('client', 'INTERIOR_ID'),
            'climate_id' => Yii::t('client', 'CLIMATE_ID'),
            'fuel_id' => Yii::t('client', 'FUEL_ID'),
            'category_id' => Yii::t('client', 'CATEGORY_ID'),
            'wheel_id' => Yii::t('client', 'WHEEL_ID'),
            'emission_id' => Yii::t('client', 'EMISSION_ID'),
            'sticker_id' => Yii::t('client', 'STICKER_ID'),
            'cab_id' => Yii::t('client', 'CAB_ID'),
            'axle_id' => Yii::t('client', 'AXLE_ID'),
            'bunk_id' => Yii::t('client', 'BUNK_ID'),
            'hydraulic_id' => Yii::t('client', 'HYDRAULIC_ID'),
            'length_id' => Yii::t('client', 'LENGTH_ID'),
            'licweight_id' => Yii::t('client', 'LICWEIGHT_ID'),
            'load_id' => Yii::t('client', 'LOAD_ID'),
            'seat_id' => Yii::t('client', 'SEAT_ID'),
            'length' => Yii::t('client', 'LENGTH'),
            'motohours' => Yii::t('client', 'MOTORHOURS'),
            'weight' => Yii::t('client', 'WEIGHT'),
            'mileage' => Yii::t('client', 'MILEAGE'),
            'price_net' => Yii::t('client', 'PRICE_NET'),
            'price_brut' => Yii::t('client', 'PRICE_BRUT'),
            'power' => Yii::t('client', 'POWER'),
            'nds' => Yii::t('client', 'NDS'),
            'youtube' => Yii::t('client', 'YOUTUBE'),
            'text_ru' => Yii::t('client', 'TEXT_RU'),
            'text_de' => Yii::t('client', 'TEXT_DE'),
            'pause' => Yii::t('client', 'PAUSE'),
            'dummy_property' => Yii::t('client', 'DUMMY_PROP'),
        ];
    }
    
    public function getDate() {
        return Yii::$app->formatter->asDate($this->date_int, 'short');
    }
    
    public function getCurr() {
        if ($this->currency == 'EUR')       return '&euro;';
        if ($this->currency == 'USD')       return '$';
        if ($this->currency == 'GBP')       return '&pound;';
        return $this->currency;
    }
    
    public function getPriceBrut() {
        return round($this->price_net * ($this->nds+100) / 100);
    }
    
    public function getCat() {
        return $this->hasOne(TransCat::className(), ['id' => 'cat_id']);
    }
    
    public function getBrand() {
        return $this->hasOne(TransBrand::className(), ['id' => 'brand_id']);
    }
    
    public function getModel() {
        return $this->hasOne(TransModel::className(), ['id' => 'model_id']);
    }
    
    public function getTransmission() {
        return $this->hasOne(TransFeature::className(), ['id' => 'transmiss_id']);
    }  
    
    public function getDrive() {
        return $this->hasOne(TransFeature::className(), ['id' => 'drive_id']);
    }       
    
    public function getInterior() {
        return $this->hasOne(TransFeature::className(), ['id' => 'interior_id']);
    }   
    
    public function getClimate() {
        return $this->hasOne(TransFeature::className(), ['id' => 'climate_id']);
    }
    
    public function getFuel() {
        return $this->hasOne(TransFeature::className(), ['id' => 'fuel_id']);
    }
    
    public function getCategory() {
        return $this->hasOne(TransFeature::className(), ['id' => 'category_id']);
    }

    public function getWheel() {
        return $this->hasOne(TransFeature::className(), ['id' => 'wheel_id']);
    }

    public function getEmission() {
        return $this->hasOne(TransFeature::className(), ['id' => 'emission_id']);
    }

    public function getSticker() {
        return $this->hasOne(TransFeature::className(), ['id' => 'sticker_id']);
    }

    public function getCab() {
        return $this->hasOne(TransFeature::className(), ['id' => 'cab_id']);
    }

    public function getAxle() {
        return $this->hasOne(TransFeature::className(), ['id' => 'axle_id']);
    }

    public function getBunk() {
        return $this->hasOne(TransFeature::className(), ['id' => 'bunk_id']);
    }

    public function getHydraulic() {
        return $this->hasOne(TransFeature::className(), ['id' => 'hydraulic_id']);
    }

    public function getLengthidval() {
        return $this->hasOne(TransFeature::className(), ['id' => 'length_id']);
    }

    public function getLicweight() {
        return $this->hasOne(TransFeature::className(), ['id' => 'licweight_id']);
    }

    public function getLoad() {
        return $this->hasOne(TransFeature::className(), ['id' => 'load_id']);
    }

    public function getSeat() {
        return $this->hasOne(TransFeature::className(), ['id' => 'seat_id']);
    }

    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
    
    public function getBookmarks() {
        return $this->hasMany(Bookmark::className(), ['trans_id' => 'id']);
    }

    public function getBookmarkExists() {
        return $this->hasMany(Bookmark::className(), ['trans_id' => 'id'])
            ->where(['user_id' => Yii::$app->user->id]);
    }

    public function getFullName() {
        return $this->brand['name'] .' ' .$this->model['name'];
    }



    
    ////////////////////////////////////////////////////////////////////////////
    //              EVENTS
    public function beforeSave($insert) {
        if (!parent::beforeSave($insert)) 
            return false;
        
        if ($this->isNewRecord) {
            $this->date_int = time();
        }
        
        $this->weight = empty($this->weight) ? 0 : $this->weight;
        $this->mileage = empty($this->mileage) ? 0 : $this->mileage;
        $this->capacity = empty($this->capacity) ? 0 : $this->capacity;
        $this->price_net = empty($this->price_net) ? 0 : $this->price_net;
        $this->nds = empty($this->nds) ? 0 : $this->nds;
        $this->price_brut = empty($this->price_brut) ? 0 : $this->price_brut;
        $this->power = empty($this->power) ? 0 : $this->power;
        $this->length = empty($this->length) ? 0 : $this->length;
        $this->motohours = empty($this->motohours) ? 0 : $this->motohours;

        $data = array();
        foreach($this->_attrs_for_details as $attribute)
            if (!empty($this->{$attribute}))
                $data[$attribute] = $this->{$attribute};

        $this->details = serialize($data);

        return true;
    }
    
    // Update data for sliders (TransCat minYear, maxYear, minPrice, maxPrice) 
    // Brand amounts (TransBrand.cnt) and User proposal amount (User.cnt)
    public function afterSave($insert, $changedAttributes) {
//        // TransCat minYear, maxYear, minPrice, maxPrice
//        $action = $insert ? 'insert' : 'update';
//        $transCat = TransCat::findOne($this->cat_id);
//        $transCat->updateGlobals($this, $action, $changedAttributes);

        // User.cnt
        if ($insert)
            User::updateAllCounters(['cnt' => 1], ['id' => $this->user_id]);
        
        parent::afterSave($insert, $changedAttributes);
    }

    // Update data for sliders (TransCat minYear, maxYear, minPrice, maxPrice) 
    // Brand amounts (TransBrand.cnt) and User proposal amount (User.cnt)
    // Delete all photos
    public function afterDelete() {
//        // TransCat minYear, maxYear, minPrice, maxPrice
//        $transCat = TransCat::findOne($this->cat_id);
//        $transCat->updateGlobals($this, 'delete');

       // User.cnt
        User::updateAllCounters(['cnt' => -1], ['id' => $this->user_id]);
        
        // Delete all photos
        $photo_folder = self::getPhotoDir($this->id, false, false);
        if (file_exists($photo_folder));
            FileHelper::removeDirectory($photo_folder);
            
        // Delete Bookmarks
        foreach ($this->bookmarks as $bookmark)
            $bookmark->delete();

        parent::afterDelete();
    }

    public function afterFind() {
        if (!empty($this->details))
            foreach(unserialize($this->details) as $attribute => $value)
                if(in_array($attribute, $this->_attrs_for_details))
                    $this->{$attribute} = $value;

        parent::afterFind();
    }

    private function updateGlobalCounters($transCat, $step) {
        $transCat->updateCounters(['cnt' => $step]);
        
        // TransBrand.cnt
        $transBrand = TransBrand::findOne($this->brand_id);
        $transBrand->updateCounters(['cnt' => $step]);
        
//        // User.cnt
//        $user = User::findOne($this->user_id);
//        $user->updateCounters(['cnt' => $step]);
    }
    
    
    
    ////////////////////////////////////////////////////////////////////////////
    //      Other functions
    public static function countUserTranses($user_id=false) {
        if (!$user_id) {
            if (Yii::$app->user->isGuest)
                return 0;
            else 
                $user_id = Yii::$app->user->id;
        }
        
        return (new Query())->from(self::tableName())
            ->where('user_id=:user_id', [':user_id' => $user_id])
            ->count();
    }    
    
    
    public static function getYearDropdownItems() {
        $minYear = Yii::$app->params['minYear'];
        $maxYear = date('Y') + 1;
        $ret = [];
        
        for ($y = $minYear; $y <= $maxYear; $y++)
            $ret[$y] = $y;
        
        return $ret;
    }
    
    public static function getMonthDropdownItems() {
        return [
            1=>'01', 2=>'02', 3=>'03', 4=>'04', 5=>'05', 6=>'06',
            7=>'07', 8=>'08', 9=>'09', 10=>'10', 11=>'11', 12=>'12',
        ];
    }
    
    
    // When $tmp=true - we use @imgTmp alias instead of @photo and $user_id instead of $trans_id
    public static function getPhotoDir($trans_id, $tmp) {
        $photo_path = self::getPhotoPath($trans_id, $tmp);
        return Yii::getAlias('@webroot') .$photo_path;
    }   
    
    public static function getPhotoUrl($trans_id, $tmp, $scheme  = false) {
        $photo_path = self::getPhotoPath($trans_id, $tmp);
        return Url::base($scheme) .$photo_path;
    }
    
    public static function getPhotoPath($trans_id, $tmp) {
        $photo_path = $tmp ? Yii::getAlias('@imgTmp') : Yii::getAlias('@photo');
        $photo_path .= '/' .str_pad((int)$trans_id, 7, '0', STR_PAD_LEFT) .'/';

        $full_path = Yii::getAlias('@webroot') .$photo_path;
        if (!is_dir($full_path))
            mkdir($full_path);

        return $photo_path;
    }
    
    
    // When $tmp=true - we use @imgTmp alias instead of @photo and $user_id instead of $trans_id
    // Mainphoto filename starts from "main_sm_" OR "main_lg_". $prefix could be 'lg_' or 'sm_'
    public static function getMainPhotoUrl($trans_id, $prefix, $tmp = false) {
        $dir = self::getPhotoDir($trans_id, $tmp);
        $files = glob($dir .'main_' .$prefix .'*.*');       // Here must be only single file
        return empty($files) ? '' : self::getPhotoUrl($trans_id, $tmp) .basename($files[0]);
    }
    
    // When $tmp=true - we use @imgTmp alias instead of @photo and $user_id instead of $trans_id
    // $prefix could be 'lg_' or 'sm_'
    public static function getPhotoUrls($trans_id, $prefix, $tmp = false) {
        // There must be only single file
        $dir = self::getPhotoDir($trans_id, $tmp);
        $files = array_merge(glob($dir. 'main_' .$prefix .'*.*'), glob($dir .$prefix .'*.*'));
        $ret = [];
        
        if (!empty($files)) {
            $url = self::getPhotoUrl($trans_id, $tmp);

            foreach ($files as $file)
                $ret[] = $url .basename($file);
        }

        return $ret;
    }


    // USED IN Synchronizer.php
    // $srcFile - Filename with full path or Url
    // $destDir - Local directory,
    // $destFileName - Filename and extension, without the path
    public static function savePhoto($srcFile, $trans_id, $tmp, $step=1) {
        $destDir = Trans::getPhotoDir($trans_id, $tmp);
        if ($step==0 && file_exists($destDir))
            FileHelper::removeDirectory(rtrim($destDir, 'main/'));

        $ext = pathinfo($srcFile, PATHINFO_EXTENSION);
        $ext = empty($ext) ? '' : '.'.strtolower($ext);
        $destFileName = uniqid().$ext ;
        $main_prefix = ($step == 0) ? 'main_' : '';

        // Big Photo (prefix "lg_" for $destFile)
        $destFile = $destDir .$main_prefix .'lg_' .$destFileName;
        $width = Yii::$app->params['photoBig']['width'];
        $height = Yii::$app->params['photoBig']['height'];
        $resizer = new PhotoResizer($srcFile, $destFile, $width, $height, 2);
        if (!$resizer->resize())
            return false;

        // Small Photo (prefix "sm_" for $destFile)
        $destFile = $destDir .$main_prefix .'sm_' .$destFileName;
        $width = Yii::$app->params['photoSmall']['width'];
        $height = Yii::$app->params['photoSmall']['height'];
        $resizer = new PhotoResizer($srcFile, $destFile, $width, $height, 2);
        return $resizer->resize();
    }
    
    
    // An array keeps not empty values only
    public static function getDescriptions($model) {
        $lang = Yii::$app->language;
        $ret = [];
        $ret[Yii::t('client', 'YEAR')]      = $model->year .'-' .str_pad($model->month, 2, '0', STR_PAD_LEFT);
        $ret[Yii::t('client', 'MILEAGE')]   = number_format($model->mileage, 0, '.', ' ') .' km';
        
        if (isset($model->fuel) && !empty($model->capacity))            
            $ret[Yii::t('client', 'ENGINE')] = $model->capacity . ' ' .$model->fuel[$lang];
        if (isset($model->fuel) && empty($model->capacity))            
            $ret[Yii::t('client', 'ENGINE')] = $model->fuel[$lang];        
        if (isset($model->transmission))    $ret[Yii::t('client', 'TRANSMISS_ID')] = $model->transmission[$lang];
        if (isset($model->drive))           $ret[Yii::t('client', 'DRIVE_ID')] = $model->drive[$lang];
        if (isset($model->interior))        $ret[Yii::t('client', 'INTERIOR_ID')] = $model->interior[$lang];
        if (isset($model->climate))         $ret[Yii::t('client', 'CLIMATE_ID')] = $model->climate[$lang];
        if (isset($model->category))        $ret[Yii::t('client', 'CATEGORY_ID')] = $model->category[$lang];
        if (isset($model->wheel))           $ret[Yii::t('client', 'WHEEL_ID')] = $model->wheel[$lang];
        if (isset($model->emission))        $ret[Yii::t('client', 'EMISSION_ID')] = $model->emission[$lang];
        if (isset($model->sticker))         $ret[Yii::t('client', 'STICKER_ID')] = $model->sticker[$lang];
        if (isset($model->cab))             $ret[Yii::t('client', 'CAB_ID')] = $model->cab[$lang];
        if (isset($model->axle))            $ret[Yii::t('client', 'AXLE_ID')] = $model->axle[$lang];
        if (isset($model->bunk))            $ret[Yii::t('client', 'BUNK_ID')] = $model->bunk[$lang];
        if (isset($model->hydraulic))       $ret[Yii::t('client', 'HYDRAULIC_ID')] = $model->hydraulic[$lang];
        if (isset($model->lengthidval))     $ret[Yii::t('client', 'LENGTH_ID')] = $model->lengthidval[$lang];
        if (isset($model->licweight))       $ret[Yii::t('client', 'LICWEIGHT_ID')] = $model->licweight[$lang];
        if (isset($model->load))            $ret[Yii::t('client', 'LOAD_ID')] = $model->load[$lang];
        if (isset($model->seat))            $ret[Yii::t('client', 'SEAT_ID')] = $model->seat[$lang];
        if (!empty($model->length))         $ret[Yii::t('client', 'LENGTH')] = $model->length;
        if (!empty($model->motohours))      $ret[Yii::t('client', 'MOTOHOURS')] = $model->motohours;
        
        return $ret;
    }
    public static function getDescriptionsGroup($model) {
        $lang = Yii::$app->language;
        $bet = [];
        $fre = [];
        $sal = [];
        $typ = [];
        $ret = [];
        $bet[Yii::t('client', 'YEAR')]      = $model->year .'-' .str_pad($model->month, 2, '0', STR_PAD_LEFT);
        $bet[Yii::t('client', 'MILEAGE')]   = number_format($model->mileage, 0, '.', ' ') .' km';
        
        if (isset($model->fuel) && !empty($model->capacity))            
            $fre[Yii::t('client', 'ENGINE')] = $model->capacity . ' ' .$model->fuel[$lang];
        if (isset($model->fuel) && empty($model->capacity))            
            $fre[Yii::t('client', 'ENGINE')] = $model->fuel[$lang];
        if (isset($model->transmission))    $fre[Yii::t('client', 'TRANSMISS_ID')] = $model->transmission[$lang];
        if (isset($model->drive))           $ret[Yii::t('client', 'DRIVE_ID')] = $model->drive[$lang];
        if (isset($model->interior))        $sal[Yii::t('client', 'INTERIOR_ID')] = $model->interior[$lang];
        if (isset($model->climate))         $sal[Yii::t('client', 'CLIMATE_ID')] = $model->climate[$lang];
        if (isset($model->category))        $typ[Yii::t('client', 'CATEGORY_ID')] = $model->category[$lang];
        if (isset($model->wheel))           $ret[Yii::t('client', 'WHEEL_ID')] = $model->wheel[$lang];
        if (isset($model->emission))        $typ[Yii::t('client', 'EMISSION_ID')] = $model->emission[$lang];
        if (isset($model->sticker))         $ret[Yii::t('client', 'STICKER_ID')] = $model->sticker[$lang];
        if (isset($model->cab))             $ret[Yii::t('client', 'CAB_ID')] = $model->cab[$lang];
        if (isset($model->axle))            $ret[Yii::t('client', 'AXLE_ID')] = $model->axle[$lang];
        if (isset($model->bunk))            $ret[Yii::t('client', 'BUNK_ID')] = $model->bunk[$lang];
        if (isset($model->hydraulic))       $ret[Yii::t('client', 'HYDRAULIC_ID')] = $model->hydraulic[$lang];
        if (isset($model->lengthidval))     $ret[Yii::t('client', 'LENGTH_ID')] = $model->lengthidval[$lang];
        if (isset($model->licweight))       $ret[Yii::t('client', 'LICWEIGHT_ID')] = $model->licweight[$lang];
        if (isset($model->load))            $ret[Yii::t('client', 'LOAD_ID')] = $model->load[$lang];
        if (isset($model->seat))            $ret[Yii::t('client', 'SEAT_ID')] = $model->seat[$lang];
        if (!empty($model->length))         $ret[Yii::t('client', 'LENGTH')] = $model->length;
        if (!empty($model->motohours))      $ret[Yii::t('client', 'MOTOHOURS')] = $model->motohours;
//        $all = [$ret, $fre, $bet];
        array_unshift($ret, $bet, $fre, $sal, $typ);
        return $ret;
    }
    public static function getFullWith() {
        return [
            'brand', 'model', 'transmission', 'drive', 'interior', 'climate', 'fuel', 'category',
            'wheel', 'emission', 'sticker', 'cab', 'axle', 'bunk', 'hydraulic', 'lengthidval',
            'licweight', 'load', 'seat',
        ];
    }



    ////////////////////////////////////////////////////////////////////////////
    //
    static function createPreviewConfig($trans_id, $tmp) {
        $ini_p = $ini_conf = [];   // initialPreview and initialPreviewConfig
        $photo_urls = self::getPhotoUrls($trans_id, 'sm_', $tmp);

        foreach ($photo_urls as $url) {
            $filename = basename($url);
            $ini_p[] = Html::img($url, ['data-filename' => ltrim($filename, 'main_')]);
            $ini_conf[] = (object)['key' => ltrim($filename, 'main_')];
        }

        $result = array('initialPreview' => $ini_p, 'initialPreviewConfig' => $ini_conf);
        return $result;
    }
}
