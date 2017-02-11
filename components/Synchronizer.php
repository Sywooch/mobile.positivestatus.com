<?php

namespace app\components;

use Yii;
use SimpleXMLElement;
use yii\base\Exception;
use yii\web\HttpException;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use app\models\TransCat;
use app\models\TransSubcat;
use app\models\TransBrand;
use app\models\TransModel;
use app\models\TransFeatureH;
use app\models\TransFeature;
use app\models\Trans;


class Synchronizer {
    // All the other URLs are in $mobile_specifics below
    const CLASSES_URL = 'https://services.mobile.de/refdata/classes';   // !!! Mobile.Class = TransCat
    const CUSTOMER_URL = 'https://services.mobile.de/search-api/search?customerId=';
    const AD_URL = 'https://services.mobile.de/search-api/ad/';
    // Next 2 url creating by substitute &CLASS and &MAKE keys
    // MAKES_URL = https://services.mobile.de/refdata/classes/&CLASS_KEY/makes
    // MODELS_URL = https://services.mobile.de/refdata/classes/&CLASS_KEY/makes/&MAKE_KEY/models

    // Full list is here - https://services.mobile.de/manual/search-api.html#XML_schema_files
    public static $mobile_specifics = [
        'ad_fuel' => ['url' => 'https://services.mobile.de/refdata/fuels', 'trans_field' => 'fuel_id'],
        'ad_gearbox' => ['url' => 'https://services.mobile.de/refdata/gearboxes', 'trans_field' => 'transmiss_id'],
        'ad_climatisation' => ['url' => 'https://services.mobile.de/refdata/climatisations', 'trans_field' => 'climate_id'],
        'ad_interior-type' => ['url' => 'https://services.mobile.de/refdata/interiortypes', 'trans_field' => 'interior_id'],
        'ad_driving-cab' => ['url' => 'https://services.mobile.de/refdata/drivingcabs', 'trans_field' => 'body_id'],
        'ad_wheel-formula' => ['url' => 'https://services.mobile.de/refdata/wheelformulas', 'trans_field' => 'wheel_id'],
        'ad_driving-mode' => ['url' => 'https://services.mobile.de/refdata/drivingmodes', 'trans_field' => 'drive_id'],
        'ad_emission-class' => ['url' => 'https://services.mobile.de/refdata/emissionclasses', 'trans_field' => 'emission_id'],
        'ad_emission-sticker' => ['url' => 'https://services.mobile.de/refdata/emissionstickers', 'trans_field' => 'sticker_id'],
        'ad_driving-cab' => ['url' => 'https://services.mobile.de/refdata/drivingcabs', 'trans_field' => 'cab_id'],
    ];
    
    public static $_mobile_keys;
    
    public static function getMobileKeys() {
        if (!isset(self::$_mobile_keys)) {
            $cat = TransCat::find()->select(['CAST("0" as UNSIGNED) as id', 'id as cat_id', 'mobile_key'])
                ->where('mobile_key<>""')->indexBy('mobile_key')->asArray()->all();
            
            $subcat = TransSubcat::find()->select(['id', 'cat_id', 'mobile_key'])
                ->where('mobile_key<>""')->indexBy('mobile_key')->asArray()->all();   
            
            self::$_mobile_keys = ArrayHelper::merge($cat, $subcat);
        }
        
        return self::$_mobile_keys;
    }
    

