<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%user_profile}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $country
 * @property string $zip
 * @property string $address
 * @property double $lat
 * @property double $lng
 * @property integer $map_zoom
 * @property integer $mobile_customer_id
 * @property string $mobile_login
 * @property string $mobile_pass
 * @property string $details
 * ****** Details *********
 * @property string $w_hour1
 * @property string $w_hour2
 * @property array $w_days (in 1.2.3.4.5.6.7)
 * @property array $sites
 * @property array $facebooks
 * @property array $twitters
 */
class UserProfile extends \yii\db\ActiveRecord
{
    public $w_hour1 = '00:00';
    public $w_hour2 = '00:00';
    public $w_days = [];
    public $sites = [''];
    public $facebooks = [];
    public $twitters = [];
    private $_attrs_for_details = ['w_hour1', 'w_hour2', 'w_days', 'sites', 'facebooks', 'twitters'];

    public static function getHours() {
        return [
            '00:00' => '00:00', '01:00' => '01:00', '02:00' => '02:00', '03:00' => '03:00',
            '04:00' => '04:00', '05:00' => '05:00', '06:00' => '06:00', '07:00' => '07:00',
            '08:00' => '08:00', '09:00' => '09:00', '10:00' => '10:00', '11:00' => '11:00',
            '12:00' => '12:00', '13:00' => '13:00', '14:00' => '14:00', '15:00' => '15:00',
            '16:00' => '16:00', '17:00' => '17:00', '18:00' => '18:00', '19:00' => '19:00',
            '20:00' => '20:00', '21:00' => '21:00', '22:00' => '22:00', '23:00' => '23:00',
        ];
    }

    // SHORT_MON, SHORT_TUE etc - are the keys in /messages/user.php
    public static function getDays() {
        return [
            '1' => 'SHORT_MON', '2' => 'SHORT_TUE', '3' => 'SHORT_WED', '4' => 'SHORT_THU',
            '5' => 'SHORT_FRI', '6' => 'SHORT_SAT', '7' => 'SHORT_SUN'
        ];
    }


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //                  MAIN
    public static function tableName() {
        return '{{%user_profile}}';
    }

    public function scenarios() {
        return \yii\helpers\ArrayHelper::merge(
            parent::scenarios(),
            [
                'sync' => ['mobile_customer_id', 'mobile_login', 'mobile_pass'],
            ]
        );
    }

    public function rules() {
        return [
            [['country', 'zip', 'address'], 'required'],
            ['user_id', 'integer', 'min' => 1],
            [['address', 'zip', 'mobile_login', 'mobile_pass'], 'trim'],
            ['address', 'string', 'max' => 255],
            ['country', 'string', 'max' => 2],
            ['zip', 'string', 'max' => 6],
            [['lat', 'lng'], 'number'],
            ['map_zoom', 'integer', 'min' => 0, 'max' => 21],
            ['mobile_customer_id', 'integer', 'min' => 0],
            ['mobile_login', 'string', 'max' => 50],
            ['mobile_pass', 'string', 'max' => 25],
            [['mobile_customer_id', 'mobile_login', 'mobile_pass'], 'required', 'on' => 'sync'],
            [['w_hour1', 'w_hour2'], 'in', 'range' => array_keys(self::getHours())],
            ['w_days', 'in', 'range' => array_keys(self::getDays()), 'allowArray' => true],
            [['sites', 'facebooks', 'twitters'], 'arrayValidator', 'skipOnEmpty' => true],
        ];
    }

    public function arrayValidator($attribute, $params) {
        foreach ($this->$attribute as $k => $v) {
            $this->{$attribute}[$k] = substr(trim($v), 0, 100);
        }

        $this->$attribute = array_diff($this->$attribute, ['']);
    }

    public function attributeLabels() {
        return [
            'country' => Yii::t('user', 'LABEL_COUNTRY'),
            'zip' => Yii::t('user', 'LABEL_ZIP'),
            'address' => Yii::t('user', 'LABEL_ADDR'),
            'mobile_customer_id' => Yii::t('user', 'LABEL_MOBILEID'),
            'mobile_login' => Yii::t('user', 'LABEL_MOBILELOGIN'),
            'mobile_pass' => Yii::t('user', 'LABEL_MOBILEPASS'),
            'sites' => Yii::t('user', 'LABEL_SITE'),
            'facebooks' => 'Facebook',
            'twitters' => 'Twitter',
        ];
    }


    ///////////////////////////////////////////////////////////////////////////
    //              EVENTS
    ///////////////////////////////////////////////////////////////////////////
    // Serialize attributes which names are stored at $this->_attrs_for_details
    public function beforeSave($insert) {
        if (!parent::beforeSave($insert))
            return false;

        $data = array();
        foreach($this->_attrs_for_details as $attribute)
            $data[$attribute] = $this->{$attribute};

        $this->details = serialize($data);
        return true;
    }

    // Unserialize $this->details to attributes which names are stored at $this->_attrs_for_details
    public function afterFind() {
        foreach(unserialize($this->details) as $attribute => $value)
            if(in_array($attribute, $this->_attrs_for_details))
                $this->{$attribute} = $value;

        if (empty($this->sites))
            $this->sites[] = '';

        if ($this->mobile_customer_id == 0)
            $this->mobile_customer_id = '';

        parent::afterFind();
    }
    

    ///////////////////////////////////////////////////////////////////////////
    //              OTHER
    ///////////////////////////////////////////////////////////////////////////
    public function getFullAddress() {
        return $this->zip .' ' .$this->address;
    }
}
