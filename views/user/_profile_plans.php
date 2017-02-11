<?php /** * @var $user User */
use app\models\User;
use  yii\helpers\Url;

$user_id = Yii::$app->user->id;
$class_basic = ($user->account_id == User::ACCOUNT_BASIC) ? 'plans__item plans__item_active' : 'plans__item';
$class_business = ($user->account_id == User::ACCOUNT_BUSINESS) ? 'plans__item plans__item_active' : 'plans__item';
$class_partner = ($user->account_id == User::ACCOUNT_PARTNER) ? 'plans__item plans__item_active' : 'plans__item';
$label_basic = Yii::t('user', 'ACCOUNT_PLAN') . ' ' . Yii::t('user', 'ACCOUNT_BASIC');
$label_business = Yii::t('user', 'ACCOUNT_PLAN') . ' ' . Yii::t('user', 'ACCOUNT_BUSINESS');
$label_partner = Yii::t('user', 'ACCOUNT_PLAN') . ' ' . Yii::t('user', 'ACCOUNT_PARTNER');
$radio_selected = ($user->account_id == User::ACCOUNT_BASIC) ? ' checked="checked" ' : ' disabled="disabled" '; ?>
<div class="plans">
    <div class="<?= $class_basic ?>">
            <h3 class="plans__pl-title plans__pl-title_basik">
                <span class="plans__round plans__round_sizem">
                    <svg width="60" height="60">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#folder"></use>
                    </svg>
                </span>

                      <?= $label_basic ?>
            </h3>
        <p class="plans__text"><?= Yii::t('user', 'TEXT_BASIC') ?></p>
        <div class="plans__chek-wrap">
            <ul class="plans__ulul">
                <li><input type="radio" id="f-basik2" name="selector" <?= $radio_selected ?>> <label class="plans__pays"
                                                                                                     for="f-basik2"><?= Yii::t('user', 'FREE') ?></label>
                    <div class="check"></div>
                </li>
            </ul>
        </div> <!-- plans__chek-wrap -->    </div>
    <div class="<?= $class_business ?>"><h3
                class="plans__pl-title plans__pl-title_bissnes">
                <span class="plans__round plans__round_sizem">
                    <svg width="64" height="64">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#portfolio"></use>
                    </svg>
                </span>
                            <?= $label_business ?>        </h3>
        <p class="plans__text"><?=$status_detail ?></p>
        <div class="plans__chek-wrap">
            <ul class="plans__ulul">
                <li><input type="radio" id="f-biss2" name="selector"> <label class="plans__pays"
                                                                             for="f-biss2"><?= Yii::t('user', 'FIFTY_EURO') ?></label>
                    <div class="plans__coopons">
                        <a href="<?= Url::to(['payment', 'id' => $user_id]) ?>" class="plans__buy">
                            <i class="plans__buy-pic"></i> <?= Yii::t('user', 'BUY') ?>
                        </a>
                        <a href="/" class="plans__get-coopon">
                            <i class="plans__get-coopon-pic"></i> <?= Yii::t('user', 'COUPON') ?>
                        </a>
                    </div>
                </li>
            </ul>
        </div> <!-- plans__chek-wrap -->    </div>
    <div class="<?= $class_partner ?>">
        <h3 class="plans__pl-title plans__pl-title_partner">
             <span class="plans__round">
                    <svg width="64" height="64">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#hands"></use>
                    </svg>
                </span>

                           <?= $label_partner ?>        </h3>
        <p class="plans__text"><?= Yii::t('user', 'TEXT_PARTNER') ?></p>
        <div class="plans__chek-wrap">
            <ul class="plans__ulul">
                <li><input type="radio" id="f-partn2" name="selector"> <label class="plans__pays"
                                                                              for="f-partn2"><?= Yii::t('user', 'COMISSION') ?></label>
                    <a href="#" class="plans__send-link" data-toggle="modal"
                       data-target="#partnership_modal"><?= Yii::t('user', 'SEND_BID') ?></a></li>
            </ul>
        </div> <!-- plans__chek-wrap -->    </div>
</div> <!-- <div  class="plans"> -->