<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\db\Query;

/**
 * This is the model class for table "{{%trans_feature_h}}".
 *
 * @property string $id
 * @property string $cat_id
 * @property string $ru
 * @property string $de
 * @property string $trans_field
 */
class TransFeatureH extends \yii\db\ActiveRecord
{
    public static function tableName() {
        return '{{%trans_feature_h}}';
    }

    public function rules() {
        return [
            ['cat_id', 'integer'],
            [['ru', 'de'], 'string', 'max' => 100],
            [['ru', 'de'], 'filter', 'filter' => ['yii\helpers\Html', 'encode']],
        ];
    }

    public function attributeLabels() {
        return [
            'cat_id' => Yii::t('admin', 'LABEL_CATID'),
            'ru' => 'RU',
            'de' => 'DE',
        ];
    }
    
    
    public function getFeatures() {
        return $this->hasMany(TransFeature::className(), ['hid' => 'id']);
    }
    
    
    ////////////////////////////////////////////////////////////////////////////
    //                      EVENTS
    public function afterDelete() {
        foreach ($this->features as $feature)
            $feature->delete();
        
        parent::afterDelete();
    }
    
    
    
    ////////////////////////////////////////////////////////////////////////////
    //                  OTHER  FUNCTIONS
    // Array for the view/site/_proposal_slider.php
    // [category_id=>&html, transmiss_id=>&html, fuel_id=>&html, interior_id=>&html]
    //  Keys are TransFeatureH->trans_field, Html is <li><a>&value</a></li>
    public static function getDropdownSrc($cat_id) {
        $lang = Yii::$app->language;
        $ret = [];
        
        // $tfh_qry - TransFeatureH, $tf_qry - TransFeature, $sc_qry - Subcat
        $tfh_qry = (new Query())->select(['id', 'cat_id', $lang, 'trans_field'])->from(TransFeatureH::tableName())
            ->where('cat_id=:cat_id AND filter=1')->params([':cat_id' => $cat_id])->orderBy($lang)->indexBy('id')->all();
        $hid_query = (new Query())->select('id')->from(TransFeatureH::tableName())
            ->where('cat_id=:cat_id AND filter=1')->params([':cat_id' => $cat_id]);
        $tf_qry = (new Query())->select(['id', 'hid', 'subcat_id', $lang])->from(TransFeature::tableName())
            ->where(['hid' => $hid_query])->orderBy($lang)->all();
        $sc_qry = (new Query())->select(['id', 'cat_id', $lang])->from(TransSubcat::tableName())
            ->where('cat_id=:cat_id')->params([':cat_id' => $cat_id])
            ->orderBy($lang)->all();
        
        if (!empty($sc_qry))
            $sc_qry = ArrayHelper::map($sc_qry, 'id', $lang, 'cat_id');
        // $tfh_qry - TransFeatureH, $tf_qry - TransFeature, $sc_qry - Subcat

        foreach ($tfh_qry as $tfh_id => $tfh) {
            $trans_field = $tfh['trans_field'];     // category_id, transmiss_id, fuel_id, interior_id
            $ret[$trans_field] = '';

            // If Subcategories exists
            if ($trans_field == 'category_id' && isset($sc_qry[$tfh['cat_id']])) {
                foreach ($sc_qry[$tfh['cat_id']] as $sc_id => $sc_name) {
                    $ret[$trans_field] .= '<li>';
                    $ret[$trans_field] .= Html::a($sc_name, '#', ['class' => 'parent']);
                    $ret[$trans_field] .= in_array($tfh['cat_id'], [TransCat::CAT_TRUCKS, TransCat::CAT_SPEC]) ? '<ul class="f15 f16 f15mob">' : '<ul>';

                    foreach ($tf_qry as $feature)
                        if ($feature['subcat_id'] == $sc_id)
                            $ret[$trans_field] .= '<li>' .Html::a($feature[$lang], '#', ['data-id' => $feature['id']]) .'</li>';

                    $ret[$trans_field] .= '</ul></li>';
                }
            }
            // If Subcategories not exists
            else {
                foreach ($tf_qry as $feature)
                    if ($feature['hid'] == $tfh_id)
                        $ret[$trans_field] .= '<li>' .Html::a($feature[$lang], '#', ['data-id' => $feature['id']]) .'</li>';
            }
        }

        return $ret;

//        // $tfh_qry - TransFeatureH, $tf_qry - TransFeature, $sc_qry - Subcat
//        foreach ($tfh_qry as $tfh_id => $tfh) {
//            $n = $tfh_id;
//            $ret[$n]['trans_field'] = $tfh['trans_field'];
//            $ret[$n]['html'] = '<a class="dropdown-toggle" href="#" data-toggle="dropdown">' .$tfh[$lang]. ' <b class="caret"></b></a>';
//            $ret[$n]['html'] .= '<ul class="dropdown-menu" trans_field="' .$tfh['trans_field']. '">';
//
//            // If Subcategories exists
//            if ($tfh['trans_field']=='category_id' && isset($sc_qry[$tfh['cat_id']])) {
//                foreach ($sc_qry[$tfh['cat_id']] as $sc_id => $sc_name) {
//                    $ret[$n]['html'] .= '<li class="menu-item dropdown dropdown-submenu">'
//                        . '<a href="#" class="dropdown-toggle" data-toggle="dropdown">' .$sc_name. '</a>';
//
//                    $ret[$n]['html'] .= '<ul class="dropdown-menu" trans_field="' .$tfh['trans_field']. '">';
//
//                    foreach ($tf_qry as $feature) {
//                        if ($feature['subcat_id'] == $sc_id) {
//                            $ret[$n]['html'] .= '<li><a href="#" feature_id="' .$feature['id']. '" tabindex="-1">' .$feature[$lang]. '</a></li>';
//                            $ret[$n][$feature['id']] = $feature[$lang];     // For the hint on view/_proposal_slider.php
//                        }
//                    }
//
//                    $ret[$n]['html'] .= '</ul>';
//                    $ret[$n]['html'] .= '</li>';
//                }
//            }
//            // If Subcategories not exists
//            else {
//                foreach ($tf_qry as $feature) {
//                    if ($feature['hid'] == $tfh_id) {
//                        $ret[$n]['html'] .= '<li><a href="#" feature_id="' .$feature['id']. '" tabindex="-1">' .$feature[$lang]. '</a></li>';
//                        $ret[$n][$feature['id']] = $feature[$lang];     // For the hint on view/_proposal_slider.php
//                    }
//                }
//            }
//
//            $ret[$n]['html'] .= '</ul>';
//        }
//
//        return $ret;
    }
    

//    //      HTML code for the view/client/_add_proposal_dropdowns.php
//    // <div class="col-xs-3">
//    //     $field = TransFeatureH->trans_field
//    //     Html::activeDropDownList($model, $field, TransFeature_array('id', 'name'), ['class' => 'form-control input-sm', 'prompt' => Yii::t('client', $field), 'title' => Yii::t('client', $field)]);
//    // </div>
//    public static function getDropdownPanel($form, $model) {
//        $cat_id = $model->cat_id;
//        $lang = Yii::$app->language;
//        $ret = '';
//
//        // $tfh_qry - TransFeatureH, $tf_qry - TransFeature, $sc_qry - Subcat
//        $tfh_qry = (new Query())->from(TransFeatureH::tableName())
//            ->where('cat_id=:cat_id')->params([':cat_id' => $cat_id])->indexBy('id')->all();
//        $hid_query = (new Query())->select('id')->from(TransFeatureH::tableName())
//            ->where('cat_id=:cat_id')->params([':cat_id' => $cat_id]);
//        $tf_qry = (new Query())->from(TransFeature::tableName())
//            ->where(['hid' => $hid_query])->orderBy($lang)->all();
//        $sc_qry = (new Query())->select(['id', 'cat_id', $lang])->from(TransSubcat::tableName())
//            ->where('cat_id=:cat_id')->params([':cat_id' => $cat_id])
//            ->orderBy($lang)->all();
//
//        if (!empty($sc_qry))
//            $sc_qry = ArrayHelper::map($sc_qry, 'id', $lang, 'cat_id');
//
//        foreach ($tfh_qry as $tfh_id => $tfh) {
//            $field = strtoupper($tfh['trans_field']);
//            $items = ['0' => Yii::t('client', $field)];
//            $subcat = false;
//
//            // If Subcategories exists
//            if ($tfh['trans_field']=='category_id' && isset($sc_qry[$tfh['cat_id']])) {
//                $subcat = true;
//
//                foreach ($sc_qry[$tfh['cat_id']] as $sc_id => $sc_name) {
//                    $items[$sc_id] = $sc_name;
//                }
//            }
//            // If Subcategories not exists
//            else {
//                foreach ($tf_qry as $feature) {
//                    if ($feature['hid'] == $tfh_id)
//                        $items[$feature['id']] = $feature[$lang];
//                }
//            }
//
//            $fld = $subcat ? 'dummy_property' : $field;
//            $ret .= '<div class="col-xs-3">';
//            $ret .= $form->field($model, strtolower($fld))->dropDownList($items, ['class' => 'form-control input-sm'])
//                ->label(Yii::t('client', $field));
//            $ret .= '</div>';
//
//            if ($subcat) {
//                $ret .= '<div class="col-xs-3">';
//                $ret .= $form->field($model, strtolower($field))->dropDownList(['0' => Yii::t('client', 'DUMMY_PROP')], ['class' => 'form-control input-sm'])
//                    ->label(Yii::t('client', 'DUMMY_PROP'));
//                $ret .= '</div>';
//            }
//        }
//
//        if ($model->cat_id == TransCat::CAT_CARS) {
//            $ret .= '<div class="col-xs-2">';
//            $ret .= $form->field($model, 'capacity')->textInput(['class' => 'form-control input-sm text-right']);
//            $ret .= '</div>';
//        }
//
//        if ($model->cat_id == TransCat::CAT_SPEC) {
//            $ret .= '<div class="col-xs-2">';
//            $ret .= $form->field($model, 'weight')->textInput(['class' => 'form-control input-sm text-right']);
//            $ret .= '</div>';
//        }
//
//        return $ret;
//    }



