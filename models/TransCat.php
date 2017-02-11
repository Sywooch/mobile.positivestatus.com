<?php

namespace app\models;

use Yii;
use yii\base\InvalidParamException;
use yii\db\Query;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * This is the model class for table "{{%trans_cat}}".
 *
 * @property string $id
 * @property string $group_id
 * @property string $ru
 * @property string $de
 * @property integer $min_year
 * @property integer $max_year
 * @property integer $min_price
 * @property integer $max_price
 * @property integer $cnt
 * @property string $mobile_key
 * @property boolean $blocked
 */
class TransCat extends \yii\db\ActiveRecord {
    const MIN_YEAR_DEFAULT = 2010;
    const MAX_YEAR_DEFAULT = 2011;
    const MIN_PRICE_DEFAULT = 0;
    const MAX_PRICE_DEFAULT = 1000;

    const CAT_CARS = 1;
    const CAT_TRUCKS = 2;
    const CAT_BUSES = 3;
    const CAT_SPEC = 4;
    const CAT_BIKES = 5;
    const CAT_BOATS = 6;

    private static $_categories = [
        self::CAT_CARS  => ['proposalform_class' => 'legko'],
        self::CAT_TRUCKS => ['proposalform_class' => 'gruz'],
        self::CAT_BUSES => ['proposalform_class' => 'bus'],
        self::CAT_SPEC  => ['proposalform_class' => 'spec'],
        self::CAT_BIKES => ['proposalform_class' => 'moto'],
        self::CAT_BOATS => ['proposalform_class' => 'lodka'],
    ];

    public static function getImageNameById($id) {
        return self::$_categories[$id]['proposalform_class'];
    }
    
    
//    // Mobile.de Class keys. Cross-table class.key <=> TransCat.id
//    // !!! Don't need Motorbikes for Synchronization, it's key not included !!!
//    public static $mobile_keys = [
//        'Car' => 1,                     // Легковой автомобиль => Легковые
//        'TruckOver7500' => 2,           // Грузовой автомобиль грузоподъемностью свыше 7,5 т => Грузовые
//        'SemiTrailerTruck' => 2,        // Седельный тягач => Грузовые
//        'VanUpTo7500' => 2,             // Фургон или грузовой автомобиль грузоподъемностью до 7,5 т => Грузовые
//        'Bus' => 3,                     // Автобус => Автобусы
//        'Motorhome' => 3,               // Жилой автомобиль или прицеп-дача => Автобусы
//        'ForkliftTruck' => 4,           // Автопогрузчик с вилочным захватом => Спецтехника
//        'ConstructionMachine' => 4,     // Строительное транспортное средство => Спецтехника
//        'SemiTrailer' => 5,             // Полуприцеп => Прицепы
//        'Trailer' => 5,                 // Прицеп для легкового автомобиля => Прицепы
//        'AgriculturalVehicle' => 6,     // Сельхозтехника => Сельхозтехника
//    ];
    
    
    public static function tableName() {
        return '{{%trans_cat}}';
    }

    public function rules() {
        return [
            ['group_id', 'number', 'min' => 1],
            [['ru', 'de'], 'string', 'max' => 100],
            [['ru', 'de'], 'filter', 'filter' => ['yii\helpers\Html', 'encode']],
        ];
    }

    public function attributeLabels() {
        return [
            'ru' => 'RU',
            'de' => 'DE',
            'group_id' => Yii::t('admin', 'LABEL_GROUP'),
        ];
    }


    ////////////////////////////////////////////////////////////////////////////
    //          getSliderValues  (min_year, max_year, min_price, max_price)
    public function getSliderValues() {
        $sql = 'SELECT MIN(year) as min_year, MAX(year) as max_year, MIN(price_brut) as min_price, MAX(price_brut) as max_price ';
        $sql .= 'FROM {{%trans}} WHERE cat_id = ' .(int)$this->id;
        $values = Yii::$app->db->createCommand($sql)->queryOne();

        if (empty($values['min_year']))
            $values = ['min_year' => self::MIN_YEAR_DEFAULT, 'max_year' => self::MAX_YEAR_DEFAULT, 'min_price' => self:: MIN_PRICE_DEFAULT, 'max_price' => self::MAX_PRICE_DEFAULT];
        else {      // To avoid problems in the reason of slider JS-ceiling
            $values['min_price'] = round($values['min_price'], -3);
            $values['max_price'] = round($values['max_price'], -3);
        }

        if ($values['min_price'] == $values['max_price']) {
            $values['min_price'] = ($values['min_price'] < 1000) ? 0 : $values['min_price'] - 1000;
            $values['max_price'] = $values['max_price'] + 1000;
        }

        if ($values['min_year'] == $values['max_year']) {
            $values['min_year'] = $values['min_year'] - 1;
            $values['max_year'] = $values['max_year'] + 1;
        }

        $this->updateAttributes($values);
        return true;
    }
    
