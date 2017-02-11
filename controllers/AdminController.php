<?php
namespace app\controllers;
use app\models\PaySearch;
use app\models\TransCatGroup;
use Yii;
use app\components\Controller;
use yii\filters\AccessControl;
use app\models\User;
use app\models\UserSearch;
use app\models\TransCat;
use app\models\TransSubcat;
use app\models\TransBrand;
use app\models\TransModel;
use app\models\TransFeatureH;
use app\models\TransFeature;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
class AdminController extends Controller {
    public $layout = 'admin';

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'matchCallback' => function($rule, $action) {
                            return isset(Yii::$app->user->identity) && (Yii::$app->user->identity->role_id == User::ROLE_ADMIN);
                        }
                    ]
                ],
            ],
        ];
    }

    public function actionIndex() {
        return $this->renderContent('<div style="padding-top: 150px; text-align:center">Welcome to Admin Zone</div>');
    }

    public function actionTransCat($cat_id = false) {
        $lang = Yii::$app->language;
        $cat_dp = new ActiveDataProvider([
            'query' => TransCat::find(),
            'sort' => false,
            'pagination' => false,
        ]);

        // Need to take $cat_dp first model key & name If $cat_id is not determined
        $cat_name = false;

        $cat_blocked = true;

        if(!$cat_id && !empty($cat_dp->models)) {

            $cat_id = $cat_dp->models[0]->id;

            $cat_name = $cat_dp->models[0]->$lang;

            $cat_blocked = $cat_dp->models[0]->blocked;

        } else {

            foreach ($cat_dp->models as $m)

                if ($m->id == $cat_id) {

                    $cat_name = $m->$lang;

                    $cat_blocked = $m->blocked;

                    break;

                }

        }



        $group_dp = new ActiveDataProvider([

            'query' => TransCatGroup::find()->orderBy([$lang => SORT_ASC]),

            'sort' => false,

            'pagination' => false,            

        ]);

        

        return $this->render('trans_cat', ['cat_id' => $cat_id, 'cat_name' => $cat_name,

            'cat_blocked' => $cat_blocked, 'cat_dp' => $cat_dp, 'group_dp' => $group_dp]);

    }

    

    

 

    public function actionModels($cat_id = false, $brand_id = false) {

        ////////////////////////////////////////////////////////////////////////

        //      The same fragment as in actionFeatures() below

        // Category Drodpdown source

        $lang = Yii::$app->language;

        //$cat_dd = TransCat::find()->select(['id', $lang])->where(['excluded' => 0])->all();

        $cat_dd = TransCat::find()->select(['id', $lang])->all();

        $cat_dd = ArrayHelper::map($cat_dd, 'id', $lang);

        

        // Will take $cat_dd first key If $cat_id is not determined

        if (!$cat_id && !empty($cat_dd)) {

            $cat_keys = array_keys($cat_dd);

            $cat_id = $cat_keys[0];

        }

        ////////////////////////////////////////////////////////////////////////

        

        $brand_dp = new ActiveDataProvider([

            'query' => TransBrand::find()->where(['cat_id' => $cat_id])->orderBy(['name' => SORT_ASC]),

            'sort' => false,

            'pagination' => ['pageSize' => 20],

        ]);

        

        // Will take $brand_dp first model key & name If $brand_id is not determined

        $brand_name = false;

        if(!$brand_id && !empty($brand_dp->models)) {

            $brand_id = $brand_dp->models[0]->id;

            $brand_name = $brand_dp->models[0]->name;

        } else {

            foreach ($brand_dp->models as $m)

                if ($m->id == $brand_id) {

                    $brand_name = $m->name;

                    break;

                }

        }

        

            

        $model_dp = new ActiveDataProvider([

            'query' => TransModel::find()->where(['brand_id' => $brand_id])->orderBy(['name' => SORT_ASC]),

            'sort' => false,

            'pagination' => ['pageSize' => 20],            

        ]);

        

        return $this->render('trans_models', ['cat_id' => $cat_id, 'brand_id' => $brand_id,

            'brand_name' => $brand_name, 'cat_dd' => $cat_dd, 'brand_dp' => $brand_dp, 'model_dp' => $model_dp]);

    }

    

    

    public function actionFeatures($cat_id = false, $head_id = false) {

        ////////////////////////////////////////////////////////////////////////

        //      The same fragment as in actionModels() above

        // Category Drodpdown source

        $lang = Yii::$app->language;

        //$cat_dd = TransCat::find()->select(['id', $lang])->where(['excluded' => 0])->all();

        $cat_dd = TransCat::find()->select(['id', $lang])->all();

        $cat_dd = ArrayHelper::map($cat_dd, 'id', $lang);

        

        // Need to take $cat_dd first key If $cat_id is not determined

        if (!$cat_id && !empty($cat_dd)) {

            $cat_keys = array_keys($cat_dd);

            $cat_id = $cat_keys[0];

        }

        ////////////////////////////////////////////////////////////////////////

   

        

        $head_dp = new ActiveDataProvider([

            'query' => TransFeatureH::find()->where(['cat_id' => $cat_id])->orderBy([$lang => SORT_ASC]),

            'sort' => false,

            'pagination' => false,

        ]);

        

        // Need to take $head_dp first model key & name If $head_id is not determined

        $head_name = false;

        if(!$head_id && !empty($head_dp->models)) {

            $head_id = $head_dp->models[0]->id;

            $head_name = $head_dp->models[0]->$lang;

        } else {

            foreach ($head_dp->models as $m)

                if ($m->id == $head_id) {

                    $head_name = $m->$lang;

                    break;

                }

        }

        

        

        $feature_dp = new ActiveDataProvider([

            'query' => TransFeature::find()->where(['hid' => $head_id])->orderBy([$lang => SORT_ASC]),

            'sort' => false,

            'pagination' => false,            

        ]);

        

        return $this->render('trans_features', ['cat_id' => $cat_id, 'head_id' => $head_id,

            'head_name' => $head_name, 'cat_dd' => $cat_dd, 'head_dp' => $head_dp, 'feature_dp' => $feature_dp]);

    }

    

    

    

    public function actionUser() {

        $searchModel = new UserSearch();

        $user_dp = $searchModel->search(Yii::$app->request->get());

        return $this->render('users', ['user_dp' => $user_dp, 'searchModel' => $searchModel]);

    }

    

    

    

    public function actionPayment() {

        $searchModel = new PaySearch();

        $pay_dp = $searchModel->search(Yii::$app->request->get());

//        $query = Department::find()->with('user');
//        $dataProvider = new ActiveDataProvider(['query' => $query]);

        return $this->render('payment', ['pay_dp' => $pay_dp, 'searchModel'=> $searchModel]);

    }

    

    

    

    public function actionBuy() {

        return $this->renderContent('Buy page');

    }

    

    

    public function actionSearch() {

        return $this->renderContent('Search page');

    }

    

    

    public function actionMessage() {

        return $this->renderContent('Message page');

    }

    

}

