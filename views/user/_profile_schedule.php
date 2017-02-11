<?php
    /**
     * @var $profile UserProfile
     */
    use app\models\UserProfile;
    use yii\helpers\Html;
?>

<!-- W_hours -->
<div class="times plus">
    <label> <?= Yii::t('user', 'SCHEDULE') ?> </label>
    <span>
        <?= Html::activeTextInput($profile, 'w_hour1', ['id' => 'datetimepicker1', 'class' => 'add drop']) ?>
    </span>

    <i class="tire">-</i>

    <span>
        <?= Html::activeTextInput($profile, 'w_hour2', ['id' => 'datetimepicker2', 'class' => 'add drop']) ?>
    </span>
</div>


<!-- W_days -->
<div class="week">
    <?php foreach (UserProfile::getDays() as $val => $i18_mess) : ?>

        <?php $class = ($val == '7') ? 'itms last' : 'itms'; ?>
        <div class="<?= $class ?>">
            <p><?= Yii::t('user', $i18_mess) ?></p>
            <div class="checkbox">
                <?php $cid = 'wdays_' .$val; ?>
                <?php $checked = in_array($val, $profile->w_days); ?>
                <?= Html::activeCheckbox($profile, 'w_days[]', ['id' => $cid, 'value' => $val, 'checked ' => $checked, 'label' => null, 'uncheck' => null]) ?>

                <label for="<?= $cid ?>">
                    <span class="pseudo-checkbox white"></span>
                </label>
            </div>
        </div>

    <?php endforeach; ?>
</div> <!--class="week" -->


<?php $this->beginJs(); ?>
    <script>
        $.datetimepicker.setLocale('en');
        $('#datetimepicker1').datetimepicker({
            datepicker:false,
            format:'H:i',
            value:'<?= Html::encode($profile->w_hour1) ?>'
        });
        $('#datetimepicker2').datetimepicker({
            datepicker:false,
            format:'H:i',
            value:'<?= Html::encode($profile->w_hour2) ?>'
        });
    </script>
<?php $this->endJs(); ?>


<?php
    $this->registerJsFile('/js/jquery.datetimepicker.full.min.js', ['depends' => 'yii\web\JqueryAsset']);
