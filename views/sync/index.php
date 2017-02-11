<?php
/**
 * Parameters :
 * $model - Profile->findOne(['user_id' => Yii::$app->user->id]);
 */
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\widgets\Pjax;
?>


<div class="content v100">
    <h2 class="img_h2 synk"> <?= Yii::t('sync', 'SYNC_HEADER') ?> </h2>

    <div class="add_fots sinko">
        <?php Pjax::begin(['id' => 'syncform_pjax']); ?>

        <?= Html::beginForm('', 'post', ['class' => 'forma', 'id' => 'syncform']) ?>
            <img src="/images/mobde.png" width="178" height="44" alt=""/>

            <label> <?= $model->getAttributeLabel('mobile_customer_id') ?><br>
                <?= Html::activeTextInput($model, 'mobile_customer_id') ?>
            </label>

            <label> <?= $model->getAttributeLabel('mobile_login') ?><br>
                <?= Html::activeTextInput($model, 'mobile_login') ?>
            </label>

            <label> <?= $model->getAttributeLabel('mobile_pass') ?><br>
                <?= Html::activePasswordInput($model, 'mobile_pass') ?>
            </label>

            <label>
                <?= Html::button(Yii::t('sync', 'SYNCHRONIZE_TOUPPER'), ['id' => 'syncform_btnok', 'type' => 'button', 'class' => 'btn', 'style' => 'cursor: pointer']); ?>
            </label>

        <?= Html::endForm() ?>

        <?php Pjax::end(); ?>
    </div>  <!--class="add_fots">-->

    <div class="sync_wrp">
        <p> <?= Yii::t('sync', 'STRING1') ?> </p>
        <p>
            <?= Yii::t('sync', 'STRING2') ?> <br>
            <?= Html::a(Yii::$app->params['mobileAuthUrl'], Yii::$app->params['mobileAuthUrl'], ['target' => '_blank']) ?>
        </p>
        <img src="/images/sync.jpg" width="810" height="360" alt=""/>
    </div>
</div>  <!--class="content">-->



<?php if (!Yii::$app->request->isPjax) : ?>
<?php $this->beginJs(); ?>
<script>
    $('#syncform_btnok').on('click', function(e) {
        e.preventDefault();
        var form = $(this).closest("form");
        if (!confirm("<?= Yii::t('user', 'SYNC_CONFIRM') ?>"))
            return false;


        $.blockUI({ message: "<?= Yii::t('site', 'PROCESSING') ?>", css: {"padding":"12px 48px"}});
        $.ajax({
            url: form.attr("action"),
            type: "post",
            data: form.serialize(),
            error: function (jqXHR, textStatus, errorThrown) {
                $.unblockUI();
                var n = noty({
                    text: textStatus,
                    type: "error",
                    layout: "center",
                    closeWith: ['click', 'button']
                });
            },
            success: function(response) {
                $.unblockUI();
                var n = noty({
                    text: response,
                    type: "success",
                    layout: "center",
                    closeWith: ['click', 'button'],
                    callback: {
                        onClose: function() {window.location.assign("<?= Url::to(['/client/list']) ?>")},
                    }
                });
            }
        });
    });
</script>
<?php $this->endJs(); ?>
<?php endif; ?>


<?php
if (!Yii::$app->request->isPjax) {
    $this->registerJsFile('/js/jquery.blockUI.js', ['depends' => ['yii\web\JqueryAsset']]);
    $this->registerJsFile('js/noty-2.3.7/js/noty/packaged/jquery.noty.packaged.min.js', ['depends' => ['yii\web\JqueryAsset']]);
}