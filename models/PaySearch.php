<?php



namespace app\models;



use Yii;

use yii\base\Model;

use yii\data\ActiveDataProvider;



class PaySearch extends Pay

{

    public function rules() {

        return [

            [['user_id', 'status'], 'integer'],

        ];

    }





    public function scenarios() {

        return Model::scenarios();

    }





    public function search($params) {

        $query = Pay::find()
//            ->with('profile')
;



        $pay_dp = new ActiveDataProvider([

            'query' => $query,

//            'sort' => [
//
//                'attributes' => [
//
//                    'user_id', 'status',
//
//                    'registered' => [
//
//                        'asc' => ['registered_at' => SORT_ASC],
//
//                        'desc' => ['registered_at' => SORT_DESC],
//
//                        'default' => SORT_ASC,
//
//                        'label' => 'Registered'
//
//                    ],
//
//                    'lastvisit' => [
//
//                        'asc' => ['lastvisit_at' => SORT_ASC],
//
//                        'desc' => ['lastvisit_at' => SORT_DESC],
//
//                        'default' => SORT_ASC,
//
//                        'label' => 'Last Visit'
//
//                    ],
//
//                ],
//
//                'defaultOrder' => ['registered' => SORT_DESC],
//
//            ],

            'pagination' => ['pageSize' => 20],

        ]);



        // load the seach form data and validate

        if (!($this->load($params) && $this->validate())) {

            return $pay_dp;
        }



        $query->andFilterWhere(['user_id' => $this->user_id]);

        $query->andFilterWhere(['status' => $this->status]);

        return $pay_dp;

    }

}

