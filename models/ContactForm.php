<?php

namespace app\models;

use app\components\Y;
use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class ContactForm extends Model
{
    public $trans_id;
    public $user_id;
    public $contact_name;
    public $message;
    public $contact_type;       // Phone, Email, Skype ... etc
    public $contact_value;      // 123-123, qwer@gmail.com ... etc
    public $verify_code;

    public function rules() {
        $rules = [
            [['trans_id', 'user_id', 'contact_name', 'message', 'contact_type', 'contact_value'], 'required'],
            [['trans_id', 'user_id'], 'integer'],
        ];

        if (Y::showCaptcha())
            $rules[] = ['verify_code', 'captcha'];

        return $rules;
    }

    public function attributeLabels()  {
        return [
            'contact_value' => Yii::t('site', 'CONNECTION_TYPE'),
            'message' => Yii::t('site', 'YOUR_MESSAGE'),
            'verify_code' => Yii::t('site', 'VERIFY_CODE'),
        ];
    }


    ////////////////////////////////////////////////////////////
    public function sendMessage() {
        $model = Trans::find()->where(['id' => $this->trans_id])
            ->with([
                'brand', 'model', 'transmission', 'drive', 'interior', 'climate',
                'fuel', 'category', 'body', 'wheel', 'user', 'cat'
            ])->one();

        if (is_null($model))
            return ['result' => 'err', 'message' => Yii::t('site', 'NO_RECORDS_FOUND')];


        $text = Yii::t('site', 'MESSAGE_FIRST_LINE') .'<br />';
        $text .= date('M d Y', $model->date_int) .' #';
        $text .= Y::getStrpadFromId($model->id) .'<br />';
        $text .= $model->brand->name .' ' .$model->model->name .'<br />';
        $text .= number_format ($model->price_brut, 0, '.', ' ') .' &euro; <br /><br />';

        $arr = Trans::getDescriptions($model);
        foreach ($arr as $k => $v)
            $text .= $v .'<br />';

        $text .= '<br />' .Yii::t('site', 'MESSAGE') .': <br />';
        $text .= $this->message;

        $text .= '<br />' .Yii::t('site', 'CONNECTION') .': <br />';
        $text .= $this->contact_type .' ' .$this->contact_value .'<br />';

        $res = Yii::$app->mailer->compose()
            ->setTo($model->user->email)
            ->setSubject($this->contact_name .', ' .Yii::t('site', 'MESSAGE_SUBJECT'))
            ->setTextBody($text)
            ->send();

        return $res ? ['result' => 'ok', 'message' => Yii::t('site', 'MESSAGE_SENT')] : ['result' => 'err', 'message' => Yii::t('site', 'SEND_EMAIL_ERR')];
    }
}
