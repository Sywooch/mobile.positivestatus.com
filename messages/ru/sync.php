<?php
    $br = Yii::$app->request->isAjax ? "\r\n" : '<br />';
    
    return [
        'SYNC_HEADER'               => 'Синхронизация предложений',
        'SYNCHRONIZATION'           => 'Синхронизация',
        'SYNCHRONIZE'               => 'Синхронизировать',
        'SYNCHRONIZE_TOUPPER'       => 'СИНХРОНИЗИРОВАТЬ',

        'STRING1'                   => '1. Ваш CustomerID вы можете узнать по телефону: +49 30 81097 - 560 (Montag - Freitag, 8:00 - 18:00 Uhr)',
        'STRING2'                   => '2. Для получения логина и пароля авторизуйтесь и перейдите по ссылке:',

        'PRE_SYNC_VALIDATE'         => 'Необходимо ввести все учетные данные : \\r\\n'
                                       .'Логин, Пароль и CustomerID',
    ];

