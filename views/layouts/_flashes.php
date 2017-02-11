<?php
    /**
     * Flash messages output
     * Following flash keys are available :
     * success_1, success_2, success_login, danger_1, danger_user_banned etc
     * The 1st word (success, danger etc) will become alert class
     */
    use yii\bootstrap\Alert;

    $s = Yii::$app->session;
    if (!$s->isActive || empty($s->allFlashes))
        return '';

    //BootstrapAsset::register($this);

    foreach ($s->allFlashes as $key => $message) {
        $a = explode('_', $key, 2);

        echo Alert::widget([
            'options' => ['class' => 'alert alert-' .$a[0]],
            'body' => $message,
        ]);
    }

    $s->removeAllFlashes();