    ////////////////////////////////////////////////////////////////////////////
    // Update Trans_ tables according to Mobile.de sources
    public static function updateModels() 
    {
        $b =$m = 0;     // New records counters (b - brands, m - models)
        $brand_insert_sql = $model_insert_sql = $ret = '';
        $brand_tbl = TransBrand::tableName();
        $model_tbl = TransModel::tableName();

        // Mobile.Makes => TransBrand. Mobile.Models => TransModel
        foreach (self::getMobileKeys() as $key => $mkey) {
            $cat_id = $mkey['cat_id'];
            $brand_ids = (new Query())->select('id, mobile_key')->from($brand_tbl)->where(['cat_id'=>$cat_id])->indexBy('mobile_key')->all();

            // All brands (makes) are inside category(class).
            // Every Class has its own url
            $makes_url = self::CLASSES_URL .'/' .rawurlencode($key) .'/makes';
            $brand_xml = self::getMobileResponse($makes_url, 'de');
            if (!$brand_xml)
                continue;

            $brand_sxe = new SimpleXMLElement($brand_xml);
            foreach ($brand_sxe->reference_item as $item) {
                $brand_key = (string)$item['key'];
                $brand_name = (string)$item->{'resource_local-description'};
                
                // Update Brand if exists, insert new record if not
                if (!isset($brand_ids[$brand_key])) {
                    $sql = "INSERT INTO $brand_tbl (cat_id, name, mobile_key) VALUES (:cat_id, :name, :mobile_key)";
                    Yii::$app->db->createCommand($sql)->bindValues([':cat_id'=>$cat_id, ':name'=>$brand_name, ':mobile_key'=>$brand_key])->execute();
                    $brand_id = Yii::$app->db->lastInsertId;
                    $b++;
                } 
                else {
                    $brand_insert_sql .= sprintf('(%u, "%s", "%s")', $cat_id, $brand_name, $brand_key) .',';
                    $brand_id = $brand_ids[$brand_key]['id'];
                }
                
                // All models (models) are inside brands(makes).
                // Every Make has its own url         
                $models_url = self::CLASSES_URL .'/' .rawurlencode($key) .'/makes/' .rawurlencode($brand_key) .'/models';
                $model_xml = self::getMobileResponse($models_url, 'de');
                if (!$model_xml)
                    continue;

                $model_sxe = new SimpleXMLElement($model_xml);
                foreach ($model_sxe->reference_item as $item) {
                    $model_key = (string)$item['key'];
                    $model_name = (string)$item->{'resource_local-description'};
                    $model_insert_sql .= sprintf('(%u, "%s", "%s")', $brand_id, $model_name, $model_key) .',';
                }
            }            
        }
      
        if (!empty($brand_insert_sql)) {
            $sql = 'DROP TABLE IF EXISTS tmp_brand; ';
            $sql .= 'CREATE TABLE tmp_brand ('
                .'cat_id INT UNSIGNED, name VARCHAR(100), mobile_key VARCHAR(50),'
                . 'UNIQUE KEY mobile_key (cat_id, mobile_key)'
                . ') ENGINE=MEMORY';
            Yii::$app->db->createCommand($sql)->execute();
        
            $sql = 'INSERT IGNORE INTO tmp_brand (cat_id, name, mobile_key) VALUES ' .trim($brand_insert_sql, ',');
            Yii::$app->db->createCommand($sql)->execute();
            
            $sql = "UPDATE $brand_tbl b SET name = (SELECT name FROM tmp_brand "
                . "WHERE b.cat_id=tmp_brand.cat_id AND b.mobile_key=tmp_brand.mobile_key) "
                . "WHERE EXISTS (SELECT * FROM tmp_brand WHERE b.cat_id=tmp_brand.cat_id AND b.mobile_key=tmp_brand.mobile_key)";
            Yii::$app->db->createCommand($sql)->execute(); 
            Yii::$app->db->createCommand('DROP TABLE tmp_brand')->execute();
        }

        if (!empty($model_insert_sql)) {
            $sql = "INSERT INTO $model_tbl (brand_id, name, mobile_key) "
                . "VALUES " .trim($model_insert_sql, ',')
                . " ON DUPLICATE KEY UPDATE name = VALUES(name)";
            $m = Yii::$app->db->createCommand($sql)->execute();
        }
        
        $ret .= 'New Brands added - ' .$b .'<br />';
        $ret .= 'New Models added - ' .$m;
        return $ret;
    }
    
    
    ////////////////////////////////////////////////////////////////////////////
    // Climate, drive, transmission, interior, fuel, body, wheel
    public static function updateReferences($climate, $drive, $transmission, $interior, $fuel, $body, $wheel, $emission, $sticker, $cab)
    {
        $ret = '';
        
        if ($climate)       $ret .= self::updateReference(self::$mobile_specifics['ad_climatisation']['url'], 'climate_id', 'Climatisations');
        if ($drive)         $ret .= self::updateReference(self::$mobile_specifics['ad_driving-mode']['url'], 'drive_id', 'Driving Modes');
        if ($transmission)  $ret .= self::updateReference(self::$mobile_specifics['ad_gearbox']['url'], 'transmiss_id', 'Gearboxes');
        if ($interior)      $ret .= self::updateReference(self::$mobile_specifics['ad_interior-type']['url'], 'interior_id', 'Interior Types');
        if ($fuel)          $ret .= self::updateReference(self::$mobile_specifics['ad_fuel']['url'], 'fuel_id', 'Fuels');
        if ($body)          $ret .= self::updateReference(self::$mobile_specifics['ad_driving-cab']['url'], 'body_id', 'Driving Cabs');
        if ($wheel)         $ret .= self::updateReference(self::$mobile_specifics['ad_wheel-formula']['url'], 'wheel_id', 'Wheel Formulas');
        if ($emission)      $ret .= self::updateReference(self::$mobile_specifics['ad_emission-class']['url'], 'emission_id', 'Emission Class');
        if ($sticker)       $ret .= self::updateReference(self::$mobile_specifics['ad_emission-sticker']['url'], 'sticker_id', 'Emission Sticker');
        if ($cab)           $ret .= self::updateReference(self::$mobile_specifics['ad_driving-cab']['url'], 'cab_id', 'Driving Cab');

        return $ret;
    }
    
