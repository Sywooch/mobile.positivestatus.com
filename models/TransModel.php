<?php

namespace app\models;

use Yii;
use yii\db\Query;
use yii\helpers\Html;

/**
 * This is the model class for table "{{%trans_model}}".
 *
 * @property string $id
 * @property string $brand_id
 * @property string $name
 */
class TransModel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%trans_model}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['brand_id', 'integer'],
            ['name', 'string', 'max' => 100],
            [['brand_id', 'name'], 'unique', 'targetAttribute' => ['brand_id', 'name'], 'message' => Yii::t('site', 'NAME_EXISTS')],
            ['name', 'filter', 'filter' => ['yii\helpers\Html', 'encode']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'brand_id' => Yii::t('admin', 'LABEL_BRANDID'),
            'name' => Yii::t('admin', 'LABEL_NAME'),
        ];
    }
    
    
    ////////////////////////////////////////////////////////////////////////////
    //          Other functions
    public static function getDropdownHtml($brand_id, $add_new_model) {
        $qry = (new Query())->select(['name', 'id'])->from(self::tableName())
            ->where('brand_id=:brand_id')->params([':brand_id' => $brand_id])
            ->orderBy('name')->indexBy('id')->column();

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

        $html .= '</li>';
        if ($add_new_model) {
            $input = Html::textInput('new_model', '', ['id' => 'new_model_input', 'onkeypress' => 'addNewModel(event, this)']);
            $html .= Html::tag('div', Yii::t('admin', 'ADD_MODEL') .$input , ['class' => 'forma where ', 'style' => 'color: #EEE']);
        }

        return $html;
    }
}
