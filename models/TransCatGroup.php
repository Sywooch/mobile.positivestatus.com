<?php

namespace app\models;

use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%trans_cat_group}}".
 *
 * @property integer $id
 * @property string $ru
 * @property string $de
 */
class TransCatGroup extends \yii\db\ActiveRecord
{
    const TRUCK_ID = 1;
    const BUS_ID = 2;
    const BOAT_ID = 3;
    const SPEC_ID = 4;

    public static function tableName() {
        return '{{%trans_cat_group}}';
    }

    public function rules() {
        return [
            [['ru', 'de'], 'string', 'max' => 100],
        ];
    }

    public function attributeLabels() {
        return [
            'id' => 'ID',
            'ru' => 'Ru',
            'de' => 'De',
        ];
    }



    ////////////////////////////////////////////////////////////////////////////
    //              Other  functions
    public static function getDropdownItems() {
        $lang = Yii::$app->language;
        $q = (new Query())->select(['id', $lang])->from(self::tableName())->orderBy($lang)->all();
        return ArrayHelper::map($q, 'id', $lang);
    }
}
