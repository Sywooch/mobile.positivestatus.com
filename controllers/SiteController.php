<?php

namespace app\controllers;

use Yii;
use app\components\Controller;
use app\models\ContactForm;
use app\models\TransCat;
use app\models\Trans;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\web\NotFoundHttpException;
use yii\web\Response;


class SiteController extends Controller
{
    public $defaultAction = 'start';

    public function actionStart() {
        return $this->renderFile('@webroot/land/index.php');
    }

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'hr\captcha\CaptchaAction',
                'operators' => ['+','-','*'],
                'maxValue' => 10,
                'fontSize' => 20,
            ],
        ];
    }

    
    ////////////////////////////////////////////////////////////////////////////
    // Proposal's pages
    public function actionProposal($trans_cat, $brand_id = false, $map = false) {
        $transcat_model = TransCat::findOne(['get_param' => $trans_cat]);
        if (is_null($transcat_model))
            throw new NotFoundHttpException();

        $transcat_model->getSliderValues();     // min_year, max_year, min_price, max_price

        $with = $map ? ['brand', 'model'] : array_merge(Trans::getFullWith(), ['bookmarkExists']);
        $query = Trans::find()->with($with)->joinWith(['user.profile']);
        if ($brand_id)
            $query->where('brand_id=:brand_id AND pause<' .time())->params([':brand_id'=>$brand_id])->orderBy('brand_id DESC, date_int DESC');
        else 
            $query->where('cat_id=:cat_id AND pause<' .time())->params([':cat_id'=>$transcat_model->id])->orderBy('cat_id DESC, date_int DESC');
        
        // Additional filter conditions can be get in $_POST
        $filt = new Trans(['scenario' => 'filter']);
        if ($filt->load(Yii::$app->request->post())) {


            $query->andFilterWhere([
                'brand_id' => $filt->brand_id,
                'category_id' => $filt->category_id,
                'transmiss_id' => $filt->transmiss_id,
                'fuel_id' => $filt->fuel_id,
                'interior_id' => $filt->interior_id,
                'wheel_id' => $filt->wheel_id,
                'climate_id' => $filt->climate_id,
                'emission_id' => $filt->emission_id,
                'sticker_id' => $filt->sticker_id,
                'cab_id' => $filt->cab_id,
                'axle_id' => $filt->axle_id,
                'bunk_id' => $filt->bunk_id,
                'hydraulic_id' => $filt->hydraulic_id,
                'length_id' => $filt->length_id,
                'licweight_id' => $filt->licweight_id,
                'load_id' => $filt->load_id,
                'seat_id' => $filt->seat_id,
                'length' => $filt->length,
                'motohours' => $filt->motohours,
            ]);

            if ($filt->model_id > 0)
                $query->andFilterWhere(['model_id' => $filt->model_id]);
            if ($filt->nds_only)
                $query->andWhere(['>', 'nds', 0]);
            if (!empty($filt->country_code))
                $query->andWhere('{{%user_profile.country}}="'.$filt->country_code .'"');

            $slider_years = explode(';', $filt->slider_years);
            $slider_prices = explode(';', $filt->slider_prices);

            if ($transcat_model->min_year <> $slider_years[0])
                $query->andWhere(['>=', 'year', $slider_years[0]]);
            if ($transcat_model->max_year <> $slider_years[1])
                $query->andWhere(['<=', 'year', $slider_years[1]]);
            if ($transcat_model->min_price <> $slider_prices[0])
                $query->andWhere(['>=', 'price_brut', $slider_prices[0]]);
            if ($transcat_model->max_price <> $slider_prices[1])
                $query->andWhere(['<=', 'price_brut', $slider_prices[1]]);
        }
        
        
        if ($map) {
            $models = $query->all();
            $this->layout = 'fullscreen_map';
            return $this->render('proposal_map', ['trans_models' => $models, 'transcat_model' => $transcat_model, 'filter_model' => $filt]);
        }
        
        // if $map == false
        $trans_dp = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => [
                'pagesize' => 5
            ],
        ]);

        Url::remember('', 'proposal');      // For coming back from Details Page
        return $this->render('proposal', ['trans_dp' => $trans_dp, 'transcat_model' => $transcat_model, 'filter_model' => $filt]);
    }
    
    
    ////////////////////////////////////////////////////////////////////////////
    public function actionDetails($trans_id) {
        $model = Trans::find()->where(['id' => $trans_id])
            ->with(Trans::getFullWith())->one();
        $model->updateCounters(['click' => 1]);

        return $this->render('details', ['model' => $model]);
    }
    
    
    ////////////////////////////////////////////////////////////////////////////
    public function actionContact() {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    public function actionAbout() {
        return $this->render('about');
    }
    
    
    public function actionAjaxValidation()  {
        if (!Yii::$app->request->isAjax)
            return json_encode('Incorrect request');
            
        $model_name = Yii::$app->request->post('model_name');
        $model_scenario = Yii::$app->request->post('model_scenario');
        if (is_null($model_name) || is_null($model_scenario))
            return json_encode('Incorrect incoming parameters');
        

        $model = new $model_name(['scenario' => $model_scenario]);
        if (is_null($model))
            return json_encode('Incorrect parameter value');

        
        $model->load(Yii::$app->request->post());
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ActiveForm::validate($model);
    }
    
    
    public function actionPhpInfo() {
        return $this->renderContent(phpinfo());
    }
}
