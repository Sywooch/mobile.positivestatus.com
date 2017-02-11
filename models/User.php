<?php

namespace app\models;

use Yii;
use app\components\Y;
use app\models\Pay;
use yii\base\InvalidParamException;
use yii\db\Query;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\IdentityInterface;


/**
 * This is the model class for table "{{%user}}".
 *
 * @property string $id
 * @property integer $account_id
 * @property integer $legal_status_id
 * @property integer $role_id
 * @property string $name
 * @property string $email
 * @property string $pass_hash
 * @property integer $registered_at
 * @property integer $lastvisit_at
 * @property integer $cnt
 * @property integer $bmark
 * @property string $auth_key
 * @property integer $status
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_BLOCKED = 2;
    const ACCOUNT_BASIC = 1;
    const ACCOUNT_BUSINESS = 2;
    const ACCOUNT_PARTNER = 3;
    const LEGAL_STATUS_PERSON = 1;
    const LEGAL_STATUS_FIRM = 2;
    const ROLE_USER = 1;
    const ROLE_ADMIN = 9;

    private static $_statuses = [];
    private static $_accounts = [];
    private static $_legal_statuses = [];
    private static $_roles = [];

    public function __construct($config = array()) {
        if (empty(self::$_statuses)) {
            self::$_statuses[self::STATUS_INACTIVE] = Yii::t('user', 'STATUS_INACTIVE');
            self::$_statuses[self::STATUS_ACTIVE]   = Yii::t('user', 'STATUS_ACTIVE');
            self::$_statuses[self::STATUS_BLOCKED]  = Yii::t('user', 'STATUS_BLOCKED');
        }

        if (empty(self::$_accounts)) {
            self::$_accounts[self::ACCOUNT_BASIC] = Yii::t('user', 'ACCOUNT_BASIC');
            self::$_accounts[self::ACCOUNT_BUSINESS] = Yii::t('user', 'ACCOUNT_BUSINESS');
            self::$_accounts[self::ACCOUNT_PARTNER] = Yii::t('user', 'ACCOUNT_PARTNER');
        }

        if (empty(self::$_legal_statuses)) {
            self::$_legal_statuses[self::LEGAL_STATUS_PERSON] = Yii::t('user', 'LEGAL_STATUS_PERSON');
            self::$_legal_statuses[self::LEGAL_STATUS_FIRM] = Yii::t('user', 'LEGAL_STATUS_FIRM');
        }

        if (empty(self::$_roles)) {
            self::$_roles[self::ROLE_USER] = Yii::t('user', 'ROLE_USER');
            self::$_roles[self::ROLE_ADMIN] = Yii::t('user', 'ROLE_ADMIN');
        }

        parent::__construct($config);
    }

    public static function getStatuses() {
        return self::$_statuses;
    }

    public static function getStatus($code) {
        $code = (int)$code;
        if (!array_key_exists($code, self::$_statuses))
            throw new InvalidParamException('Invalid parameter value');

        return self::$_statuses[$code];
    }

    public static function getAccounts() {
        return self::$_accounts;
    }

    public static function getAccount($code) {
        $code = (int)$code;
        if (!array_key_exists($code, self::$_accounts))
            throw new InvalidParamException('Invalid parameter value');

        return self::$_accounts[$code];
    }

    public static function getRoles() {
        return self::$_roles;
    }

    public static function getRole($code) {
        $code = (int)$code;
        if (!array_key_exists($code, self::$_roles))
            throw new InvalidParamException('Invalid parameter value');

        return self::$_roles[$code];
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public $pass;
    public $repeat_pass;

    public static function tableName() {
        return '{{%user}}';
    }

    public function scenarios() {
        return \yii\helpers\ArrayHelper::merge(
            parent::scenarios(),
            [
                'register' => ['email', 'account_id'],
                'activate' => ['email'],
                'edit' => ['name', 'email', 'pass', 'repeat_pass'],
                'forgot_pass' => ['email'],
                'recovery' => ['name', 'email', 'pass', 'repeat_pass'],
            ]
        );
    }

    public function rules() {
        return [
            ['legal_status_id', 'required', 'on' => 'register'],
            [['account_id', 'legal_status_id', 'status'], 'integer', 'min' => 0],
            ['name', 'required', 'when' => function($model) { return $model->account_id != self::ACCOUNT_BASIC; }, 'on' => 'default'],
            ['name', 'trim'],
            ['name', 'string', 'max' => 100],
            ['email', 'required'],
            ['email', 'string', 'max' => 100],
            ['email', 'email'],
            ['email', 'unique', 'except' => 'forgot_pass'],
            ['email', 'exist', 'on' => 'forgot_pass'],
            ['pass', 'string', 'min' => 6],
            ['pass', 'match', 'pattern' => '/^[A-Za-z0-9_-]*$/', 'message' => Yii::t('user', 'RULE_PASS')],
            [['pass', 'repeat_pass'], 'required', 'on' => ['activate', 'recovery']],
            ['repeat_pass', 'compare', 'compareAttribute' => 'pass', 'message' => Yii::t('user', 'RULE_REPEATPASS')],
        ];
    }

    public function attributeLabels() {
        return [
            'id' => 'ID',
            'account_id' => Yii::t('user', 'LABEL_ACCTYPE'),
            'legal_status_id' => Yii::t('user', 'LABEL_LEGAL_STATUS'),
            'role_id' => Yii::t('user', 'LABEL_ROLE'),
            'name' => Yii::t('user', 'LABEL_COMPANY_NAME'),
            'email' => 'Email',
            'status' => Yii::t('user', 'LABEL_STATUS'),
            'pass' => Yii::t('user', 'LABEL_PASS'),
            'repeat_pass' => Yii::t('user', 'LABEL_REPEATPASS'),
        ];
    }

    public function getProfile() {
        return $this->hasOne(UserProfile::className(), ['user_id' => 'id']);
    }

    public function getContacts() {
        return $this->hasMany(UserContact::className(), ['user_id' => 'id']);
    }

    public function getTranses() {
        return $this->hasMany(Trans::className(), ['user_id' => 'id']);
    }

    public function getBookmarks() {
        return $this->hasMany(Bookmark::className(), ['user_id' => 'id']);
    }

    public function getPays() {
        return $this->hasOne(Pay::className(), ['user_id' => 'id']);
//        return $this->find()->orderBy('date1_int DESC')->one();
    }
//    public function getBookmarkCount() {
//        return (new Query())->from(Bookmark::tableName())->where(['user_id' => $this->id])->count();
//    }
//
//    public function getTransCount() {
//        return (new Query())->from(Trans::tableName())->where(['user_id' => $this->id])->count();
//    }

    ///////////////////////////////////////////////////////////////////////////
    //              EVENTS
    ///////////////////////////////////////////////////////////////////////////
    public function beforeSave($insert) {
        if (!parent::beforeSave($insert))
            return false;

        if ($this->scenario=='activate' && !empty($this->pass)) {
            $this->status = self::STATUS_ACTIVE;
            $this->registered_at = time();
            $this->auth_key = Yii::$app->security->generateRandomString();
        }

        if (!empty($this->pass)) {
            $this->auth_key = Yii::$app->security->generateRandomString();
            $this->pass_hash = $this->setPasswordHash($this->pass);
        }

        return true;
    }


    public function afterDelete() {
        $this->profile->delete();

        foreach ($this->contacts as $contact)
            $contact->delete();

        foreach ($this->transes as $trans)
            $trans->delete();

        foreach ($this->bookmarks as $bookmark)
            $bookmark->delete();

        parent::afterDelete();
    }



    ///////////////////////////////////////////////////////////////////////////
    //              IDENTITY
    ///////////////////////////////////////////////////////////////////////////
    public static function findIdentity($id) {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null) {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Find model by email.
     * @param string $email Email
     * @param string $scope Scope
     * @return array|\yii\db\ActiveRecord[] User
     */
    public static function findByEmail($email, $scope = null) {
        $query = static::find()->where(['email' => $email]);
        if ($scope !== null) {
            if (is_array($scope)) {
                foreach ($scope as $value) {
                    $query->$value();
                }
            } else {
                $query->$scope();
            }
        }
        return $query->one();
    }

    public function getId() {
        return $this->id;
    }

    public function getAuthKey() {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey) {
        return $this->auth_key === $authKey;
    }

    public function validatePassword($password) {
        return Yii::$app->security->validatePassword($password, $this->pass_hash);
    }

    public function setPasswordHash($password) {
        return Yii::$app->security->generatePasswordHash($password);
    }

    ///////////////////////////////////////////////////////////////////////////
    //              OTHER
    ///////////////////////////////////////////////////////////////////////////
    public function getRegistered() {
        return Yii::$app->formatter->asDate($this->registered_at, 'short');
    }

    public function getLastvisit() {
        return Yii::$app->formatter->asDate($this->lastvisit_at, 'short');
    }
    /**
     * @param bool|false $date_format
     * @return false if not found | int if $date_format=false | otherwise formatter->asDate($date_format)
     */
    public function getPayedTo($date_format = false) {
        if (!in_array($this->account_id, array_keys(Y::getPayDetails())))
            return false;

        $payedTo = (new Query())->from(Pay::tableName())
            ->where(['user_id' => $this->id, 'status' => Pay::STATUS_CONFIRMED])
            ->max('date2_int');

//        if (!$payedTo || $payedTo < time())
        if (!$payedTo)
            return false;
//        return ($date_format) ? Yii::$app->formatter->asDate($payedTo, $date_format) : $payedTo;
        return ($date_format) ? Yii::$app->formatter->asDate($payedTo, $date_format) : Yii::$app->formatter->asDate($payedTo, 'short') ;
    }
    public function getOpenInvoice($date_format = false){

        $openInvoice = (new Query())->from(Pay::tableName())
            ->where(['user_id' => $this->id, 'status' => Pay::STATUS_NOTCONFIRMED])
            ->max('date1_int');

        if (!$openInvoice)
            return false;

//        $days_limit = Pay::getDate1();
//        return ($date_format) ? Yii::$app->formatter->asDate($payedTo, $date_format) : $payedTo;
        return ($date_format) ? Yii::$app->formatter->asDate($openInvoice, $date_format) : Yii::$app->formatter->asDate($openInvoice, 'short') ;
    }
    public function getIsActice(){
        return $this->hasOne(Pay::className(), ['user_id'=>'id'])->orderBy('date1_int DESC')->one();
    }


    public function getLastPayment(){
//        $payment = (new Query())->from(Pay::tableName())
//            ->where(['user_id' => $this->id, 'status' => Pay::STATUS_CONFIRMED])
//            ->max('date1_int')->one();
        return $this->hasOne(Pay::className(), ['user_id' => 'id'])->where(['status' => Pay::STATUS_CONFIRMED])->orderBy('date1_int DESC')->one();

//        return $payment;
    }
    public function getLastOpenInvoice(){

        return $this->hasOne(Pay::className(), ['user_id' => 'id'])->where(['status' => Pay::STATUS_NOTCONFIRMED])->orderBy('date1_int DESC')->one();

//        return $payment;
    }

