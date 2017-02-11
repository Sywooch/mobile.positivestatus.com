<?php
/**
 * @var $model User::findOne
 * @var $profile UserProfile::findOne
 * @var $contacts UserContact::findAll()
 */
    use app\components\Y;
    use app\models\User;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\widgets\ActiveForm;
    use yii\bootstrap\Modal;


    $form = ActiveForm::begin([
        'id' => 'profile_form',
        'action' => '',     //Url::to(['']),
        'ajaxDataType' => 'json',
        'ajaxParam' => 'ajax',
        'attributes' => [],     // client validation options for individual attributes
        'enableAjaxValidation' => false,
        'enableClientScript' => true,
        'enableClientValidation' => true,
        'errorSummaryCssClass' => 'alert alert-danger',
        //'fieldConfig' => function ($model, $attribute) {return [];} // default configuration used by field()
        'method' => 'post',
        'options' => [
            'class' => 'forma',
        ],
        'validateOnBlur' => false,
        'validateOnChange' => false,
        'validateOnSubmit' => true,
        'validateOnType' => false,
        'validationDelay' => 500,   // for validateOnType = true
        'validationUrl' => null,    // Url::to(['']),
    ]);

    $validation_errors = $form->errorSummary(array_merge([$model, $profile], $contacts), ['header' => false]);
    if (strpos($validation_errors, '<li>'))
        echo '<br />' .$validation_errors;

//    if (Yii::$app->session->hasFlash('profile_saved'))
//        echo Html::tag('div', Yii::$app->session->getFlash('profile_saved'), ['class' => 'alert alert-success']);

?>

    <aside class="left_s">
       <!-- <h2 class="profil_h2">
            <?= Yii::t('site', 'PROFILE') ?>&nbsp;&nbsp;<span>ID</span>&nbsp;<?= Y::getStrpadFromId($model->id) ?>
        </h2>  -->


        <!-- Email, Pass, Repeat pass -->
        <div class="obod">

            <label class="eml"> <?= $model->getAttributeLabel('email') ?>*
                <?= Html::activeTextInput($model, 'email') ?>
            </label>
            <label class="par1"> <?= $model->getAttributeLabel('pass') ?>*
                <?= Html::activePasswordInput($model, 'pass') ?>
            </label>
            <label class="par2"> <?= $model->getAttributeLabel('repeat_pass') ?>*
                <?= Html::activePasswordInput($model, 'repeat_pass') ?>
            </label>

            <?php if ($model->account_id != User::ACCOUNT_BASIC) : ?>
            <label class="firm"> <?= $model->getAttributeLabel('name') ?>*
                <?= Html::activeTextInput($model, 'name') ?>
            </label>
            <?php endif; ?>
        </div>


        <!-- Contacts -->
        <div class="ov heigh">
            <?php
                // I have to find the first key of $contacts array. It's not neccessary
                // to draw "Delete Contact" link in _account_contact view
                $keys = array_keys($contacts);
                foreach ($contacts as $n => $contact)
                    echo $this->render('_profile_contact', ['contact' => $contact, 'form' => $form, 'delete_contact' => ($n != $keys[0])]);
            ?>
        </div>  <!-- class="ov heigh"> -->


        <!-- Website, Facebook, Twitter. Add Link -->
        <?= $this->render('_profile_link', ['profile' => $profile]) ?>
         <!-- Schedule, W_hours, W_days -->
        <?= $this->render('_profile_schedule', ['profile' => $profile]) ?>
    </aside>



    <aside class="content mud flexo">
        <!-- Basic, Business, Partner -->
        <?= $this->render('_profile_plans', ['user' => $model, 'status_detail' => $status_detail,]) ?>


        <!-- Zip, Country, Address -->
        <?= $this->render('_profile_address', ['profile' => $profile]) ?>


        <!-- Map -->
        <?= $this->render('_profile_map', ['profile' => $profile]) ?>




        <button type="button" id="btn_profile_save" class="sohr"><?= Yii::t('site', 'SAVE') ?></button>
    </aside>  <!--class="content">-->

    <div class="clear"></div>
<?php ActiveForm::end(); ?>

<?php
Modal::begin([
    'id' => 'partnership_modal',
    'header' => '<h2>Заявка на партнерство</h2>',
//    'toggleButton' => ['label' => 'click me'],
]);


$formModel = new \app\models\PartnershipForm();
$formPartnership = ActiveForm::begin(

    [
        'id' => 'partnership_form',

        'action' => \yii\helpers\Url::toRoute('/user/partnership'),

        'options' => [

            'style' => 'padding-top:12px;'

        ]

    ]);
//echo $form->field($model, 'account_id')->hiddenInput()->label(false);

echo $formPartnership->field($formModel, 'message')->textarea(['rows'=> 7, 'resize'=>'none', 'value'=> 'Здравствуйте, мы хотели бы продавать запчасти по партнерской программе...'])->label(false);

echo '<div class="request_button_cnt">';

echo Html::submitButton(Yii::t('user', 'SEND_BID'), ['class' => 'btn btn-default request_button']);

echo '</div>';

// class register_button described at view/user/register_ini.php



ActiveForm::end();

Modal::end();

?>

<?php

$this->beginJs();
?>
    <script>
        $(document).on("submit", "#partnership_form", function (e) {

            e.preventDefault();

            var form = $(this);

            $.ajax({

                url: form.attr('action'),

                type: "POST",

                data: form.serialize(),

                success: function (response) {

                    if (response.result == 'ok') {

                        $("#register_modal").modal("hide");

                        var n = noty({

                            text: response.message,

                            layout: 'top',

                            closeWith: ['click', 'button'],

                            type: 'center',

                            theme: 'relax',

                        });

                        $("#noty_center_layout_container").css({'width':'80%', 'left':'10%'}).children().first().css('width', 'auto');

                    } else {

                        $("#partnership_form").find(".modal-body").html(response.message);

                    }

                }

            });

        });

    </script>

<?php $this->endJs();

    // Function setMapPositionByAddress() is in _profile_address.php
    $profile_js = '
        $("#btn_profile_save").on("click", function() {
            setMapPositionByAddress();
            window.onbeforeunload = null;
            $("#profile_form").submit();
        });
    ';

    if (Yii::$app->session->hasFlash('profile_saved')) {
        $profile_js .= '
            var n = noty({
                text: "' .Yii::$app->session->getFlash('profile_saved'). '",
                type: "success",
                layout: "center",
                timeout: 3000
            });
        ';
    }

    if ($model->scenario == 'activate') {
        $profile_js .= '
            window.onbeforeunload = function() {
                var message = "' .Yii::t('site', 'CONFIRM_PAGE_LEAVING'). ' ?";
                if (typeof evt == "undefined") { evt = window.event; }
                if (evt) { evt.returnValue = message; }
                return message;
            };
        ';
    }

    $this->registerJs($profile_js);
?>


<?php
    $this->registerJsFile('/js/adap_m.js', ['depends' => 'yii\web\JqueryAsset']);
    $this->registerJsFile('/js/start.js', ['depends' => 'yii\web\JqueryAsset']);
    $this->registerJsFile('js/noty-2.3.7/js/noty/packaged/jquery.noty.packaged.min.js', ['depends' => ['yii\web\JqueryAsset']]);