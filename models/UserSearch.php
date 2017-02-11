<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class UserSearch extends User 
{
    public function rules() {
        return [
            ['email', 'string', 'max' => 100],
            [['account_id', 'role_id', 'status'], 'integer'],
        ];
    }
    
    
    public function scenarios() {
        return Model::scenarios();
    }
    
    
    public function search($params) {
        $query = User::find()->with('profile', 'pays');
        
        $user_dp = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'account_id', 'role_id', 'email', 'status',
                    'registered' => [
                        'asc' => ['registered_at' => SORT_ASC],
                        'desc' => ['registered_at' => SORT_DESC],
                        'default' => SORT_ASC,
                        'label' => 'Registered'
                    ], 
                    'lastvisit' => [
                        'asc' => ['lastvisit_at' => SORT_ASC],
                        'desc' => ['lastvisit_at' => SORT_DESC],
                        'default' => SORT_ASC,
                        'label' => 'Last Visit'                        
                    ],
                ],
                'defaultOrder' => ['registered' => SORT_DESC],
            ],
            'pagination' => ['pageSize' => 20],
        ]);  
        
        // load the seach form data and validate
        if (!($this->load($params) && $this->validate())) {
            return $user_dp;
        }   
        
        
        $query->andFilterWhere(['like', 'email', $this->email]);
        $query->andFilterWhere(['account_id' => $this->account_id]);
        $query->andFilterWhere(['role_id' => $this->role_id]);
        $query->andFilterWhere(['status' => $this->status]);
        return $user_dp;
    }
}