    ////////////////////////////////////////////////////////////////////////////
    //              Update  Globals  (min_year, max_year, min_price, max_price)
    /**
     * @param $trans    Trans::findOne()
     * @param $action   'insert', 'update' or 'delete'
     */
//    public function updateGlobals($trans, $action, $changedAttributes = []) {
//        switch ($action) {
//            case 'insert' :     // Trans::afterSave($insert = true)
//                $new_values = $this->updateGlobalsInsert($trans);
//                break;
//
//            case 'update' :     // Trans::afterSave($insert = false)
//                $new_values = $this->updateGlobalsUpdate($trans, $changedAttributes);
//                break;
//
//            case 'delete' :     // Trans::afterDelete()
//                $new_values = $this->updateGlobalsDelete($trans);
//                break;
//        }
//
//        if (!empty($new_values))
//            $this->updateAttributes($new_values);
//
//        return true;
//    }
//
//    private function updateGlobalsInsert($trans) {
//        $new_values = [];
//
//        if ($trans->year < $this->min_year)
//            $new_values['min_year'] = $trans->year;
//        if ($trans->year > $this->max_year)
//            $new_values['max_year'] = $trans->year;
//
//        if ($this->min_price == self::MIN_PRICE_DEFAULT && $this->max_price == self::MAX_PRICE_DEFAULT) {
//            $new_values['min_price'] = $trans->price_brut;
//            $new_values['max_price'] = $trans->price_brut + 1000;
//        }
//        elseif ($trans->price_brut < $this->min_price) {
//            $new_values['min_price'] = $trans->price_brut;
//        }
//        elseif ($trans->price_brut > $this->max_price) {
//            $new_values['max_price'] = $trans->price_brut;
//        }
//
//        return $new_values;
//    }
//
//    private function updateGlobalsUpdate($trans, $changedAttributes) {
//        $new_values = [];
//        $tab = Trans::tableName();
//
//        if (isset($changedAttributes['year']) && in_array($changedAttributes['year'], [$this->min_year, $this->max_year])) {
//            $new_values['min_year'] = (new Query())->from($tab)->where(['cat_id' => $trans->cat_id])->min('year');
//            $new_values['max_year'] = (new Query())->from($tab)->where(['cat_id' => $trans->cat_id])->max('year');
//        }
//        if (isset($changedAttributes['price_brut']) && in_array($changedAttributes['price_brut'], [$this->min_price, $this->max_price])) {
//            $new_values['min_price'] = (new Query())->from($tab)->where(['cat_id' => $trans->cat_id])->min('price_brut');
//            $new_values['max_price'] = (new Query())->from($tab)->where(['cat_id' => $trans->cat_id])->max('price_brut');
//        }
//
//        return $new_values;
//    }
//
//    private function updateGlobalsDelete($trans) {
//        $new_values = [];
//        $tab = Trans::tableName();
//
//        if ($trans->year == $this->min_year || $trans->year == $this->max_year) {
//            $new_values['min_year'] = (new Query())->from($tab)->where(['cat_id' => $trans->cat_id])->min('year');
//            $new_values['max_year'] = (new Query())->from($tab)->where(['cat_id' => $trans->cat_id])->max('year');
//
//            if (empty($new_values['min_year'])) {
//                $new_values['min_year'] = self::MIN_YEAR_DEFAULT;
//                $new_values['max_year'] = self::MAX_YEAR_DEFAULT;
//            }
//            elseif ($new_values['min_year'] == $new_values['max_year']) {
//                $new_values['min_year'] = $new_values['max_year'] - 1;
//            }
//        }
//
//        if ($trans->price_brut == $this->min_price || $trans->price_brut == $this->max_price) {
//            $new_values['min_price'] = (new Query())->from($tab)->where(['cat_id' => $trans->cat_id])->min('price_brut');
//            $new_values['max_price'] = (new Query())->from($tab)->where(['cat_id' => $trans->cat_id])->max('price_brut');
//
//            if (empty($new_values['min_price'])) {
//                $new_values['min_price'] = self::MIN_PRICE_DEFAULT;
//                $new_values['max_price'] = self::MAX_PRICE_DEFAULT;
//            }
//            elseif ($new_values['min_price'] == $new_values['max_price']) {
//                $new_values['min_price'] = $new_values['max_price'] - 1000;
//                if ($new_values['min_price'] < 0)
//                    $new_values['min_price'] = 0;
//            }
//        }
//
//        return $new_values;
//    }



