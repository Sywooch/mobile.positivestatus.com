<?php

namespace app\controllers;

use app\models\User;
use Yii;
use app\components\Controller;
use app\models\Bookmark;
use app\models\Trans;
use yii\db\Query;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

class ClientController extends Controller {
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    ['allow' => true, 'roles' => ['@']]
                ],
            ],
        ];
    }
    
    
    public function actionList($account_id = false) {
        $user_id = Yii::$app->user->id;

        if ($account_id) {
            Yii::$app->user->identity->updateAttributes(['account_id' => $account_id]);
        }
        
        $trans_dp = new ActiveDataProvider([
            'query' => Trans::find()
                ->with(['brand', 'model', 'transmission', 'drive', 'interior', 'climate', 'fuel', 'category', 'wheel',
                    'user.profile', 'emission', 'sticker', 'cab', 'axle', 'bunk', 'hydraulic', 'lengthidval', 'licweight',
                    'load', 'seat'])
                ->where('user_id=:user_id', [':user_id'=>$user_id])
                ->orderBy('user_id DESC, pause DESC, date_int DESC'),
            'sort' => false,
            'pagination' => [
                'pagesize' => 5
            ],
        ]);

        Url::remember('', 'client_list');       // For coming back from Edit Page
        return $this->render('list', [
            'trans_dp' => $trans_dp, 'user_id' => $user_id, 
            'header' => Yii::t('client', 'PROPOSALS'), 'is_owner' => true
        ]);
    }



    public function actionBookmark() {
        $user_id = Yii::$app->user->id;
        $trans_ids = (new Query())->select(['trans_id'])
            ->from(Bookmark::tableName())->where(['user_id' => $user_id])->column();

        $trans_dp = new ActiveDataProvider([
            'query' => Trans::find()
                ->with(['brand', 'model', 'transmission', 'drive', 'interior', 'climate', 'fuel', 'category', 'wheel',
                    'user.profile', 'emission', 'sticker', 'cab', 'axle', 'bunk', 'hydraulic', 'lengthidval', 'licweight',
                    'load', 'seat'])
                ->where(['and', ['id' => $trans_ids], ['<', 'pause', time()]]),
            'sort' => false,
            'pagination' => [
                'pagesize' => 5
            ],
        ]);
        
        return $this->render('list', [
            'trans_dp' => $trans_dp, 'user_id' => $user_id, 
            'header' => Yii::t('site', 'BOOKMARK'), 'is_owner' => false
        ]);
    }
    
    

    // Edit existing proposal if $trans_id defined
    // Create new proposal if $trans_id = null
    public function actionEditProposal($trans_id = false, $cat_id = false) {
        if ($trans_id == false) {        // Create Mode
            $model = new Trans();
            $model->loadDefaultValues();
            
            $model->user_id = Yii::$app->user->id;
            $model->price_brut = $model->mileage = $model->capacity = $model->power  = $model->motohours = null;
            $model->cat_id = $cat_id ? $cat_id : null;
        }
        else {                          // Edit Mode
            $model = Trans::findOne($trans_id);
            if (is_null($model))
                throw new NotFoundHttpException();
        }
        
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            // Save model first to define model->id
            $model->save(false);
            
            // Copying photos from temp folder to photo folder, then delete temp folder
            $tmp_folder = Trans::getPhotoDir($model->user_id, true);
            if (file_exists($tmp_folder)) {
                $photo_folder = Trans::getPhotoDir($model->id, false);
                FileHelper::copyDirectory($tmp_folder, $photo_folder);
                FileHelper::removeDirectory($tmp_folder);
            }

            $back_url = is_null(Url::previous('client_list')) ? Url::to(['list']) : Url::previous('client_list');
            $this->redirect($back_url);
        }

        // Other proposals
        $other_proposals = Trans::find()->with(['brand', 'model'])
            ->where('user_id=:user_id', [':user_id' => $model->user_id])->asArray();

        if($trans_id) {
            $other_proposals->andWhere('id <> :trans_id', ['trans_id' => $trans_id]);
        }

        // Delete files from tmp folder (do it after $model->save())
        $tmp_folder = $model->getPhotoDir($model->user_id, true);
        FileHelper::removeDirectory($tmp_folder);

        return $this->render('edit_proposal', ['model' => $model, 'other_proposals' => $other_proposals->all(), 'cat_id' => $cat_id]);
    }

}