//    public  function getLastOpenInvoice(){
//        $openInvoice = (new Query())->from(Pay::tableName())
//            ->where(['user_id' => $this->id, 'status' => Pay::STATUS_NOTCONFIRMED])
//            ->max('date1_int');
//
//        return Yii::$app->formatter->asDate($openInvoice, 'short');
//    }


    public function sendRegistrationMail() {
        $this->auth_key = Yii::$app->security->generateRandomString();  // will be saved later (see user/register)
        $this->pass_hash = $this->setPasswordHash(uniqid());            // will be saved later
        $activation_url = Url::toRoute(['user/activate', 'email' => $this->email, 'auth_key' => $this->auth_key], true);
        $activation_link = Html::a($activation_url, $activation_url);

        return Yii::$app->mailer->compose()
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setTo($this->email)
            ->setSubject(Yii::t('user', 'REGISTRATIONMAIL_SUBJECT'))
            ->setHtmlBody(Yii::t('user', 'REGISTRATIONMAIL_BODY') .$activation_link)
            ->send();
    }

    public function sendRecoveryMail() {
        $recovery_url = Url::toRoute(['user/recovery', 'email' => $this->email, 'auth_key' => $this->auth_key], true);
        $recovery_link = Html::a($recovery_url, $recovery_url);

        return Yii::$app->mailer->compose()
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setTo($this->email)
            ->setSubject(Yii::t('user', 'RECOVERY_SUBJECT'))
            ->setHtmlBody(Yii::t('user', 'RECOVERY_BODY') .$recovery_link)
            ->send();
    }

}