    ////////////////////////////////////////////////////////////////////////////
    //              Other  functions
    public static function getDropdownItems() {
        $lang = Yii::$app->language;
        $sql = 'SELECT id, ru, de FROM ' .self::tableName();        // .' WHERE excluded=0';
        $items = Yii::$app->db->createCommand($sql)->queryAll();
        
        $res = [];
        foreach ($items as $item)
            $res[] = ['label' => $item[$lang], 'url' => Url::to(['/client/edit-proposal', 'cat_id' => $item['id']])];
        
        return $res;
    }


//    <div class="items">
//      <input type="radio" value="kateg" name="katego" id="category_1">
//      <label for="category_1" class="legko"></label>
//    </div>
    public static function getProposalFormCategoryPanel() {
        $ret = '';

        foreach(self::$_categories as $cat_id => $cat_data) {
            $input = Html::radio('katego', false, ['value' => 'kateg', 'id' => 'category_'.$cat_id, 'data-category_id' => $cat_id]);
            $label = Html::label('', 'category_'.$cat_id, ['class' => $cat_data['proposalform_class']]);
            $ret .= Html::tag('div', $input.$label, ['class' => 'items']);
        }

        return $ret;
    }


    public static function getNameById($id, $first_word_only = false) {
        $lang = Yii::$app->language;
        $cat_name = (new Query())->select($lang)->from(self::tableName())->where('id=:id', [':id' => $id])->scalar();
        if (!$first_word_only)
            return $cat_name;

        $a = explode(' ', $cat_name);
        return ucfirst(trim($a[0], ', '));
    }


    public static function getMainmenuItem($item) {
        $item = strtoupper($item);

        if ($item == 'CAR')
            return Html::a('<svg class="mob_hide"  width="113" height="34">
                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#car_two"></use>
            </svg>
            <svg class="mob_show" width="57" height="57">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#vcars"></use>
                            </svg>' .Yii::t('site', 'CAR'), Url::to(['/site/proposal', 'trans_cat' => 'cars']));
        if ($item == 'BUS')
            return Html::a('<svg class="mob_hide"  width="113" height="34">
                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#bus2"></use>
            </svg>
            <svg class="mob_show" width="57" height="57">
                 <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#vbus"></use>
             </svg>' .Yii::t('site', 'BUS'), Url::to(['/site/proposal', 'trans_cat' => 'buses']));
        if ($item == 'TRUCK')
            return Html::a('<svg class="mob_hide"  width="113" height="34">
                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#gruz"></use>
            </svg>
            <svg class="mob_show" width="57" height="57">
                 <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#vgruz"></use>
             </svg>' .Yii::t('site', 'TRUCK'), Url::to(['/site/proposal', 'trans_cat' => 'vans']));
        if ($item == 'SPEC')
            return Html::a('<svg class="mob_hide"  width="113" height="34">
                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#spec_teh"></use>
            </svg>
            <svg class="mob_show" width="57" height="57">
                 <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#vspec"></use>
             </svg>' .Yii::t('site', 'SPEC'), Url::to(['/site/proposal', 'trans_cat' => 'constructionmachines']));
        if ($item == 'BIKE')
            return Html::a('<svg class="mob_hide"  width="113" height="34">
                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#motos"></use>
            </svg>
            <svg class="mob_show" width="57" height="57">
                 <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#vmotos"></use>
             </svg>' .Yii::t('site', 'BIKE'), Url::to(['/site/proposal', 'trans_cat' => 'bikes']));
        if ($item == 'BOAT')
            return Html::a('<svg class="mob_hide"  width="82" height="34">
                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#yaht2"></use>
            </svg>
            <svg class="mob_show" width="57" height="57">
                 <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#vyahts"></use>
             </svg>' .Yii::t('site', 'BOAT'), Url::to(['/site/proposal', 'trans_cat' => 'motorboats']));

        throw new InvalidParamException('unknown parameter ' .$item);
    }
}

