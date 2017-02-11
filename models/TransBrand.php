<?php

namespace app\models;

use Yii;
use yii\db\Query;
use yii\helpers\Html;

/**
 * This is the model class for table "{{%trans_brand}}".
 *
 * @property string $id
 * @property string $cat_id
 * @property string $name
 * @property integer $cnt
 */
class TransBrand extends \yii\db\ActiveRecord
{
    public static function tableName() {
        return '{{%trans_brand}}';
    }

    public function rules() {
        return [
            ['cat_id', 'integer'],
            ['name', 'string', 'max' => 100],
            [['cat_id', 'name'], 'unique', 'targetAttribute' => ['cat_id', 'name'], 'message' => Yii::t('site', 'NAME_EXISTS')],
            ['name', 'filter', 'filter' => ['yii\helpers\Html', 'encode']],
        ];
    }

    public function attributeLabels() {
        return [
            'cat_id' => Yii::t('admin', 'LABEL_CATID'),
            'name' => Yii::t('admin', 'LABEL_NAME'),
        ];
    }
    
    
    public function getModels() {
        return $this->hasMany(TransModel::className(), ['brand_id' => 'id']);
    }
    
    
    
    ////////////////////////////////////////////////////////////////////////////
    //                      EVENTS
    public function afterDelete() {
        foreach ($this->models as $model)
            $model->delete();
        
        parent::afterDelete();
    }
    
    
    ////////////////////////////////////////////////////////////////////////////
    //                  OTHER  FUNCTIONS
    public static function getDropdownHtml($cat_id) {
        $qry = (new Query())->select(['name', 'id'])->from(self::tableName())
            ->where('cat_id=:cat_id')->params([':cat_id' => $cat_id])->orderBy('name')->indexBy('id')->column();

        if (empty($qry))
            return '';

        $html = '';
        $first_letter = '?';

        foreach ($qry as $id => $name) {
            if ($name[0] != $first_letter) {
                $html .= ($first_letter == '?') ? '<li>' : '</li><li>';
                $first_letter = $name[0];
            }

            $html .= Html::a($name, '#', ['data-id' => $id]);
        }

        return $html .'</li>';
    }
}
