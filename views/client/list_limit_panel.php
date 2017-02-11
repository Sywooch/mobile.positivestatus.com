<?php
// Parameters
// $user_id     - Yii::$app->user->id
// $isPrivateAccount - boolean

use app\models\Trans;
use app\models\TransCat;
use app\models\TransCatGroup;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$user_id = Yii::$app->user->id;
$proposals = (new Query())->from(Trans::tablename())->where(['user_id' => $user_id])->count();

if ($proposals < 3) {
    $lang = Yii::$app->language;
    $categories = (new Query())->select(['id', 'group_id', 'get_param', 'name' => $lang])->from(TransCat::tableName())->all();
    $cat_ids = ArrayHelper::map($categories, 'get_param', 'id');
}
?>



<?php if ($isPrivateAccount) : ?>
<div class="counter">
    <?php if ($proposals == 0) : ?>
        <div class="counter__svetofor">
            <i class="counter__circle"></i>
            <i class="counter__circle"></i>
            <i class="counter__circle"></i>
        </div>
        <div class="counter__text"><?= Yii::t('client', '3_PROP_LEFT') ?></div>
    <?php endif; ?>

    <?php if ($proposals == 1) : ?>
        <div class="counter__svetofor">
            <i class="counter__circle green"></i>
            <i class="counter__circle"></i>
            <i class="counter__circle"></i>
        </div>
        <div class="counter__text"><?= Yii::t('client', '2_PROP_LEFT') ?></div>
    <?php endif; ?>

    <?php if ($proposals == 2) : ?>
        <div class="counter__svetofor">
            <i class="counter__circle yellow"></i>
            <i class="counter__circle yellow"></i>
            <i class="counter__circle"></i>
        </div>
        <div class="counter__text"><?= Yii::t('client', '1_PROP_LEFT') ?></div>
    <?php endif; ?>

    <?php if ($proposals >= 3) : ?>
        <div class="counter__svetofor">
            <i class="counter__circle red"></i>
            <i class="counter__circle red"></i>
            <i class="counter__circle red"></i>
        </div>
        <div class="counter__tex redss"><?= Yii::t('client', '0_PROP_LEFT') ?></div>
    <?php endif; ?>
</div>

    <p class="levlel-margin">
        <?php $url = Url::to(['user/profile', 'id' => $user_id]) ?>
        <?= Html::a(Yii::t('client', 'CHANGE_STATUS'), $url, ['class' => 'js-level-up']) ?>
    </p>
<?php endif; ?>



