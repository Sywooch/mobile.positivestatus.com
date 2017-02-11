<?php

namespace app\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "{{%bookmark}}".
 *
 * @property string $id
 * @property string $user_id
 * @property string $trans_id
 */
class Bookmark extends \yii\db\ActiveRecord
{
    public static function tableName() {
        return '{{%bookmark}}';
    }

    public function rules() {
        return [
            [['user_id', 'trans_id'], 'required'],
            [['user_id', 'trans_id'], 'integer', 'min' => 1],
        ];
    }

    public function attributeLabels() {
        return [
            'id' => 'ID',
            'user_id' => 'User',
            'trans_id' => 'Trans',
        ];
    }

    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getTrans() {
        return $this->hasOne(Trans::className(), ['id' => 'trans_id']);
    }

    ///////////////////////////////////////////////////////////////////////////
    //              EVENTS
    ///////////////////////////////////////////////////////////////////////////
    public function afterSave($insert, $changedAttributes) {
        if ($insert)
            User::updateAllCounters(['bmark' => 1], ['id' => $this->user_id]);

        parent::afterSave($insert, $changedAttributes);
    }


    public function afterDelete() {
        User::updateAllCounters(['bmark' => -1], ['id' => $this->user_id]);
        parent::afterDelete();
    }
    

    ///////////////////////////////////////////////////////////////////////////
    //              OTHER
    ///////////////////////////////////////////////////////////////////////////
    public static function countUserBookmarks($user_id = false) {
        if (!$user_id && Yii::$app->user->isGuest)
            return 0;

        $user_id = $user_id ? (int)$user_id : Yii::$app->user->id;
        return self::find()->where('user_id=:user_id', [':user_id' => $user_id])->count();
    }
}
