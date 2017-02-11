<?php


namespace app\controllers;


use app\models\PartnershipForm;
use app\models\Pay;
use Yii;

use app\components\Controller;

Use app\components\Y;

use yii\filters\AccessControl;

use yii\helpers\Html;

use yii\helpers\Url;

use yii\base\Model;

use yii\web\NotFoundHttpException;

use app\models\User;

use app\models\UserProfile;

use app\models\UserContact;

use app\models\LoginForm;

use yii\widgets\ActiveForm;


use app\models\PaymentForm;

use yii\web\Response;
use yii\web\HttpException;
use yii\db\ActiveRecord;

use app\components\PayPal;
class UserController extends Controller
{

    public function behaviors()

    {

        return [

            'access' => [

                'class' => AccessControl::className(),

                'except' => ['register', 'login', 'activate', 'forgot-pass', 'recovery'],

                'rules' => [

                    ['allow' => true, 'roles' => ['@']],

                ],

            ],

        ];

    }
	


    public function actionRegister()
    {

        $model = new User(['scenario' => 'register']);

        $res = ['result' => 'err'];


        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            if (!$model->sendRegistrationMail())

                $res['html'] = Html::tag('div', Yii::t('site', 'SEND_EMAIL_ERR'), ['class' => 'alert alert-danger text-center', 'role' => 'alert']);

            elseif (!$model->save(false))

                $res['html'] = Html::tag('div', Yii::t('site', 'SAVE_TO_BASE_ERR'), ['class' => 'alert alert-danger text-center', 'role' => 'alert']);

            else {

                $res = ['result' => 'ok'];

                $res['html'] = Html::tag('div', Yii::t('user', 'ACCOUNT_CREATED'), ['class' => 'alert alert-success text-center', 'role' => 'alert']);

            }

        } else {

            $res['html'] = Html::tag('div', Y::getArErrors($model), ['class' => 'alert alert-danger', 'role' => 'alert']);

        }


