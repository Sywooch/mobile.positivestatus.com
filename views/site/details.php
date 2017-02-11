<?php
    // Parameters:
    // $model - Trans->findOne()
    // $back_url - Yii::$app->request->referrer OR NULL
    use app\models\Trans;
    use app\models\TransCat;
    use yii\helpers\Html;
    use yii\helpers\Url;

    $back_url = Url::previous('proposal');
    $cat_name = TransCat::getNameById($model->cat_id);
    $showBookmarkLink = !Yii::$app->user->isGuest && (Yii::$app->user->id != $model->user_id) && empty($model->bookmarkExists);
?>


<div class="content content_640">
    <br /><br /><br />

    <div class="fotorama" data-width="621" data-nav="thumbs" data-thumbheight="102">
    <?php
        $sm_photos = Trans::getPhotoUrls($model->id, 'sm_');
        $lg_photos = Trans::getPhotoUrls($model->id, 'lg_');
        foreach ($sm_photos as $n => $photo) {
            echo Html::a($photo, $lg_photos[$n], ['width' => '140', 'height' => '102']);
        }
    ?>
    </div>

    <div class="car_inf">
        <div class="ov">
            <div class="ll">
                <h3><?= $model->fullName ?></h3>
                <div class="dinf">
                    <span class="data"><?= $model->date ?></span>
                    <span class="smotr"><?= $model->click ?></span>
                </div>

                <div class="prc">
                    <big><?= number_format($model->price_brut, 0, ',', ' ') ?> €</big> brutto
                </div>
                <div class="prc">
                    <b><?= number_format($model->price_net, 0, ',', ' ') ?> €</b> netto
                </div>

            </div>
            <div class="rr">
            <?php
                // Labels and Descriptions (not empty values only)
                $arr = Trans::getDescriptions($model);
                $labels = array_keys($arr);
                $descs = array_values($arr);
                $lines = count($labels);

                // <div class="info-line"><i>Год выпуска:</i><span>2007</span></div>
                for ($n = 0; $n < $lines; $n++)
                    echo '<div class="info-line"><span>' .$descs[$n] .'</span></div>';
            ?>
            </div>
        </div>  <!--class="ov"> -->
        <div class="cartext">
            <p><?= $model->text_de ?></p>
        </div>

        <div class=" botsoc">
            <?php if ($showBookmarkLink) : ?>
            <a href="#" id="addbookmark_link" class="inzakl"><?= Yii::t('site', 'ADD_TOBOOKMARK') ?></a>
            <?php endif; ?>

            <div class="share">
                <span class="tx"><?= Yii::t('site', 'SHARE') ?></span>
                <a class="f"  href="#"></a>
                <a class="tw"  href="#"></a>
                <a class="g"  href="#"></a>
                <a class="mail"  href="#"></a>
            </div>
        </div>

        <?php if (!is_null($back_url)) : ?>
            <div class="backto"> <?=Html::a(Yii::t('site', 'BACK_TO_LIST'), $back_url) ?> </div>
        <?php endif; ?>
    </div>  <!--class="car_inf"> -->

</div>  <!--class="content">-->



<div class="right-menu car">
    <?= $this->render('//client/_list_userdata', ['user' => $model->user, 'trans' => $model]) ?>
</div>


<?php if ($showBookmarkLink && !Yii::$app->request->isPjax) : ?>
<?php $this->beginJs(); ?>
<script>
    $("#addbookmark_link").on("click", function(e) {
        e.preventDefault();
        if (!confirm("<?= Yii::t('site', 'ADD_TOBOOKMARK') ?> ?"))
            return false;

        var params = {
            link: $("#addbookmark_link"),
            url: "<?= Url::to(['/auxx/update-model']) ?>",
            user_id: "<?= Yii::$app->user->id ?>",
            trans_id: "<?= $model->id ?>",
            message: "<?= Yii::t('site', 'BOOKMARK_CREATED') ?>"
        }

        addBookmark(params);        // Function is declared in main.js
    });
</script>
<?php $this->endJs(); ?>
<?php endif; ?>
