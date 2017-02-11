<?php

// Parameters :

// $pay_dp - Pay ActiveDataProvider

// $searchModel - PaySearch Model for the filter

use app\models\Pay;

use app\models\User;

use yii\grid\GridView;

use yii\helpers\Html;

use yii\helpers\Url;

use yii\widgets\Pjax;



echo Html::tag('h4', Yii::t('admin', 'PAY_LIST'));

echo GridView::widget([

    'id' => 'user_grid',

    'dataProvider' => $pay_dp,

    'filterModel' => $searchModel,

    'filterPosition' => GridView::FILTER_POS_HEADER,

    'layout' => '{errors} {items} {pager}',

//    'rowOptions' => function ($model, $key, $index, $grid) {
//
//        return ['user_email' => $model->email];
//
//    },

    'columns' => [
        'id',
        'user_id',
        'summa',
        'date1_int' => ['attribute' => 'date1', 'label' => Yii::t('user', 'Оплачено')],
        'date2_int' => ['attribute' => 'date2', 'label' => Yii::t('user', 'Окончание')],
        'status',

        [

            'content' => function($model, $key, $index, $column) {

                $a_text = Html::tag('span', '', ['class' => 'glyphicon glyphicon-remove']);

                return Html::a($a_text, '#', ['class' => 'delete_user_link', 'title' => Yii::t('site', 'DELETE')]);

            },

        ],

    ],

]);


?>

<!-- <br /><br /><br /><br /><br />

<div style="color:#E00; line-height: 26px">

    Здесь админка, верстка строгая не нужна. Просто нарисуй как страница должна выглядеть. Слева таблица Юзеров,

    справа Платежи, или как? Поиск по имени? по емайлу? по номеру счета? Сейчас на странице Профиля вообще

    наименования никакого нет, непонятно как искать. <br />

    Как платежи помечать те которые пейпалом и те которые руками заведены? Какие можно редактировать и удалять,

    или менять даты От-До. Расписывай и разрисовывай все подробно. Рисовать можешь хоть карандашом, только потом

    отсканируй, чтобы можно было приложить рисунок к описанию <br /><br />



    В предыдцщем ТЗ обсуждался какой-то модуль какой-то посторонней компании, который по твоим словами должен

    был просто прикрутиться. Сейчас все по-другому, значит это новое ТЗ.

</div> -->