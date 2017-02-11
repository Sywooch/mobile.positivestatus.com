<?php

namespace app\controllers;

use Yii;
use app\components\Synchronizer;
use app\components\Y;
use app\models\User;
use app\models\UserProfile;
use yii\filters\AccessControl;
use yii\web\Controller;


class SyncController extends Controller {
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }
    
    
    public function actionUpdateModels() {
//        if (!Yii::$app->request->isAjax)
//            return 'Incorrect request type';
        if (Yii::$app->user->identity->role_id != User::ROLE_ADMIN)
            return 'You don\'t have permission to access this action';
        
        return Synchronizer::updateModels();
    }
    
    
    // !!! Use for starting project only. Don't use for synchronization in the future !!!
    public function actionUpdateReferences() {
        // Parameters - Climate, drive, transmission, interior, fuel, body, wheel, emission, sticker, cab
        $mess = Synchronizer::updateReferences(true, true, true, true, true, true, true, true, true, true);
        return $this->renderContent($mess);
    }
    
    
    // !!! Use for starting project only. Don't use for synchronization in the future !!!
    public function actionUpdateCategories() {
        $mess = Synchronizer::updateCategories();
        return $this->renderContent($mess);
    }
    
    
    ////////////////////////////////////////////////////////////////////////////
    //                  MOBILE.DE - SYNCHRONIZATION
    public function actionIndex() {
        $model = UserProfile::findOne(['user_id' => Yii::$app->user->id]);
        $model->setScenario('sync');

        // Next block for request->isAjax only
        if ($model->load(Yii::$app->request->post())) {
            if (!$model->validate())
                return Y::getArErrors($model);

            $model->save(false);
            Yii::$app->user->identity->profile->mobile_customer_id = $model->mobile_customer_id;
            Yii::$app->user->identity->profile->mobile_login = $model->mobile_login;
            Yii::$app->user->identity->profile->mobile_pass = $model->mobile_pass;
            $user_id = Yii::$app->user->id;

            try {
                $ad_keys = Synchronizer::mobileDePrepare();
                if (empty($ad_keys)) {
                    Synchronizer::mobileDe_delete($user_id);
                    return Yii::t('site', 'NO_RECORDS_FOUND');
                }

                Synchronizer::mobileDe_delete($user_id, $ad_keys);
                $message = Synchronizer::mobileDe($ad_keys);
            }
            catch (\yii\base\Exception $e) {
                $message = $e->getMessage();
            }

            return nl2br($message);
        }

        return $this->render('index', ['model' => $model]);
    }


//    public function actionMobileDe() {
//        $mobile_customer_id = Yii::$app->request->post('mobile_customer_id');
//        $mobile_login = Yii::$app->request->post('mobile_login');
//        $mobile_pass = Yii::$app->request->post('mobile_pass');
//
//        Yii::$app->db->createCommand()->update(User::tableName(), [
//            'mobile_customer_id' => $mobile_customer_id,
//            'mobile_login' => $mobile_login,
//            'mobile_pass' => $mobile_pass
//        ], 'id='.Yii::$app->user->id)->execute();
//
//        Yii::$app->user->identity->mobile_customer_id = $mobile_customer_id;
//        Yii::$app->user->identity->mobile_login = $mobile_login;
//        Yii::$app->user->identity->mobile_pass = $mobile_pass;
//        $user_id = Yii::$app->user->id;
//
//        try {
//            $ad_keys = Synchronizer::mobileDePrepare();
//            if (empty($ad_keys)) {
//                Synchronizer::mobileDe_delete($user_id);
//                return json_encode(['result' => 'err', 'message' => Yii::t('site', 'NO_RECORDS_FOUND')]);
//            }
//
//            Synchronizer::mobileDe_delete($user_id, $ad_keys);
//            $message = Synchronizer::mobileDe($ad_keys);
//        }
//        catch (\yii\base\Exception $e) {
//            $message = $e->getMessage();
//        }
//
//        return $message;
//    }
}
