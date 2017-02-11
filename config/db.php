<?php

if(LOCALHOST) {
    return [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=localhost;dbname=dominicana_mobil',
        'username' => 'dominicana_mobil',
        'password' => 'rYaK5koTHv',
        'charset' => 'utf8',
        'tablePrefix' => 'tbl_',
        'enableSchemaCache' => false,
    ];
} else {
    return [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=localhost;dbname=dominicana_mobil',
        'username' => 'dominicana_mobil',
        'password' => 'rYaK5koTHv',
        'charset' => 'utf8',
        'tablePrefix' => 'tbl_',
        'enableSchemaCache' => true,
    ];    
}
