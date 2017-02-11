<?php

namespace app\models;

use Yii;
use app\components\Y;

/**
 * This is the model class for table "{{%user_contact}}".
 *
 * @property string $id
 * @property string $user_id
 * @property string $name
 * @property string $details
 * ****** Details *********
 * @property array $langs
 * @property array $phones
 * @property array $cells
 * @property array $vibers
 * @property array $whatsapps
 * @property array $skypes
 * @property array $berries
 * @property array $emails
 */
class UserContact extends \yii\db\ActiveRecord
{
    public $langs = [];
    public $phones = [''];
    public $cells = [''];
    public $vibers = [];
    public $whatsapps = [];
    public $skypes = [];
    public $berries = [];
    public $emails = [];
    private $_attrs_for_details = ['langs', 'phones', 'cells', 'vibers', 'whatsapps', 'skypes', 'berries', 'emails'];

    public static function getLangList() {
        return ['Eng' => 'English', 'Deu' => 'Deutsche', 'Fra' => 'Français', 'Esp' => 'Español', 'Рус' => 'Русский'];
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //                  MAIN
    public static function tableName() {
        return '{{%user_contact}}';
    }

    public function rules() {
        return [
            [['user_id', 'name'], 'required'],
            ['user_id', 'integer', 'min' => 1],
            ['name', 'trim'],
            ['name', 'string', 'max' => 100],
            ['langs', 'in', 'range' => array_keys(self::getLangList()), 'allowArray' => true],
            [['phones', 'cells', 'vibers', 'whatsapps', 'skypes', 'berries', 'emails'], 'arrayValidator', 'skipOnEmpty' => true],
        ];
    }

    public function arrayValidator($attribute, $params) {
        foreach ($this->$attribute as $k => $v)
            $this->{$attribute}[$k] = substr(trim($v), 0, 100);

        $this->$attribute = array_diff($this->$attribute, ['']);
    }

    public function attributeLabels() {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'name' => Yii::t('user', 'LABEL_NAME'),
            'langs' => Yii::t('user', 'LABEL_LANG'),
            'phones' => Yii::t('user', 'LABEL_PHONE'),
            'cells' => Yii::t('user', 'LABEL_CELL'),
            'vibers' => 'Viber',
            'whatsapps' => 'WhatsApp',
            'skypes' => 'Skype',
            'berries' => 'BlackBerry',
            'emails' => 'EMail',
        ];
    }


    ///////////////////////////////////////////////////////////////////////////
    //              EVENTS
    ///////////////////////////////////////////////////////////////////////////
    // Serialize attributes which names are stored at $this->_attrs_for_details
    public function beforeSave($insert) {
        if (!parent::beforeSave($insert))
            return false;

        $n = count($this->cells);
        if (count($this->vibers) > $n)
            $this->vibers = array_slice($this->vibers, 0, $n);
        if (count($this->whatsapps) > $n)
            $this->whatsapps = array_slice($this->whatsapps, 0, $n);

        $data = array();
        foreach($this->_attrs_for_details as $attribute)
            if (!empty($this->{$attribute}))
                $data[$attribute] = $this->{$attribute};

        $this->details = serialize($data);
        return true;
    }

    // Unserialize $this->details to attributes which names are stored at $this->_attrs_for_details
    public function afterFind() {
        foreach(unserialize($this->details) as $attribute => $value)
            if(in_array($attribute, $this->_attrs_for_details))
                $this->{$attribute} = $value;

        if (empty($this->phones))
            $this->phones[] = '';
        if (empty($this->cells))
            $this->cells[] = '';

        parent::afterFind();
    }

    public function afterDelete() {
        $avatar = Y::getAvatarFile($this->id, false);
        if ($avatar) {
            $afile = Y::getAvatarDir() .DIRECTORY_SEPARATOR .$avatar;

            if (is_file($afile))
                @unlink($afile);
        }

        parent::afterDelete();
    }


    ///////////////////////////////////////////////////////////////////////////
    //              OTHER
    ///////////////////////////////////////////////////////////////////////////
}