    // HTML-code for /views/client/_list_listview.php
    public static function getFeatureSrc($cat_id, $trans_field) {
        $lang = Yii::$app->language;
        $ret = '';

        // $tfh_id - TransFeatureH.id
        $tfh_id = (new Query())->select('id')->from(TransFeatureH::tableName())
            ->where('cat_id=:cat_id AND trans_field=:trans_field')
            ->params([':cat_id' => $cat_id, 'trans_field' => $trans_field])->one();

        if (!$tfh_id)
            return '';

        // $tf_qry - TransFeature, $sc_qry - Subcat
        $tf_qry = (new Query())->select(['id', 'hid', 'subcat_id', $lang])->from(TransFeature::tableName())
            ->where(['hid' => $tfh_id])->orderBy($lang)->all();
        $sc_qry = (new Query())->select([$lang, 'id'])->from(TransSubcat::tableName())
            ->where('cat_id=:cat_id')->params([':cat_id' => $cat_id])
            ->orderBy($lang)->indexBy('id')->column();


        // If Subcategories exists
        if (!empty($sc_qry)) {
            if ($trans_field == 'category_id')
                $ret .= '<li><a href="#" data-feature_id="all">' .Yii::t('client', 'ALL_CATEGORIES') .' <i class="count"></i></a></li>';

            foreach ($sc_qry as $sc_id => $sc_name) {
                $ret .= '<li><a href="#">' . $sc_name . '<i class="count"></i></a>';

                $ret .= '<ul>';

                foreach ($tf_qry as $feature) {
                    if ($feature['subcat_id'] == $sc_id)
                        $ret .= '<li><a href="#" data-feature_id="' . $feature['id'] . '">' . $feature[$lang] . '<i class="count"></i></a></li>';
                }

                $ret .= '</li></ul>';
            }
        } // If Subcategories not exists
        else {
            foreach ($tf_qry as $feature) {
                //if ($feature['subcat_id'] == $sc_id)
                    $ret .= '<li><a href="#" data-feature_id="' . $feature['id'] . '">' . $feature[$lang] . '<i class="count"></i></a></li>';
            }
        }

        return empty($ret) ? $ret : '<ul>' .$ret .'</ul>';
    }
}
