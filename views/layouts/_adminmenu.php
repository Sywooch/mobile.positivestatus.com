<?php
    use yii\bootstrap\Nav;
    use yii\bootstrap\NavBar;
    use yii\helpers\Html;
    use yii\helpers\Url;


    NavBar::begin([
        'brandLabel' => '',
        'brandUrl' => null,
        'brandOptions' => ['style' => 'font-size: 30px;'],
        'options' => [
            'class' => 'navbar-default',
        ],
    ]);

    echo '<div>';
    echo Nav::widget([
        'options' => ['id' => 'adminmenu', 'class' => 'navbar-nav navbar-right'],
        'activateParents' => true,
        'items' => [
            [
                'options' => ['id' => 'admin_submenu'],
                'label' => Yii::t('site', 'ADMIN_SETTING'),
                'active' => false,
                'items' => [
                    ['label' => Yii::t('site', 'ADMIN_VIDTRANS'), 'url' => ['/admin/trans-cat'], 'active' => false],
                    ['label' => Yii::t('site', 'ADMIN_MODEL'), 'url' => ['/admin/models'], 'active' => false],
                    ['label' => Yii::t('site', 'ADMIN_FILTER'), 'url' => ['/admin/features'], 'active' => false],
                ]
            ],
            ['label' => Yii::t('site', 'ADMIN_USER'), 'url' => ['/admin/user']],
            ['label' => Yii::t('site', 'ADMIN_PAYMENT'), 'url' => ['/admin/payment']],
    //                        ['label' => Yii::t('site', 'ADMIN_BUY'), 'url' => ['/admin/buy']],
    //                        ['label' => Yii::t('site', 'ADMIN_SEARCH'), 'url' => ['/admin/search']],
    //                        ['label' => Yii::t('site', 'ADMIN_MESS'), 'url' => ['/admin/message']],
        ],
    ]);
    echo '</div>';

NavBar::end();