    public static function updateReference($url, $trans_field, $feature_name)
    {
        $cnt = 0;
        $htbl = TransFeatureH::tableName();
        $tbl = TransFeature::tableName();
        $hids = (new Query())->select('id')->from($htbl)->where(['trans_field' => $trans_field])->column();
        
        foreach (Yii::$app->params['langs'] as $lang)
        {
            $xml = self::getMobileResponse($url, $lang);
            $sxe = new SimpleXMLElement($xml); 
            $insert_sql = '';
            
            foreach ($hids as $hid) {
                foreach ($sxe->reference_item as $item) {
                    $key = (string)$item['key'];
                    $name = (string)$item->{'resource_local-description'};
                    $insert_sql .= sprintf('(%u, "%s", "%s")', $hid, $name, $key) .',';
                }  
            }

            if (!empty($insert_sql)) {
                $sql = "INSERT INTO $tbl (hid, $lang, mobile_key) "
                    . "VALUES " .trim($insert_sql, ',')
                    . " ON DUPLICATE KEY UPDATE $lang = VALUES($lang)";
                $exec = Yii::$app->db->createCommand($sql)->execute();

                if ($lang == Yii::$app->params['langs'][0])
                    $cnt += $exec;
            }     
        }
            
        self::correctEmptyNames($tbl);
        return "New $feature_name added - " .$cnt .'<br />';
    }

    
//    public static function updateCategories()
//    {
//        $ret = '';
//        $htbl = TransFeatureH::tableName();
//        $tbl = TransFeature::tableName();
//        $item_names = [];       // To exclude repeated names inside Category
//        $current_cat_id = 0;
//        
//        // Every Category has its own Url
//        foreach (self::getMobileKeys() as $key => $mkey) {
//            $cat_id = $mkey['cat_id'];
//            $subcat_id = $mkey['subcat_id'];
//            
//            $url = self::CLASSES_URL .'/' .$key .'/categories';
//            $hid = (new Query())->select('id')->from($htbl)->where(['cat_id' => $cat_id, 'trans_field' => 'category_id'])->scalar();
//            $cnt = 0;
//            
//            if ($current_cat_id != $cat_id) {
//                $item_names = [];       // Clear the array
//                $current_cat_id = $cat_id;
//            }
//            
//            foreach (Yii::$app->params['langs'] as $lang)
//            {
//                $xml = self::getMobileResponse($url, $lang);
//                $sxe = new SimpleXMLElement($xml); 
//                $insert_sql = '';
//                
//                foreach ($sxe->reference_item as $item) {
//                    $item_name = (string)$item->{'resource_local-description'};
//                    if (in_array($item_name, $item_names)) 
//                        continue; 
//                    
//                    $item_key = (string)$item['key'];
//                    $insert_sql .= sprintf('(%u, "%s", "%s")', $hid, $item_name, $item_key) .',';
//                    $item_names[] = $item_name;
//                }  
//
//                if (!empty($insert_sql)) {
//                    $sql = "INSERT INTO $tbl (hid, $lang, mobile_key) "
//                        . "VALUES " .trim($insert_sql, ',')
//                        . " ON DUPLICATE KEY UPDATE $lang = VALUES($lang)";
//                    $exec = Yii::$app->db->createCommand($sql)->execute();
//
//                    if ($lang == Yii::$app->params['langs'][0])
//                        $cnt += $exec;
//                }     
//            }
//            
//            $ret .= "New $key Categories added - " .$cnt .'<br />';
//        }
//        
//        self::correctEmptyNames($tbl);
//        return $ret;
//    }
    
