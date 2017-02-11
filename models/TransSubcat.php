<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%trans_subcat}}".
 *
 * @property string $id
 * @property string $cat_id
 * @property string $ru
 * @property string $de
 * @property string $mobile_key
 */
class TransSubcat extends \yii\db\ActiveRecord
{
    public static function tableName() {
        return '{{%trans_subcat}}';
    }

    public function rules() {
        return [
            [['cat_id'], 'integer'],
            [['ru', 'de'], 'string', 'max' => 100],
        ];
    }

    public function attributeLabels() {
        return [
            'id' => 'ID',
            'cat_id' => 'Category',
            'ru' => 'Ru',
            'de' => 'De',
            'mobile_key' => 'Mobile Key',
        ];
    }
    
    
    ////////////////////////////////////////////////////////////////////////////
    
}
