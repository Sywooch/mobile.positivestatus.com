<?php
use yii\captcha\Captcha;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var $contact app\models\UserContact
 */

$model = new \app\models\ContactForm([
    'trans_id' => Yii::$app->request->get('trans_id'),
    'user_id' => $contact->user_id,
    'contact_name' => $contact->name,
]);
?>

<a href="#" class="mail" id="show_forma">
    <?= Yii::t('site', 'WRITE_MESSAGE') ?>
</a>

<!-- form action="#" class="form" id="hidy_forma" -->
<?php
    $form = ActiveForm::begin([
        'id' => 'hidy_forma', 'enableClientValidation' => false,
        'options' => ['class' => 'form', ],
    ]);

    echo $form->field($model, 'trans_id')->hiddenInput()->label(false);
    echo $form->field($model, 'user_id')->hiddenInput()->label(false);
    echo $form->field($model, 'contact_name')->hiddenInput()->label(false);
    echo $form->field($model, 'contact_type')->hiddenInput()->label(false);
?>

    <div class="form_add">
        <div class="line dpoped"> <?= Yii::t('site', 'CONNECTION_TYPE') ?> <sub>*</sub>
            <span class="des">
                <span id="min_ind" class="pic">&nbsp;</span>
                <ul class="downbox addpole">
                    <li class="email">
                        <span class="ddlabel">Email</span>
                    </li>
                    <li class="phon">
                        <span class="ddlabel"> <?= Yii::t('site', 'PHONE') ?> </span>
                    </li>
                    <li class="skype">
                        <span class="ddlabel">Skype</span>
                    </li>
                    <li class="whatsap">
                        <span class="ddlabel">Whatsapp</span>
                    </li>
                    <li class="viber">
                        <span class="ddlabel">Viber</span>
                    </li>
                </ul>
            </span>

            <!-- input class="indx_inp" id="your_contact"  name="ind" value="" placeholder="Ваш email" --> <!-- не менять ID -->
            <?= Html::activeTextInput($model, 'contact_value', [
                'class' => 'indx_inp', 'id' => 'your_contact',
                'placeholder' => Yii::t('site', 'YOUR_EMAIL')
            ]) ?>
        </div> <!-- class="line"> -->

        <div class="line dpoped"> <?= Yii::t('site', 'YOUR_MESSAGE') ?> <sub>*</sub>
            <!--textarea name="msg" id="mesag" cols="30" rows="7"></textarea-->
            <?= Html::activeTextarea($model, 'message', ['id' => 'mesag', 'cols' => '30', 'rows' => '7']) ?>
        </div>

        <?php if (\app\components\Y::showCaptcha()) : ?>
        <div class="line dpoped"> <?= Yii::t('site', 'TYPE_CODE') ?> <sub>*</sub>
            <div class="row_captcha">
                <!--div class="cifra"> <img src="/images/baloon.png" alt=""></div-->
                <!--input type="text" id="chisl" name="chi" class="chislo" -->

                <?php
                    $title = Yii::t('site', 'CLICK_TO_REFRESH');
                    echo $form->field($model, 'verify_code')->widget(Captcha::className(), [
                        'template' => '<div>
                            <div class="captcha_img" title="' .$title. '">{image}</div>
                            <div class="captcha_input">{input}</div>
                        </div>',
                    ])->label(false);
                ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="alert alert-danger" style="display: none;"></div>

        <div class="line dpoped">
            <button type="button" class="sohr send_message"> <?= Yii::t('site', 'SEND_MESSAGE') ?> </button>
        </div>
    </div>

<?php ActiveForm::end(); ?>
<!-- /form -->
<br /><br />


<?php if (!Yii::$app->request->isPjax) : ?>
<?php $this->beginCss(); ?>
<style>
    div.captcha_img, div.captcha_input {float:left; width: 50%}
    div.captcha_img {margin-top: 7px; cursor:pointer}
    div.row_captcha {padding-bottom: 15px;}
</style>
<?php $this->endCss(); ?>
<?php endif; ?>


<?php if (!Yii::$app->request->isPjax) : ?>
<?php $this->beginJs(); ?>
<script>
    $("body").on("click", "button.send_message", function() {
        var form = $(this).closest("form");
        var contact_value_input = form.find("[name='ContactForm[contact_value]']");
        var contact_type = contact_value_input.attr("placeholder").split(' ')[1];
        form.find("[name='ContactForm[contact_type]']").val(contact_type);

        var data = form.serialize();
        var err_cnt = $(this).parent().siblings(".alert-danger");

        $.ajax({
            url: "<?= Url::to(['/auxx/send-message']) ?>",
            type: "post",
            dataType: "json",
            data: data,
            error: function (a,b,c) {
                alert(b);
            },
            success: function(obj) {
                if (obj.result == "err")
                    err_cnt.html(obj.message).show();
                if (obj.result == "ok") {
                    err_cnt.hide();
                    alert(obj.message);
                }
            }
        });
    });
</script>
<?php $this->endJs(); ?>
<?php endif; ?>