    public static function updateCategories() {
        $ret = '';
        $htbl = TransFeatureH::tableName();
        $tbl = TransFeature::tableName();
        
        // Every Category has its own Url
        foreach (self::getMobileKeys() as $key => $mkey) {
            $cat_id = $mkey['cat_id'];

            $url = self::CLASSES_URL .'/' .$key .'/categories';
            $hid = (new Query())->select('id')->from($htbl)->where(['cat_id' => $cat_id, 'trans_field' => 'category_id'])->scalar();
            $cnt = 0;
            
            foreach (Yii::$app->params['langs'] as $lang)
            {
                $xml = self::getMobileResponse($url, $lang);
                $sxe = new SimpleXMLElement($xml); 
                $insert_sql = '';
                
                foreach ($sxe->reference_item as $item) {
                    $item_name = (string)$item->{'resource_local-description'};
                    $item_key = (string)$item['key'];
                    $insert_sql .= sprintf('(%u, "%s", "%s")', $hid, $item_name, $item_key) .',';
                }  

                if (!empty($insert_sql)) {
                    $sql = "INSERT INTO $tbl (hid, $lang, mobile_key) "
                        . "VALUES " .trim($insert_sql, ',')
                        . " ON DUPLICATE KEY UPDATE $lang = VALUES($lang)";
                    $exec = Yii::$app->db->createCommand($sql)->execute();

                    if ($lang == Yii::$app->params['langs'][0])
                        $cnt += $exec;
                }     
            }
            
            $ret .= "New $key Categories added - " .$cnt .'<br />';
        }
        
        self::correctEmptyNames($tbl);
        return $ret;
    }
    
    
    public static function correctEmptyNames($transfeature_tbl) {
        $sql = "UPDATE $transfeature_tbl SET ru = de WHERE ru = \"\"; UPDATE $transfeature_tbl SET de = ru WHERE de = \"\"";
        Yii::$app->db->createCommand($sql)->execute();        
    }
    
    
    
    
    ////////////////////////////////////////////////////////////////////////////
    //                  MOBILE.DE - SYNCHRONIZATION
    // Preparing an array [
    //      'ad_key' => [name, price],
    //      'ad_key' => [name, price], ....
    // ]
    // to show to user list of proposal before import to DB
    public static function mobileDePrepare()
    {
        try {
            $url = self::CUSTOMER_URL .Yii::$app->user->identity->profile->mobile_customer_id;
            $xml = self::getMobileResponse($url, 'de');
            $sxe = new SimpleXMLElement($xml);  
            $ret = [];

            foreach ($sxe->search_ads->ad_ad as $ad) {
                $class_key = (string)$ad->ad_vehicle->ad_class['key'];     // My Category (TransCat)
                $mkeys = self::getMobileKeys();
                $cat_id = $mkeys[$class_key]['cat_id'];
                if (!isset($cat_id))
                    continue;

                $ret[] = (string)$ad['key'];
            }

            return $ret;
        }
        catch (Exception $e) {
            throw $e;
        }
    }
    
    
    public static function mobileDe_delete($user_id, $ad_keys=[]) {
        $q = Trans::find()->where(['user_id' => $user_id]);
        $q->andWhere('mobile_key > 0');
        if (!empty($ad_keys)) 
            $q->andWhere(['NOT IN', 'mobile_key', $ad_keys]);
        
        $models = $q->all();
        foreach ($models as $model)
            $model->delete();
        
        return true;
    }
    
    
    public static function mobileDe($ad_keys)
    {
        $user_id = Yii::$app->user->id;
        $is_ajax = Yii::$app->request->isAjax;
        $br = $is_ajax ? "\r\n" : '<br />';
            
        // We can't execute self::updateReferences() here because in case of adding new 
        // FeatureH Feature.hid will be unknown. So we update only brands and models here.
        // self::updateModels();
        $my_brands = TransBrand::find()->indexBy(function($row) {return $row['cat_id'].$row['mobile_key']; })->asArray()->all();
        $my_models = TransModel::find()->indexBy(function($row) {return $row['brand_id'].$row['mobile_key']; })->asArray()->all();
        $ret = '';
        
        $my_featureh = TransFeatureH::find()->indexBy(function($row) {return $row['cat_id'].$row['trans_field']; })->asArray()->all();
        $trans_fields = TransFeatureH::find()->indexBy('trans_field')->asArray()->all();
        $my_feature = TransFeature::find()->indexBy(function($row) {return $row['hid'].$row['mobile_key']; })->asArray()->all();
        $f_tbl = TransFeature::tableName();
        $my_feature_names = (new Query())->select(['CONCAT(`hid`, `ru`) as `name`', 'id'])->from($f_tbl)->indexBy('name')
                ->union((new Query())->select(['CONCAT(`hid`, `de`) as `name`', 'id'])->from($f_tbl)->indexBy('name'), true)->all();

        foreach ($ad_keys as $ad_key) {
            $url = self::AD_URL .$ad_key;
            $xml = self::getMobileResponse($url, 'de');
            $sxe = new SimpleXMLElement($xml);

            $class_key = (string)$sxe->ad_vehicle->ad_class['key'];     // My Category (TransCat)
            $mkeys = self::getMobileKeys();
            $cat_id = $mkeys[$class_key]['cat_id'];
            if (is_null($cat_id))
                continue;
           
            ////////////////////////////////////////////////////////////////////
            // Add new FeatureH if not exists (for all the References except Category)
            // Add new Feature if not exists
            foreach (self::$mobile_specifics as $specif_name => $specif_data) {
                $mob_element = $sxe->ad_vehicle->ad_specifics->$specif_name;
                if (!isset($mob_element['key']))
                    continue;
     
                self::mobileDe_checkFeature($cat_id, $specif_data['trans_field'], $trans_fields, $specif_data['url'], $mob_element, $my_featureh, $my_feature, $my_feature_names);
            }
            
            // The same operations for Category
            $mob_element = $sxe->ad_vehicle->ad_category;
            if (is_null($mob_element['key']))
                continue;
            
            $trans_field = 'category_id';
            $url = self::CLASSES_URL .'/' .$class_key .'/categories';
            self::mobileDe_checkFeature($cat_id, $trans_field, $trans_fields, $url, $mob_element, $my_featureh, $my_feature, $my_feature_names);
            ////////////////////////////////////////////////////////////////////
            
            
            $trans = Trans::findOne(['mobile_key' => $ad_key]);
            if (is_null($trans)) {
                $trans = new Trans();
                $trans->loadDefaultValues();
                $trans->user_id = Yii::$app->user->id;
                $trans->date_int = time();
                $trans->cat_id = $cat_id;
                $trans->mobile_key = $ad_key;
            }
            
            // BRAND
            $brand_key = (string)$sxe->ad_vehicle->ad_make['key'];
            if (!is_null($brand_key)) {
                if (!isset($my_brands[$cat_id.$brand_key])) {
                    $brand_id = self::mobileDe_insert(TransBrand::tableName(), [
                        'cat_id' => $cat_id, 'mobile_key' => $brand_key,
                        'name' => $sxe->ad_vehicle->ad_make->{'resource_local-description'},
                    ]);
                        
                    $my_brands[$cat_id.$brand_key]['id'] = $brand_id;
                    $my_brands[$cat_id.$brand_key]['cat_id'] = $cat_id;
                    $my_brands[$cat_id.$brand_key]['mobile_key'] = $brand_key;
                } else { 
                    $brand_id = $my_brands[$cat_id.$brand_key]['id'];
                }
                
                $trans->brand_id = $brand_id;
            }
            
            
            // MODEL
            $model_key = $sxe->ad_vehicle->ad_model['key'];
            if (!is_null($model_key)) {
                $model_key = (string)$model_key;
                
                if (!isset($my_models[$brand_id.$model_key])) {
                    $model_id = self::mobileDe_insert(TransModel::tableName(), [
                        'brand_id' => $brand_id, 'mobile_key' => $model_key,
                        'name' => $sxe->ad_vehicle->ad_model->{'resource_local-description'},
                    ]);
                        
                    $my_models[$brand_id.$model_key]['id'] = $model_id;
                    $my_models[$brand_id.$model_key]['brand_id'] = $brand_id;
                    $my_models[$brand_id.$model_key]['mobile_key'] = $model_key;
                } else {
                    $model_id = $my_models[$brand_id.$model_key]['id'];
                }
                
                $trans->model_id = $model_id;
            }
            
            
            // YEAR & MONTH
            $ym = $sxe->ad_vehicle->ad_specifics->{'ad_first-registration'}['value'];
            if (!is_null($ym)) {
                $d = explode('-', $ym);
                $trans->year = (int)$d[0];
                $trans->month = (int)$d[1];
            }
            
            // CAPACITY
            $capacity = $sxe->ad_vehicle->ad_specifics->{'ad_cubic-capacity'}['value'];
            if (!is_null($capacity))
                $trans->capacity = (int)$capacity;
            
            // TRANSMISSION
            $transmiss_key = $sxe->ad_vehicle->ad_specifics->ad_gearbox['key'];
            if (!is_null($transmiss_key)) {
                $hid = $my_featureh[$cat_id.'transmiss_id']['id'];
                $trans->transmiss_id = $my_feature[$hid.(string)$transmiss_key]['id'];
            }
            
            // DRIVER_MODE
            $drive_key = $sxe->ad_vehicle->ad_specifics->{'ad_driving-mode'}['key'];
            if (!is_null($drive_key)) {
                $hid = $my_featureh[$cat_id.'drive_id']['id'];  
                $trans->drive_id = $my_feature[$hid.(string)$drive_key]['id'];
            }
            
            // INTERIOR
            $interior_key = $sxe->ad_vehicle->ad_specifics->{'ad_interior-type'}['key'];
            if (!is_null($interior_key)) {
                $hid = $my_featureh[$cat_id.'interior_id']['id'];  
                $trans->interior_id = $my_feature[$hid.(string)$interior_key]['id'];
            }
            
            // CLIMATE
            $climate_key = $sxe->ad_vehicle->ad_specifics->ad_climatisation['key'];
            if (!is_null($climate_key)) {
                $hid = $my_featureh[$cat_id.'climate_id']['id'];
                $trans->climate_id = $my_feature[$hid.(string)$climate_key]['id'];
            }
            
            // FUEL
            $fuel_key = $sxe->ad_vehicle->ad_specifics->ad_fuel['key'];
            if (!is_null($fuel_key)) {
                $hid = $my_featureh[$cat_id.'fuel_id']['id'];    
                $trans->fuel_id = $my_feature[$hid.(string)$fuel_key]['id'];
            }
            
            // CATEGORY
            // We have to use name instead of key because we have excluded repeated values
            // and some keys are absent in TransFeature
            $category_name = $sxe->ad_vehicle->ad_category->{'resource_local-description'};
            if (!is_null($category_name)) {
                $hid = $my_featureh[$cat_id.'category_id']['id'];
                $category_id = $my_feature_names[$hid.(string)$category_name]['id'];
                $trans->category_id = is_null($category_id) ? 0 : $category_id;
            }
            
            // CAB
            $cab_key = $sxe->ad_vehicle->ad_specifics->{'ad_driving-cab'}['key'];
            if (!is_null($cab_key)) {
                $hid = $my_featureh[$cat_id.'cab_id']['id']; 
                $trans->cab_id = $my_feature[$hid.(string)$cab_key]['id'];
            }
           
            // WHEEL
            $wheel_key = $sxe->ad_vehicle->ad_specifics->{'ad_wheel-formula'}['key'];
            if (!is_null($wheel_key)) {
                $hid = $my_featureh[$cat_id.'wheel_id']['id']; 
                $trans->wheel_id = $my_feature[$hid.(string)$wheel_key]['id'];
            }
            
            // MILEAGE
            $mileage = $sxe->ad_vehicle->ad_specifics->ad_mileage['value'];
            if (!is_null($mileage)) 
                $trans->mileage = (int)$mileage; 
            
            // PRICE_BRUT if exists ad:vat-rate, or PRICE_NET if not
            $price = $sxe->ad_price->{'ad_consumer-price-amount'}['value'];
            if (!is_null($price)) {
                $trans->currency = (string)$sxe->ad_price['currency'];
                $price = round( (float)$price );
                
                $vat_rate = $sxe->ad_price->{'ad_vat-rate'}['value'];
                if (is_null($vat_rate)) {
                    $trans->price_brut = $price; 
                    $trans->nds = 0;
                    $trans->price_net = $price; 
                } else {
                    $trans->price_brut = $price; 
                    $trans->nds = round( (float)$vat_rate*100 );
                    $trans->price_net = round( $price / (1+(float)$vat_rate) ); 
                }
            }
            
            // YOUTUBE
            $youtube = $sxe->ad_videoUrl['value'];
            if (!is_null($youtube)) 
                $trans->youtube = (string)$youtube; 
            
            // TEXT_DE, TEXT_RU
            $text = $sxe->ad_description;
            if (!is_null($text)) {
                $trans->text_de = (string)$text; 
                $trans->text_ru = (string)$text; 
            }

            $isNewRecord = $trans->isNewRecord;
            $name = $my_brands[$cat_id.$brand_key]['name'];
            if (!empty($my_models[$brand_id.$model_key]['name']))
                $name .= ' ' .$my_models[$brand_id.$model_key]['name'];
            $name .= ' ' .$trans->price_brut .' ' .$trans->currency;
                
            if (!$trans->save())
                $ret .= $name .$br .Html::errorSummary ($trans) .$br;
            elseif ($isNewRecord)
                $ret .= $name .'  -  ' .Yii::t('site', 'SAVED') .$br;
           else  
                $ret .= $name .'  -  ' .Yii::t('site', 'UPDATED') .$br;
           
           
           /////////////////////////////////////////////////////////////////////
           //           IMAGES
           if (is_null($sxe->ad_images))
               continue;
           
           $n = 0;
           foreach ($sxe->ad_images->ad_image as $image) {
               foreach ($image->ad_representation as $image_data) {
                   if ($image_data['size'] == 'XL') {
                       $srcFile = str_replace('http_//',  'http://', (string)$image_data['url']);
                       Trans::savePhoto($srcFile, $trans->id, false, $n);
                       break;
                   }
               }
               
               $n++;
           }
        }
        
        return $ret;
    }
    
    
    public static function mobileDe_insert($tbl, $columns = []) 
    {
        Yii::$app->db->createCommand()->insert($tbl, $columns)->execute();
        return Yii::$app->db->lastInsertID;        
    }
    
