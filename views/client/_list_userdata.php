<?php
    // Parameters :
    // $user - User Model
    // $trans - Trans Model OR null
    use app\components\Countries;
    use app\components\Y;
    use yii\helpers\Html;

    $profile_data = unserialize($user->profile->details);

    $isDetailPage = Yii::$app->controller->route == 'site/details';
    $show_select_contact = (Yii::$app->controller->route == 'client/edit-proposal');
    if ($show_select_contact)
        $checkbox_style = (count($user->contacts) == 1) ? 'visibility: hidden' : '';
?>

    <h4> <?= Html::encode($user->name) ?></h4>

    <?php
        if (!$isDetailPage)
            echo Html::mailto(Html::encode($user->email), null, ['class' => 'mails'])
    ?>

    <address>
        <em class="icn"></em>
        <?= Html::encode($user->profile->address) ?> <br />
        <?= Countries::getCountryByCode(Html::encode($user->profile->country)) ?> <br />
    </address>


    <?php foreach ($user->contacts as $contact) : ?>

        <?php
            if (Yii::$app->controller->route == 'site/details' && !empty($trans->contacts) && !in_array($contact->id, $trans->contacts))
                continue;
        ?>

        <?php $contact_data = unserialize($contact->details); ?>

        <div class="manager">
            <?php $avatarUrl = Y::getAvatarUrl() .Y::getAvatarFile($contact->id); ?>
            <a href="#" onclick="return false;"> <?= Html::img($avatarUrl, ['width' => 80, 'height' => 80]) ?> </a>

            <p><?= Html::encode($contact->name) ?></p>
            <span class="langu">
                <?php
                if (!empty($contact_data['langs'])) {
                    $langs = '';

                    foreach ($contact_data['langs'] as $lang) {
                        $langs .= Html::encode($lang) .', ';
                    }

                    echo trim($langs, ', ');
                }
                ?>
            </span>

            <?php if ($show_select_contact) : ?>
            <div class="checkbox" style="<?= $checkbox_style ?>">
                <?= Html::checkbox('Trans[contacts][]', empty($trans->contacts) || in_array($contact->id, $trans->contacts), ['id' => 'trans-contact-' .$contact->id, 'value' => $contact->id]) ?>

                <label for="trans-contact-<?= $contact->id ?>">
                    <span class="pseudo-checkbox white"></span>
                    <span><?= Yii::t('client', 'SELECT_CONTACT') ?> </span>
                </label>
            </div>
            <?php endif; ?>
        </div>

        <div class="phones" style="margin-bottom:12px;">
            <?php
                if (!empty($contact_data['phones'])) {
                    foreach ($contact_data['phones'] as $phone) {
                        $trim_phone = str_replace([' ', '  ', '   '], '', Html::encode($phone));
                        $link = Html::a($phone, 'tel:' . $trim_phone);
                        echo Html::tag('p', $link, ['class' => 'ph1']);
                    }
                }

                if (!empty($contact_data['cells'])) {
                    foreach ($contact_data['cells'] as $n => $phone) {
                        $trim_phone = str_replace([' ', '  ', '   '], '', Html::encode($phone));
                        $link = Html::a($phone, 'tel:' . $trim_phone);

                        if (!empty($contact_data['vibers'][$n]))
                            $link .= ' <i class="viber"></i>';
                        if (!empty($contact_data['whatsapps'][$n]))
                            $link .= ' <i class="whatap"></i>';

                        echo Html::tag('p', $link, ['class' => 'ph2']);
                    }
                }

                if (!empty($contact_data['skypes'])) {
                    foreach ($contact_data['skypes'] as $skype) {
                        $trim_skype = str_replace([' ', '  ', '   '], '', Html::encode($skype));
                        echo Html::tag('p', $trim_skype, ['class' => 'skype']);
                    }
                }

                if (!empty($contact_data['berries'])) {
                    foreach ($contact_data['berries'] as $berry) {
                        $trim_berry = str_replace([' ', '  ', '   '], '', Html::encode($berry));
                        echo Html::tag('p', $trim_berry, ['class' => 'bery']);
                    }
                }

                if (!empty($contact_data['facebooks'])) {
                    foreach ($contact_data['facebooks'] as $fb) {
                        $trim_fb = str_replace([' ', '  ', '   '], '', Html::encode($fb));
                        $link = Html::a($trim_fb, $trim_fb);
                        echo Html::tag('p', $link, ['class' => 'fb', 'target' => '_blank']);
                    }
                }

                if (!empty($contact_data['twitters'])) {
                    foreach ($contact_data['twitters'] as $tw) {
                        $trim_tw = str_replace([' ', '  ', '   '], '', Html::encode($tw));
                        $link = Html::a($trim_tw, $trim_tw);
                        echo Html::tag('p', $link, ['class' => 'tw', 'target' => '_blank']);
                    }
                }

                if (!empty($contact_data['sites'])) {
                    foreach ($contact_data['sites'] as $site) {
                        $trim_site = str_replace([' ', '  ', '   '], '', Html::encode($site));
                        $link = Html::a($trim_site, $trim_site);
                        echo Html::tag('p', $link, ['class' => 'web', 'target' => '_blank']);
                    }
                }
            ?>
        </div>

        <div class="w_data" style="margin-bottom:12px;">
        <?php
            // Working days and times
            $w_times = $profile_data['w_hour1'] .' - ' .$profile_data['w_hour2'];
            echo '<p>' .Yii::t('user', 'SCHEDULE') .' &nbsp;' .$w_times .'</p>';

            $w_days = '';
            if (in_array('1',$profile_data['w_days']))      $w_days .= Yii::t('user', 'SHORT_MON') .'. ';
            if (in_array('2',$profile_data['w_days']))      $w_days .= Yii::t('user', 'SHORT_TUE') .'. ';
            if (in_array('3',$profile_data['w_days']))      $w_days .= Yii::t('user', 'SHORT_WED') .'. ';
            if (in_array('4',$profile_data['w_days']))      $w_days .= Yii::t('user', 'SHORT_THU') .'. ';
            if (in_array('5',$profile_data['w_days']))      $w_days .= Yii::t('user', 'SHORT_FRI') .'. ';
            if (in_array('6',$profile_data['w_days']))      $w_days .= Yii::t('user', 'SHORT_SAT') .'. ';
            if (in_array('7',$profile_data['w_days']))      $w_days .= Yii::t('user', 'SHORT_SUN') .'. ';

            if ($w_days != "")
                echo '<p>' .$w_days .'</p>';
        ?>
        </div>

        <?php
            // Contact form
            if ($isDetailPage) {
                echo '<div class="phones">';
                echo $this->render('_list_contactform', ['contact' => $contact]);
                echo '</div>';
            }
        ?>

       
    <?php endforeach; ?>
