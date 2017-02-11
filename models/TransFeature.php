<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%trans_feature}}".
 *
 * @property integer $id
 * @property integer $hid
 * @property integer $subcat_id
 * @property string $ru
 * @property string $de
 */
class TransFeature extends \yii\db\ActiveRecord
{
    public static function tableName() {
        return '{{%trans_feature}}';
    }

    public function rules() {
        return [
            [['hid', 'subcat_id'], 'integer', 'min' => 0],
            [['ru', 'de'], 'string', 'max' => 100],
            [['ru', 'de'], 'filter', 'filter' => ['yii\helpers\Html', 'encode']],
        ];
    }

    public function attributeLabels() {
        return [
            'hid' => Yii::t('admin', 'LABEL_HID'),
            'subcat_id' => Yii::t('admin', 'LABEL_SUBCATID'),
            'ru' => 'RU',
            'de' => 'DE',
        ];
    }
}