    // Add new FeatureH if not exists. Add new Feature if not exists
    public static function mobileDe_checkFeature($cat_id, $trans_field, $trans_fields, $url, $mob_element, &$my_featureh, &$my_feature, $my_feature_names) 
    {
        if (!isset($my_featureh[$cat_id.$trans_field])) {
            $hid = self::mobileDe_insert(TransFeatureH::tableName(), [
                'cat_id' => $cat_id, 'trans_field' => $trans_field,
                'ru' => $trans_fields[$trans_field]['ru'], 
                'de' => $trans_fields[$trans_field]['de'], 
            ]);

            $my_featureh[$cat_id.$trans_field]['id'] = $hid;
        }
        else {
            $hid = $my_featureh[$cat_id.$trans_field]['id'];
        }
        
        // Add new Feature if not exists
        $mob_key = (string)$mob_element['key'];
        if (isset($my_feature[$hid.$mob_key])) 
            return true;

        // TransFeature is not existst if we are here
        $de = (string)$mob_element->{'resource_local-description'};
        $ref_names = ['de' => $de, 'ru' => $de];        // ru will change below
        if (array_key_exists($hid.$de, $my_feature_names))
            return true;            // To exclude repeated values

        // Locate the Ru-Name
        $xml2 = self::getMobileResponse($url, 'ru');
        $sxe2 = new SimpleXMLElement($xml2); 
        foreach ($sxe2->reference_item as $item) {
            if ((string)$item['key'] == $mob_key) {
                $ref_names['ru'] = (string)$item->{'resource_local-description'};
                break;
            }
        }
        
        if (array_key_exists($hid.$ref_names['ru'], $my_feature_names))
            return true;            // To exclude repeated values
        
        self::mobileDe_insert(TransFeature::tableName(), [
            'hid' => $hid, 'mobile_key' => $mob_key,
            'ru' => $ref_names['ru'], 'de' => $ref_names['de'], 
        ]);

        $my_feature[$hid.$mob_key]['hid'] = $hid;
        $my_feature[$hid.$mob_key]['mobile_key'] = $mob_key;  
        return true;
    }    
    
