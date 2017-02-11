<?php

namespace app\controllers;

use app\models\Bookmark;
use app\models\ContactForm;
use app\models\Pay;
use app\models\TransBrand;
use app\models\User;
use Yii;
use app\components\Controller;
use app\components\PhotoResizer;
use app\components\Y;
use app\models\Trans;
use app\models\TransFeature;
use app\models\TransFeatureH;
use app\models\TransModel;
use yii\filters\AccessControl;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\UploadedFile;


class AuxxController extends Controller 
{
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['get-model-dropdown-options', 'get-feature-dropdown-options', 'upload-handler'],
                        'allow' => true, 
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true, 
                        'verbs' => ['POST']
                    ],
                ],
            ],
        ];
    }

    public function actions() {
        return [
            'captcha' => [
                'class' => 'hr\captcha\CaptchaAction',
                'operators' => ['+','-','*'],
                'maxValue' => 10,
                'fontSize' => 20,
            ],
    ];
}

    ////////////////////////////////////////////////////////////////////////////
    
    public function actionDeleteModelById() {
        $model_name = Yii::$app->request->post('model_name');
        $model_id = Yii::$app->request->post('model_id');
        if (is_null($model_name) || is_null($model_id))
            return 'Incorrect incoming parameters';
        
        $model_name = 'app\\models\\' .$model_name;
        $model = $model_name::findOne($model_id);
        if (!is_null($model))
            $model->delete();

        return 'ok';
    }
    
    
    // Get required post parameter "model_name" (User for example) & model_data
    // Model_data is an array of attributes.
    // If Model_data[id] is absent or empty - creating the new Record
    // otherwise - updating existing record
    // $view param could be false, 'model_id' or view filename
    public function actionUpdateModel($view=false, $validate=false) {
        $model_name = Yii::$app->request->post('model_name');
        $model_data = Yii::$app->request->post($model_name);
        if (is_null($model_name) || is_null($model_data))
            return json_encode('Incorrect incoming parameters');  
        
        $full_model_name = 'app\\models\\' .$model_name;
        if (empty($model_data['id']))
            $model = new $full_model_name();
        else 
            $model = $full_model_name::findOne($model_data['id']);
        
        $model->load(Yii::$app->request->post());
       
        if ($validate && !$model->validate())
            return json_encode($this->getStringModelErrors($model));
        elseif (!$model->save(false))
            return json_encode('Error while saving data');
        elseif (!$view)
            return json_encode('ok');
        elseif ($view == 'model_id')
            return json_encode(['result' => 'ok', 'model_id' => $model->id]);
        else {
            $html = $this->renderPartial($view, ['model' => $model]);
            return json_encode(['result' => 'ok', 'html' => $html]);
        }
    }


    public function actionDeleteBookmark() {
        $user_id = Yii::$app->request->post('user_id');
        $trans_id = Yii::$app->request->post('trans_id');
        if (is_null($user_id) || is_null($trans_id))
            return 'Incorrect incoming parameters';

        $model = Bookmark::findOne(['user_id' => $user_id, 'trans_id' => $trans_id]);
        if (is_null($model))
            return 'Record not found';

        $model->delete();
        return 'ok';
    }


    public function actionConfirmPayment() {
        $pay_id = Yii::$app->request->post('pay_id');
        $user_id = Yii::$app->request->post('user_id');

        if (is_null($pay_id) || is_null($user_id))
            return json_encode('Incorrect incoming parameters');



        $user = User::findOne(['id' => $user_id]);
            if (is_null($user))
                return json_encode('Record not found');
        $user->account_id = User::ACCOUNT_BUSINESS;
//        $user->save();
        if (!$user->save(false))
            return json_encode('Error while saving data');

        $pay = Pay::findOne(['id' => $pay_id]);
        if (is_null($pay))
            return json_encode('Record not found');
        $pay->status = Pay::STATUS_CONFIRMED;
        $pay->save();
        return json_encode("ok");
    }
	
	
	
	public function actionConfirmTestPayment() {
        
        $user_id = Yii::$app->request->post('user_id');
		
		$date = time();
        $date_expire = strtotime('+1 month', $date);

        if ( is_null($user_id))
            return json_encode('Incorrect incoming parameters');

		$pay = new \app\models\Pay();
        $pay->user_id = $user_id;
        $pay->summa = 50;
        $pay->date1_int = $date;
        $pay->date2_int = $date_expire;
        $pay->status = Pay::STATUS_CONFIRMED;
        $pay->save();
		
		$pays_test = Pay::find()->indexBy('id')->where(['status' => Pay::STATUS_TEST, 'user_id' => $user_id])->all();
        
		foreach ($pays_test as $pay){
		
			$pay->delete();
			
		}
		
        return json_encode("ok");
    }
	

    
    protected function getStringModelErrors($model) {
        $ret = '';
        foreach ($model->errors as $error) {
            foreach ($error as $mess)
                $ret .= $mess .'<br />';
        }
        
        return trim($ret, '<br />');
    }
    
    
    
    ////////////////////////////////////////////////////////////////////////////
    public function actionChangeAvatar() 
    { 
        $contact_id = (int)Yii::$app->request->post('contact_id');
        if (empty($contact_id))
            return json_encode(['result'=>'error', 'message'=>'Parameter contact_id not found']);
        
        $uploadedFile = UploadedFile::getInstanceByName('userfile');
        
        if (is_null($uploadedFile))
            return json_encode(['result'=>'error', 'message'=>"Parameter userfile not found"]);
        if (!in_array($uploadedFile->type, ['image/gif', 'image/jpeg', 'image/png']))
            return json_encode(['result'=>'error', 'message'=>Yii::t('site', 'IMAGE_EXTENSIONS')]);
       
        $aSizes = Y::getAvatarSize();
        $ext = empty($uploadedFile->extension) ? '' : '.' .$uploadedFile->extension;
        $fileName = Y::getAvatarDir() .DIRECTORY_SEPARATOR .Y::getStrpadFromId($contact_id) .$ext;

        $resizer = new PhotoResizer($uploadedFile->tempName, $fileName, $aSizes['width'], $aSizes['height'], 2);

        if($resizer->resize()) {
            $newUrl = Y::getAvatarUrl() .Y::getStrpadFromId($contact_id) .$ext .'?rand='.  uniqid();
            return json_encode(['result'=>'ok', 'avatar_url'=>$newUrl]);
        } else
            return json_encode(['result'=>'error', 'message'=>"Ошибка обработки файла"]);
    }    
    
    
    
    ////////////////////////////////////////////////////////////////////////////
    public function actionGetModelDropdownOptions($brand_id) {
        $models = TransModel::find()->where('brand_id = :brand_id', [':brand_id' => $brand_id])
            ->orderBy('name')->all();
        
        $options = '<option value="0" disabled="disabled" style="color:#AAA">' .Yii::t('admin', 'LABEL_MODEL'). '</option>';
        foreach ($models as $model)
            $options .= '<option value="' .$model['id']. '" style="color:#000;">' .$model['name']. '</option>';
        
        return json_encode(['result'=>'ok', 'options'=>$options]);
    }
    
    
    public function actionGetFeatureDropdownOptions($subcat_id) {
        $lang = Yii::$app->language;
        $models = TransFeature::find()->where('subcat_id = :subcat_id', [':subcat_id' => $subcat_id])
            ->orderBy($lang)->all();
        
        $options = '<option value="0" disabled="disabled" style="color:#AAA">' .Yii::t('client', 'CATEGORY_ID'). '</option>';
        foreach ($models as $model)
            $options .= '<option value="' .$model['id']. '" style="color:#000;">' .$model[$lang]. '</option>';
        
        return json_encode(['result'=>'ok', 'options'=>$options]);
    }
    
    
    public function actionUploadPhotos() {
        if (!Yii::$app->request->isAjax)
            throw new CException('Incorrect request type');

        if (empty($_FILES['trans_photos']))
            return('{}');

        // Incoming parameters
        $trans_id = Yii::$app->request->post('trans_id');
        $tmp = Yii::$app->request->post('tmp');
        if (is_null($trans_id) || is_null($tmp))
            return json_encode(['result'=>'error', 'message'=>'Incorrect incoming parameters']);

        // Upload errors or invalid extension
        $uploadedFiles = UploadedFile::getInstancesByName('trans_photos');
        $messages = [];
        foreach ($uploadedFiles as $file) {
            if ($file->hasError)
                $messages[] = $file->name .'. ' .Yii::t('site', 'UPLOAD_ERR');

            if (!in_array($file->type, ['image/gif', 'image/jpeg', 'image/png']))
                $messages[] = $file->name .'. ' .Yii::t('site', 'IMAGE_EXTENSIONS');
        }

        if (!empty($messages))
            return ['message' => implode('<br />', $messages)];


        // Resizing and saving
        $largeSizes = Yii::$app->params['photoBig'];          // ['width'=>nn, 'height'=>nn]
        $smallSizes = Yii::$app->params['photoSmall'];
        $photo_dir = Trans::getPhotoDir($trans_id, $tmp);
        $photo_url = Trans::getPhotoUrl($trans_id, $tmp);
        $ini_p = $ini_conf = [];   // initialPreview, initialPreviewConfig

        foreach ($uploadedFiles as $file) {
            $ext = empty($file->extension) ? '' : '.' .$file->extension;
            $fName = uniqid() .$ext;

            // Large photo
            $fileName = $photo_dir .'lg_' .$fName;
            $resizer = new PhotoResizer($file->tempName, $fileName, $largeSizes['width'], $largeSizes['height'], 2);
            if(!$resizer->resize()) {
                $messages[] = $file->name . '. ' . Yii::t('site', 'UPLOAD_ERR');
                continue;
            }

            // Small photo
            $fileName = $photo_dir .'sm_' .$fName;
            $resizer = new PhotoResizer($file->tempName, $fileName, $smallSizes['width'], $smallSizes['height'], 2);
            if(!$resizer->resize()) {
                $messages[] = $file->name . '. ' . Yii::t('site', 'UPLOAD_ERR');
                continue;
            }

            $ini_p[] = Html::img($photo_url .'sm_' .$fName, ['data-filename' => 'sm_' .$fName]);
            $ini_conf[] = ['key' => 'sm_' .$fName];
            unlink($file->tempName);
        }

        $ret = ['initialPreview' => $ini_p, 'initialPreviewConfig' => $ini_conf];
        if (!empty($messages))
            $ret['messages'] = implode('<br />', $messages);

        return json_encode($ret);
    } 
    
    
    public function actionDeletePhoto() {
        $sm_file = Yii::$app->request->post('key');     // Here we get a small image file_path WITHOUT "main_" prefix
        $trans_id = Yii::$app->request->post('trans_id');
        $tmp = Yii::$app->request->post('tmp');
        if (is_null($sm_file) || is_null($trans_id) || is_null($tmp))
            return json_encode('Incorrect incoming parameters');

        $photo_path = Trans::getPhotoDir($trans_id, $tmp);
        @unlink($photo_path .$sm_file); @unlink($photo_path .'main_' .$sm_file);
        $lg_file = substr_replace($sm_file, 'lg_', 0, 3);
        @unlink($photo_path .$lg_file); @unlink($photo_path .'main_' .$lg_file);
        return json_encode('ok');
    }
    
    
    public function actionDeleteAllPhotos() {
        $trans_id = Yii::$app->request->post('trans_id');
        $tmp = Yii::$app->request->post('tmp');
        if (is_null($trans_id) || is_null($tmp))
            return 'Incorrect incoming parameters'; 
        
        $dir = Trans::getPhotoDir($trans_id, $tmp);
        FileHelper::removeDirectory($dir);
        return  'ok';
    }


    public function actionSetMainPhoto() {
        $trans_id = Yii::$app->request->post('trans_id');
        $tmp = Yii::$app->request->post('tmp');
        $new_filename = Yii::$app->request->post('new_filename');     // Here is a small image file_path
        if (is_null($new_filename) || is_null($trans_id) || is_null($tmp))
            return 'Incorrect incoming parameters';

        $dir = Trans::getPhotoDir($trans_id, $tmp);

        // Remove prefix "main_" for $old_filename (if exists)
        $files = glob($dir .'main_' .'*.*');
        foreach ($files as $file) {
            $new_name = substr_replace(basename($file), '', 0, 5);
            rename($file, $dir.$new_name);
        }

        // Add prefix "main_" for $new_filename
        rename($dir.$new_filename, $dir.'main_'.$new_filename);     // Small photo
        $new_filename = substr_replace($new_filename, 'lg_', 0, 3);
        rename($dir.$new_filename, $dir.'main_'.$new_filename);     // Large photo
        return 'ok';
    }
    
    

    public function actionGetModelDropdownHtml() {
        $brand_id = Yii::$app->request->post('brand_id');
        $add_new_model = Yii::$app->request->post('add_new_model', false);
        if (is_null($brand_id))
            return json_encode(['result' => 'err', 'message' => 'Incorrect incoming parameters']);

        return json_encode(['result' => 'ok', 'html' => TransModel::getDropdownHtml($brand_id, $add_new_model)]);
    }


    public function actionSetTransPause() {
        $trans_id = Yii::$app->request->post('trans_id');
        $strtotime_param = Yii::$app->request->post('param');
        if (is_null($trans_id) || is_null($strtotime_param))
            return 'Incorrect incoming parameters';

        $new_pause = ($strtotime_param == 'reset_to_zero') ? 0 : strtotime($strtotime_param);
        $q = Trans::updateAll(['pause' => $new_pause], ['id' => $trans_id]);
        return $q ? 'ok' : 'Error while updating database';
    }


    // Return Json::encode(array)
    // [brand_id => &html, category_id=>&html, transmiss_id=>&html, fuel_id=>&html, interior_id=>&html] ... NO MODEL_ID
    public function actionGetProposalFormDropdowns() {
        $cat_id = Yii::$app->request->post('cat_id');
        $brand_id = Yii::$app->request->post('brand_id');
        $get_year_month = Yii::$app->request->post('get_year_month');
        $add_new_model = Yii::$app->request->post('add_new_model', false);
        if (is_null($cat_id))
            return Json::encode(['result' => 'err', 'message' => 'Incorrect incoming parameters']);

        $ret = [];

        // Brands
        $brand_dropdown = TransBrand::getDropdownHtml($cat_id);
        if (!empty($brand_dropdown))
            $ret['brand_id'] = $brand_dropdown;

        // Models
        if (!empty($brand_id)) {
            $model_dropdown = TransModel::getDropdownHtml($brand_id, $add_new_model);
            if (!empty($model_dropdown))
                $ret['model_id'] = $model_dropdown;
        }

        // Categories, Transmission, Fuel, Interior (not empty only)
        $features = TransFeatureH::getDropdownSrc($cat_id);
        if (!empty($features))
            $ret = array_merge($ret, $features);

        // Years, Monthes
        if (!is_null($get_year_month) && $get_year_month == '1') {
            $ret['year'] = Y::getYearDropDownHtml();
            $ret['month'] = Y::getMonthDropDownHtml();
        }

        $ret['result'] = 'ok';
        return Json::encode($ret);
    }


    public function actionSendMessage() {
        $model = new ContactForm();
        if (!$model->load(Yii::$app->request->post()))
            return json_encode(['result' => 'err', 'message' => Yii::t('site', 'INCORRECT_PARAMS')]);

        if (!$model->validate())
            return json_encode(['result' => 'err', 'message' => Y::getArErrors($model)]);

        return json_encode($model->sendMessage());
    }


    public function actionAddNewModel() {
        $brand_id = Yii::$app->request->post('brand_id');
        $name = Yii::$app->request->post('name');
        if (is_null($brand_id) || is_null($name))
            return 'Incorrect incoming parameters';

        $model = new TransModel(['brand_id' => $brand_id, 'name' => $name]);
        if ($model->save())
            return $model->id;

        return Y::getArErrors($model);
    }
}