        return json_encode($res);

    }


    public function actionActivate($email, $auth_key)
    {

        $model = User::findOne(['email' => $email, 'auth_key' => $auth_key, 'status' => User::STATUS_INACTIVE]);


        if (is_null($model)) {

            Yii::$app->session->setFlash('danger', 'Incorrect incoming parameters');

            return $this->goHome();

        }


        // All users must have Profile and at least 1 Contact

        if (is_null($model->profile)) {

            $profile = new UserProfile(['user_id' => $model->id]);

            $profile->save(false);      // Default values for Details are created here

        }


        if (empty($model->contacts)) {

            $contact = new UserContact(['user_id' => $model->id]);

            $contact->save(false);      // Default values for Details are created here

        }


        //$model->updateAttributes(['auth_key' => Yii::$app->security->generateRandomString()]);

        Yii::$app->user->login($model);

        return $this->redirect(['profile', 'id' => $model->id, 'scenario' => 'activate']);

    }





    // While the first entry User.status = INACTIVE ('scenario' => 'activate')

    // If status = INACTIVE, pass & repeat pass are required

    public function actionProfile($id, $scenario = 'edit')
    {

        if (Yii::$app->user->id != $id)

            throw new NotFoundHttpException();


        $model = User::findOne($id);

        if (is_null($model))

            throw new NotFoundHttpException();


        $model->setScenario($scenario);

        $profile = UserProfile::findOne(['user_id' => $id]);

        $contacts = UserContact::find()->where(['user_id' => $id])->indexBy('id')->all();


        if ($model->load(Yii::$app->request->post())) {

            $val1 = $model->validate();

            $val2 = $profile->load(Yii::$app->request->post()) && $profile->validate();

            $val3 = Model::loadMultiple($contacts, Yii::$app->request->post()) && Model::validateMultiple($contacts);


            if ($val1 && $val2 && $val3) {

                $model->save(false);

                $profile->save(false);


                foreach ($contacts as $contact)

                    $contact->save(false);


                Yii::$app->session->setFlash('profile_saved', Yii::t('user', 'ACCOUNT_SAVED'));

                return $this->refresh();

            }

        }


        // For the first call after registration

        if ($profile->lat == 0 && $profile->lng == 0)

            $this->setLatLng($profile);
			
		
		// For displaying status of Bisness Account
		
		$user=User::findOne($id);
		
		$status_account=$user->account_id;
		
		$status_detail=Yii::t('user', 'TEXT_BUSINESS');
		
		$pay = $user->getLastPayment();
		
		if ($status_account == User::ACCOUNT_BUSINESS && $pay)
		
		{
			$date1=date("d,m,y", $pay->date1_int);
			
			$date2=date("d,m,y", $pay->date2_int);
			
			$status_detail="Created at: ".$date1."<br>Expire at: ".$date2;
			
		}

        return $this->render('profile', ['model' => $model, 'profile' => $profile, 'contacts' => $contacts, 'status_detail' =>$status_detail,]);

    }


    protected function setLatLng(&$profile)
    {

        $userIP = Yii::$app->request->userIP;

        $geoip = \Yii::createObject([

            'class' => '\rmrevin\yii\geoip\HostInfo',

            'host' => $userIP,

        ]);


        if (!$geoip->isAvailable()) {

            $profile->lat = Yii::$app->params['mobileLat'];

            $profile->lng = Yii::$app->params['mobileLng'];

        } else {

            $profile->lat = $geoip->getLatitude();

            $profile->lng = $geoip->getLongitude();

            $profile->address = $geoip->getCity();

            $zip_code = $geoip->getAreaCode();

            if ($zip_code != 0)

                $profile->zip = $zip_code;

        }


        return true;

    }





    ////////////////////////////////////////////////////////////////////////////

    //          Login & Logout

    public function actionLogin()
    {

        if (!Yii::$app->user->isGuest) {

            return 'ok';

        }


        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {

            return 'ok';

        } else {

            return Y::getArErrors($model);

        }

    }


    public function actionLogout()
    {

        Yii::$app->user->logout();

        return $this->redirect(Url::home());

    }







    ////////////////////////////////////////////////////////////////////////////

    //          Forgot Password & recoverw

    public function actionForgotPass()
    {

        $model = new User(['scenario' => 'forgot_pass']);

        $modal_mess = false;


        if ($model->load(Yii::$app->request->post())) {

            if (!$model->validate()) {

                $modal_mess = Y::getArErrors($model);

            } else {

                /** @var User $user */

                $user = User::findOne(['email' => $model->email]);


                if ($user->status == User::STATUS_INACTIVE) {

                    $modal_mess = Yii::t('user', 'INCORRECT_STATUS');

                } else {

                    $user->updateAttributes(['auth_key' => Yii::$app->security->generateRandomString()]);

                    $modal_mess = ($user->sendRecoveryMail()) ? Yii::t('user', 'RECOVERY_OK_MESSAGE') : Yii::t('site', 'SEND_EMAIL_ERR');

                }

            }

        }


        return $modal_mess;

        //return $this->render('forgot_pass',['model'=>$model, 'modal_mess' => $modal_mess]);

    }


    public function actionRecovery($email, $auth_key)
    {

        $model = User::findOne(['email' => $email, 'auth_key' => $auth_key]);

        if (is_null($model)) {

            Yii::$app->session->setFlash('danger', Yii::t('site', 'INCORRECT_PARAMS'));

            return $this->goHome();

        }


        Yii::$app->user->login($model);

        $profile_url = Url::to(['profile', 'id' => $model->id, 'scenario' => 'recovery']);

        return $this->redirect($profile_url);


//        $model->setScenario('recovery');

//

//        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

//            $model->auth_key = Yii::$app->security->generateRandomString();

//            $model->save(false);

//            Yii::$app->user->login($model);

//            $start_mess = Yii::t('user', 'PASS_CHANGED');

//            return $this->redirect(Url::to(['/site/index', 'start_mess' => $start_mess]));

//        }

//

//        return $this->render('forgot_pass', ['model' => $model]);

    }

    public function actionPayment($id)
    {

        if (Yii::$app->user->id != $id)

            throw new NotFoundHttpException();


        $model = User::findOne($id);
        if (is_null($model))

            throw new NotFoundHttpException();
		
		
		
		$payment = PaymentForm::findOne(['user_id' => $id]);
		
		if(!$payment){
		
			$payment=new PaymentForm();
		
		}
		
        if (is_null($payment))

            throw new NotFoundHttpException();

//        if ($model->getPays()){
//            $pay = Pay::find()->where(['user_id'=>$id])->orderBy("date1_int DESC")->one();
//            if ($pay->status == Pay::STATUS_NOTCONFIRMED)
//                return $form->generatePdf($id)->render();
//        }

        $profile = UserProfile::findOne(['user_id' => $id]);

        $contacts = UserContact::find()->where(['user_id' => $id])->indexBy('id')->all();

        return $this->render('payment', ['model' => $model, 
										 'profile' => $profile, 
										 'contacts' => $contacts,
										 'payment' => $payment,
										 
										 ]);

//        return $this->render('payment');

    }
	
	
	
	

	
    public function actionTransfer()
    {

        $user_id = Yii::$app->user->id;
        $date = time();
        $date_expire = strtotime('+1 month', $date);
		
		$form=PaymentForm::findOne(['user_id' => $user_id]);
		if ($form) {
			if (Yii::$app->getRequest()->isPost) {
				$form->load(Yii::$app->request->post());
				if ($form->validate()) {
					$form->save();
                    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
					
					//To enable a Account Bisness for user
					$user = User::findOne(['id' => $user_id]);
					if (is_null($user))
						return json_encode('Record not found');
					$user->account_id = User::ACCOUNT_BUSINESS;
//       			$user->save();
					if (!$user->save(false))
						return json_encode('Error while saving data');
					
                    $pay = new \app\models\Pay();
                    $pay->user_id = $user_id;
                    $pay->summa = 50;
                    $pay->date1_int = $date;
                    $pay->date2_int = $date_expire;
                    $pay->status = Pay::STATUS_TEST;
                    $pay->save();
                    $pay_id = $pay->id;
                    // update an existing row of data
//                    $customer = User::findOne($user_id);
//                    $pdf = $form->generatePdf($pay_id);
//                    $pdf_string = $pdf->output('', 'S');
                    $form->sendMessage($user_id, $pay_id); 
                        return $this->redirect('/user/profile?id='.$user_id);
//                    \Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
//                    return Pay::findOne($pay_id);
					
					}
				}
		}
		
		else {
		
        if (Yii::$app->getRequest()->isPost) {
            $form = new PaymentForm();
//            if (isset($_POST['bank_transfer'])) {
            $form->load(Yii::$app->request->post());
                if ($form->validate()) {
					
					$form->save();
                    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
					
					//To enable a Account Bisness for user
					$user = User::findOne(['id' => $user_id]);
					if (is_null($user))
						return json_encode('Record not found');
					$user->account_id = User::ACCOUNT_BUSINESS;
//       			$user->save();
					if (!$user->save(false))
						return json_encode('Error while saving data');
					
                    $pay = new \app\models\Pay();
                    $pay->user_id = $user_id;
                    $pay->summa = 50;
                    $pay->date1_int = $date;
                    $pay->date2_int = $date_expire;
                    $pay->status = Pay::STATUS_TEST;
                    $pay->save();
                    $pay_id = $pay->id;
                    // update an existing row of data
//                    $customer = User::findOne($user_id);
//                    $pdf = $form->generatePdf($pay_id);
//                    $pdf_string = $pdf->output('', 'S');
                    $form->sendMessage($user_id, $pay_id);
                        return $this->redirect('/user/profile?id='.$user_id);
//                    \Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
//                    return Pay::findOne($pay_id);
                } else {
                    throw new HttpException(404, 'Incorrect form');
                }
//            } else if (isset($_POST['paypal'])) {
//            }
        }
		
        else {
            throw new HttpException(403, 'You don\'t have permissions');
        }
		}

    }
	
	
	
	
	public function actionPaymentSuccess($status, $user_id){
	
		if ($status=='ok') {
			
			$form=PaymentForm::findOne(['user_id' => $user_id]);
			$date = time();
			$date_expire = strtotime('+1 month', $date);
			\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
					
					//активируем пользователю аккаунт Бизнес
					$user = User::findOne(['id' => $user_id]);
					if (is_null($user))
						return json_encode('Record not found');
					$user->account_id = User::ACCOUNT_BUSINESS;
//       			$user->save();
					if (!$user->save(false))
						return json_encode('Error while saving data');
					
                    $pay = new \app\models\Pay();
                    $pay->user_id = $user_id;
                    $pay->summa = 50;
                    $pay->date1_int = $date;
                    $pay->date2_int = $date_expire;
                    $pay->status = Pay::STATUS_CONFIRMED;
                    $pay->save();
                    $pay_id = $pay->id;
                    // update an existing row of data
//                    $customer = User::findOne($user_id);
//                    $pdf = $form->generatePdf($pay_id);
//                    $pdf_string = $pdf->output('', 'S');
                    $form->sendMessagePaySuccess($user_id, $pay_id); 
                        return $this->redirect('/user/profile?id='.$user_id);
//                    \Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
//                    return Pay::findOne($pay_id);
			
		}
	
	
	}
	
	
	
	
	public function actionPaypal(){
	
		
        if( isset($_GET['token']) && !empty($_GET['token']) ) { // Токен присутствует
            // Получаем детали оплаты, включая информацию о покупателе.
            // Эти данные могут пригодиться в будущем для создания, к примеру, базы постоянных покупателей
            $paypal = new Paypal();
			
            $checkoutDetails = $paypal -> request('GetExpressCheckoutDetails', array('TOKEN' => $_GET['token']));

            // Завершаем транзакцию
            $requestParams = array(
                'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',
                'PAYERID' => $_GET['PayerID']
            );

            $response = $paypal -> request('DoExpressCheckoutPayment',$requestParams);
            if( is_array($response) && $response['ACK'] == 'Success') { // Оплата успешно проведена
                // Здесь мы сохраняем ID транзакции, может пригодиться во внутреннем учете
                $transactionId = $response['PAYMENTINFO_0_TRANSACTIONID'];
            }
        }
        return $this->render("Платеж обрабатывается");
    }
	
	
	
	
	

    public function actionPartnership()
    {
        $user_id = Yii::$app->user->id;
        $form = new PartnershipForm();
        if (Yii::$app->getRequest()->isPost) {
            $form->load(Yii::$app->request->post());
            \Yii::$app->response->format = Response::FORMAT_JSON;
            if ($form->validate()) {
                return $form->sendMessage($user_id);
            } else {
                throw new HttpException(404, Yii::t('app', 'Incorrect form'));
            }
        } else {
            throw new HttpException(403, Yii::t('app', 'You don\'t have permissions'));
        }
    }
}