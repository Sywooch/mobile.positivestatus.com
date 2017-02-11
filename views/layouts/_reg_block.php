<?php
/**
 * Parameters :
 * $renderHtml - boolean
 * $renderJs - boolean
 */
    use yii\bootstrap\Modal;
    use yii\helpers\Url;
    use yii\web\View;

    $renderHtml = isset($renderHtml) && ($renderHtml == true);
    $renderJs = isset($renderJs) && ($renderJs == true);
?>


<?php if ($renderHtml) : ?>
    <div class="container text-center slides">
        <h2><?= Yii::t('user', 'SELECT_TYPE') ?></h2>
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-4">
                <div class="white">
                    <div class="pic free"></div>
                    <p class="p1"><?= Yii::t('user', 'ACCOUNT_BASIC') ?></p>
                    <p class="yell" style="line-height:66px "><?= Yii::t('user', 'FREE') ?></p>
                    <?= Yii::t('user', 'BLOCK_PRIVATE2') ?>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4">
                <div class="white">
                    <div class="pic biz"></div>
                    <p class="p1"><?= Yii::t('user', 'ACCOUNT_BUSINESS') ?></p>
                    <p class="yell y50"><span>€</span>50<br>

                        <i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= Yii::t('user', 'REGISTER_PERMONTH') ?></i></p>
                    <?= Yii::t('user', 'BLOCK_BUSINESS2') ?>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4">
                <div class="white">
                    <div class="pic partn"></div>
                    <p class="p1"><?= Yii::t('user', 'ACCOUNT_PARTNER') ?></p>
                    <p class="yell y50">%<br>
                        <i><?= Yii::t('user', 'REGISTER_DISCOUNT') ?></i>
                    </p>
                    <?= Yii::t('user', 'BLOCK_PARTNER2') ?>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4">

            </div>
        </div>
    </div>

    <div class="text-center">
        <button type="button" class="btn register_button"><?= Yii::t('user', 'REGISTER_BUTTON') ?></button>
    </div>
<?php endif; ?>

