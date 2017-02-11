<?php 
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    
    
    echo Yii::t('user', 'REGISTER_EMAIL');

    $form = ActiveForm::begin(
        [
            'id' => 'register_email_form', 
            'enableClientValidation' => false,
            'enableAjaxValidation' => false,
            'validateOnBlur' => false,
            'validateOnChange' => false,
            'options' => [    
                'style' => 'padding-top:12px;'
            ]
    ]);

    echo $form->field($model, 'account_id')->hiddenInput()->label(false);
    echo $form->field($model, 'email', [
        'inputOptions' => ['class'=>'form-control', 'placeholder' => 'Email'],
    ])->label(false);
    
    echo '<div class="register_button_cnt">';
    echo Html::submitButton(Yii::t('user', 'REGISTER_BUTTON'), ['class' => 'btn btn-default register_button']);
    echo '</div>';
    // class register_button described at view/user/register_ini.php

    ActiveForm::end();
?>