    ////////////////////////////////////////////////////////////////////////////
    // Call with $colon_replace_with = false if replacement is not neccessary
    // or with any ather character instead of underscore.
    // We use the Exception for Ajax requests because we want to send message to user.
    public static function getMobileResponse($url, $lang, $colon_replace_with = '_')
    {
        $is_ajax = Yii::$app->request->isAjax;
        $ch = curl_init($url);
        if (!$ch) {
            if ($is_ajax)       throw new Exception(Yii::t('site', 'CURL_INIT_ERR'));
            else                throw new HttpException(503, Yii::t('site', 'CURL_INIT_ERR'));
        }

        $user_pwd = Yii::$app->user->identity->profile->mobile_login .':' .Yii::$app->user->identity->profile->mobile_pass;
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_USERPWD, $user_pwd);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/xml', 'Accept-Language: ' .$lang]);

        $res = curl_exec($ch);
        if ($res === false) {
            if ($is_ajax)       throw new Exception(curl_error($ch));
            else                throw new HttpException(500, curl_error($ch));
        }

        $info = curl_getinfo($ch);
        curl_close($ch);
        if ($info['http_code'] != 200) {
            return false;
//            if ($is_ajax)       throw new Exception('HTTP Error code - ' .$info[ 'http_code'] ."<br />" .Yii::t('user', 'CURL_MOBILE_DE_ERR'));
//            else                throw new HttpException($info[ 'http_code'], Yii::t('user', 'CURL_MOBILE_DE_ERR'));
        }

        if ($colon_replace_with)
            $res = str_replace(':', $colon_replace_with, $res);

        return $res;
    }
    
    
}
