<?php

namespace app\models;

use app\components\Y;
use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class PartnershipForm extends Model
{
    public $message;
    CONST SEND_EMAIL_TO = 'mobilemakler@gmail.com';


    public function rules()
    {
        return [

            [['message'], 'string'],
        ];

    }
//    public function attributeLabels()  {
//        return [
//            'contact_value' => Yii::t('site', 'CONNECTION_TYPE'),
//            'message' => Yii::t('site', 'YOUR_MESSAGE'),
//            'verify_code' => Yii::t('site', 'VERIFY_CODE'),
//        ];
//    }


    ////////////////////////////////////////////////////////////
    public function sendMessage($id) {

        $model = User::findOne($id);

        $profile = UserProfile::findOne(['user_id' => $id]);

        $contacts = UserContact::find()->where(['user_id' => $id])->indexBy('id')->all();

        $text = null;

        $text .= Yii::t('site', 'MESSAGE') .': ';
        $text .= $this->message;

        $res = Yii::$app->mailer->compose()
            ->setTo(self::SEND_EMAIL_TO)
            ->setSubject('Заявка на партнерство от ID' . Y::getPaymentId($id))
            ->setTextBody($text)
            ->send(); 

        return $res ? ['result' => 'ok', 'message' => Yii::t('site', 'MESSAGE_SENT')] : ['result' => 'err', 'message' => Yii::t('site', 'SEND_EMAIL_ERR')];
    }
}
