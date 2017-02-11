<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%pay}}".
 *
 * @property string $id
 * @property string $user_id
 * @property string $summa
 * @property string $date1_int
 * @property string $date2_int
 * @property integer $status
 */
class Pay extends \yii\db\ActiveRecord
{
    const STATUS_NOTCONFIRMED = 0;
    const STATUS_CONFIRMED = 1;
    const STATUS_TEST = 2;
    const STATUS_PROMO = 3;

    public static function getStatuses() {
        return [
            self::STATUS_NOTCONFIRMED => 'Not Confirmed',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_TEST => 'Test',
            self::STATUS_PROMO => 'Promo code'
        ];
    }

    public static function tableName() {
        return '{{%pay}}';
    }

    public function rules() {
        return [
            [['user_id', 'summa', 'date1_int', 'date2_int'], 'required'],
            [['user_id', 'summa', 'date1_int', 'date2_int'], 'integer', 'min' => 1],
            ['status', 'in', 'range' => array_keys(self::getStatuses())]
        ];
    }

    public function attributeLabels() {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'summa' => 'Summa',
            'date1_int' => 'Date From',
            'date2_int' => 'Date To',
            'date1' => 'Date From',
            'date2' => 'Date To',
            'status' => 'Status',
        ];
    }


    ///////////////////////////////////////////////////////////////////////////
    //              EVENTS
    ///////////////////////////////////////////////////////////////////////////

    public function getPayment($user_id){
        return $this::find()->where(['user_id'=>$user_id, 'status' => self::STATUS_NOTCONFIRMED])->orderBy("date1_int DESC")->one();
    }
    public function getAllOpenInvoice(){
        return $this::find()->where(['status' => self::STATUS_NOTCONFIRMED])->orderBy("date1_int DESC")->all();
    }
    public function getLastPayment(){
        return $this::find()->where(['status' => self::STATUS_CONFIRMED])->orderBy("date1_int DESC")->one();
    }


    public function getPeriodExpire(){
            $today = time();
            $payment_day = $this->date1_int;
            $date_expire = strtotime('+3 day', $payment_day);
            $time_left = round(abs($date_expire - $today) / 86400);
//            $time_left = floor(($date_expire - $today) / 86400);

            return $time_left;



//        if ($time_left >= 0){
////            return "Платеж просрочен";
//        }   else {
//            return round(abs($time_left) / (60 * 60 * 24));
//        }

        // for debug
        /*function retDate($date){
                return date('d/m/Y', $date);
        }
        return retDate($payment_day) . ' ' . retDate($today) . ' ' . retDate($date_expire);*/

    }


    ///////////////////////////////////////////////////////////////////////////
    //              OTHER
    ///////////////////////////////////////////////////////////////////////////


    public function getDate1() {
        return Yii::$app->formatter->asDate($this->date1_int, 'short');
    }

    public function getDate2() {
        return Yii::$app->formatter->asDate($this->date2_int, 'short');
    }

}
