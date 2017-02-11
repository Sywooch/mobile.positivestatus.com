<?php
use app\models\User;
use yii\helpers\Url;
?>

<section class="account">
    <div class="text-center slides container account__container">
        <div class="row account__row">
            <div class="col-lg-4 col-md-4 col-sm-4">
                <div class="white">
                    <div class="pic free">
                        <img src="/images/folder.svg" alt="">
                    </div>
                    <p class="p1"><?= Yii::t('user', 'ACCOUNT_BASIC') ?></p>
                    <p class="yell" style="line-height:66px "><?= Yii::t('user', 'FREE') ?></p>

                    <?= Yii::t('user', 'BLOCK_PRIVATE2') ?>

                    <button style="opacity:0" type="button" class="btn register_button" data-account_id="1">
                        <?= Yii::t('user', 'REGISTER_BUTTON') ?>
                    </button>
                </div>
            </div>

            <div class="col-lg-4 col-md-4 col-sm-4">
                <div class="white">
                    <div class="pic biz">
                        <img src="/images/briefcase.svg" alt="">
                    </div>
                    <p class="p1"><?= Yii::t('user', 'ACCOUNT_BUSINESS') ?></p>
                    <p class="yell y50"><span>€</span>50<br>
                        <i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= Yii::t('user', 'REGISTER_PERMONTH') ?></i>
                    </p>

                    <?= Yii::t('user', 'BLOCK_BUSINESS2') ?>

                    <a href="<?= Url::current(['account_id' => User::ACCOUNT_BUSINESS]) ?>">
                    <button type="button" class="btn register_button" data-account_id="2">
                        <?= Yii::t('user', 'REGISTER_BUTTON') ?>
                    </button>
                    </a>
                </div>
            </div>

            <div class="col-lg-4 col-md-4 col-sm-4">
                <div class="white">
                    <div class="pic partn">
                        <img src="/images/handshake.svg" alt="">
                    </div>
                    <p class="p1"><?= Yii::t('user', 'ACCOUNT_PARTNER') ?></p>
                    <p class="yell y50">%<br>
                        <i><?= Yii::t('user', 'REGISTER_DISCOUNT') ?></i>
                    </p>

                    <?= Yii::t('user', 'BLOCK_PARTNER2') ?>

                    <a href="<?= Url::current(['account_id' => User::ACCOUNT_PARTNER]) ?>">
                    <button type="button" class="btn register_button" data-account_id="3">
                        <?= Yii::t('user', 'REGISTER_BUTTON') ?>
                    </button>
                    </a>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4">

            </div>
        </div>
    </div>
</section>