<?php if (!$isPrivateAccount || $proposals < 3) : ?>
<div class="add-prop add-pro__unlim_end">
    <?php if ($proposals < 3) : ?>
        <p class="add-prop__title"><?= Yii::t('client', 'ADD_PROPOSAL') ?></p>

        <nav class="add-prop__main-ul">
            <li class="add-prop__main-li">
                <a class="add-prop__main-a add-prop__main-lcars" href="<?= Url::to(['client/edit-proposal', 'trans_id' => false, 'cat_id' => $cat_ids['cars']]) ?>"></a>
            </li>

            <li class="add-prop__main-li">
                <a class="add-prop__main-a add-prop__main-bus" href="#"></a>

                <div class="add-prop__downwrap">
                    <div class="add-prop__choose"><?= Yii::t('client', 'SELECT_CATEGORY') ?></div>

                    <ul class="add-prop__level2">
                        <?php
                            foreach ($categories as $cat) {
                                if ($cat['group_id'] == TransCatGroup::BUS_ID) {
                                    $url = Url::to(['client/edit-proposal', 'trans_id' => false, 'cat_id' => $cat_ids[$cat['get_param']]]);
                                    echo '<li class="add-prop__level2-li">'
                                            . '<a  class="add-prop__level2-a" href="' .$url. '"> ' .$cat['name']. ' </a>'
                                            . '</li>';
                                }
                            }
                        ?>
                    </ul>

                    <p class="add-prop__back"><?= Yii::t('client', 'COME_BACK') ?></p>
                </div>  <!--"add-prop__downwrap">-->
            </li>

            <li class="add-prop__main-li">
                <a class="add-prop__main-a add-prop__main-cargo" href="#"></a>

                <div class="add-prop__downwrap">
                    <div class="add-prop__choose"><?= Yii::t('client', 'SELECT_CATEGORY') ?></div>

                    <ul class="add-prop__level2">
                        <?php
                        foreach ($categories as $cat) {
                            if ($cat['group_id'] == TransCatGroup::TRUCK_ID) {
                                $url = Url::to(['client/edit-proposal', 'trans_id' => false, 'cat_id' => $cat_ids[$cat['get_param']]]);
                                echo '<li class="add-prop__level2-li">'
                                    . '<a  class="add-prop__level2-a" href="' .$url. '"> ' .$cat['name']. ' </a>'
                                    . '</li>';
                            }
                        }
                        ?>
                    </ul>

                    <p class="add-prop__back"><?= Yii::t('client', 'COME_BACK') ?></p>
                </div>  <!--"add-prop__downwrap">-->
            </li>

            <li class="add-prop__main-li">
                <a class="add-prop__main-a add-prop__main-spec" href="#"></a>

                <div class="add-prop__downwrap">
                    <div class="add-prop__choose"><?= Yii::t('client', 'SELECT_CATEGORY') ?></div>

                    <ul class="add-prop__level2">
                        <?php
                        foreach ($categories as $cat) {
                            if ($cat['group_id'] == TransCatGroup::SPEC_ID) {
                                $url = Url::to(['client/edit-proposal', 'trans_id' => false, 'cat_id' => $cat_ids[$cat['get_param']]]);
                                echo '<li class="add-prop__level2-li">'
                                    . '<a  class="add-prop__level2-a" href="' .$url. '"> ' .$cat['name']. ' </a>'
                                    . '</li>';
                            }
                        }
                        ?>
                    </ul>

                    <p class="add-prop__back"><?= Yii::t('client', 'COME_BACK') ?></p>
                </div>  <!--"add-prop__downwrap">-->
            </li>

            <li class="add-prop__main-li">
                <a class="add-prop__main-a add-prop__main-motos" href="<?= Url::to(['client/edit-proposal', 'trans_id' => false, 'cat_id' => $cat_ids['bikes']]) ?>"></a>
            </li>

            <li class="add-prop__main-li">
                <a class="add-prop__main-a add-prop__main-sea" href="#"></a>

                <div class="add-prop__downwrap">
                    <div class="add-prop__choose"><?= Yii::t('client', 'SELECT_CATEGORY') ?></div>

                    <ul class="add-prop__level2">
                        <?php
                        foreach ($categories as $cat) {
                            if ($cat['group_id'] == TransCatGroup::BOAT_ID) {
                                $url = Url::to(['client/edit-proposal', 'trans_id' => false, 'cat_id' => $cat_ids[$cat['get_param']]]);
                                echo '<li class="add-prop__level2-li">'
                                    . '<a  class="add-prop__level2-a" href="' .$url. '"> ' .$cat['name']. ' </a>'
                                    . '</li>';
                            }
                        }
                        ?>
                    </ul>

                    <p class="add-prop__back"><?= Yii::t('client', 'COME_BACK') ?></p>
                </div>  <!--"add-prop__downwrap">-->
            </li>
        </nav>
    <?php endif; ?>

    <?php if ($proposals >= 3) : ?>
        <div class="add-pro__unlim">
            <p class="add-pro__ulp-p">
                <?= Yii::t('client', '0_PROP_COMMENT') ?>
                <a href="#" class="js-level-up" > <?= strtolower(Yii::t('client', 'CHANGE_STATUS')) ?> </a>
            </p>
        </div>
    <?php endif; ?>
</div>   <!--class="add-prop">-->
<?php endif; ?>