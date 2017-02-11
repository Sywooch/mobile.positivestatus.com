<?php

namespace app\commands;

use yii\console\Controller;
use yii\console\Request;
use yii\console\Response;
use yii\web\HttpException;

use Yii;

use app\models\User;
use app\models\Pay;
use app\models\ManagerForm;

/**
 * Cron controller
 */
class ManagerController extends \yii\console\Controller {

    public function actionIndex() {
        echo "cron service runnning";
    }

   public function actionMail($to) {
        echo "Sending mail to " . $to;
    }

	
    //Checking for the test period
    public function actionTest(){
//        $users = User::find()->indexBy('id')->where(['account_id' => User::ACCOUNT_BUSINESS])->all();
        $pays_test = Pay::find()->indexBy('id')->where(['status' => Pay::STATUS_TEST])->all();
        $time_now = time();
        foreach ($pays_test as $pay){
            /** @var Pay $pay */
//            $user_id = $pay->user_id;
//            $user = User::findOne(['id'=>$user_id]);
            $payment_date = $pay->date1_int;
            $pay_expire = strtotime('+3 day', $payment_date);
            $days_left = round(abs($time_now - $pay_expire) / 86400);

            if ($days_left == 2){
                \Yii::$app->response->format = Response::FORMAT_JSON;
                $form = new ManagerForm();
                $form->pay_id = $pay->id;
                if ($form->validate()) {
                    //Notification about non payment
                    $form->sendMessage();
                } else {
                    throw new HttpException(404, Yii::t('app', 'Incorrect form'));
                }

            }elseif($days_left == 0){
                //Notification about end of test period
                $form = new ManagerForm();
                $form->pay_id = $pay->id;
                $form->sendNotification();
                return $pay->delete();
            }
        }
    }


    //Checking for the Bisness Account
    public function actionCheck(){
        $users = User::find()->indexBy('id')->where(['account_id' => User::ACCOUNT_BUSINESS])->all();
//        $pays_test = Pay::find()->indexBy('id')->where(['status' => Pay::STATUS_TEST])->all();
        $time_now = time();
        foreach ($users as $user){
            /* @var User $user*/
            $pay = $user->getLastPayment();
            if(isset($pay) && !empty($pay)){
                if($time_now < $pay->date2_int){
                    $payment_date = $pay->date1_int;
                    $payment_expire = $pay->date2_int;
                    $days_to_expire = round(abs($time_now - $payment_expire) / 86400);

                    if ($days_to_expire == 7){
                        //to create a new invoice with status non paymant
                        $next_period_date1 = strtotime('+1 day', $pay->date2_int);
//                        echo date('d,m,Y', $next_period_date1).' ';
                        $next_period_date2 = strtotime('+1 month', $next_period_date1);
//                        echo date('d,m,Y', $next_period_date2).'';
                        $next_period_pay = new Pay();
                        $next_period_pay->user_id = $user->id;
                        $next_period_pay->summa = 50;
                        $next_period_pay->date1_int = $next_period_date1;
                        $next_period_pay->date2_int = $next_period_date2;
                        $next_period_pay->status = Pay::STATUS_NOTCONFIRMED;
                        $next_period_pay->save();

                        $new_pay_id = $next_period_pay->id;

                        $form = new ManagerForm();
                        $form->pay_id = $new_pay_id;
                        //send new invoice and notification
                        $form->sendWarning($days_to_expire);

                    } elseif($days_to_expire == 3 || $days_to_expire == 1){

                        $pay = $user->getLastOpenInvoice();
                        $form = new ManagerForm();
                        $form->pay_id = $pay->id;
                        $form->sendWarning($days_to_expire);

                    } elseif($days_to_expire == 0){
                        //to disable an Account Bisness
                        $user->account_id = User::ACCOUNT_BASIC;
                        $user->save();

                    }
                }

            }
        }
    }
	


}