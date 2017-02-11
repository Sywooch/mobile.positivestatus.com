<?php
    // Parameters:
    // $model           - Trans::findOne() OR new Trans();
    // $other_proposals - an array
    // $cat_id
    use app\models\Trans;
    use yii\helpers\Html;
    use yii\helpers\Url;
?>


<div class="content content_add-proposal" style="overflow: visible">
    <h2 class="img_h2 add"> <?= Yii::t('client', 'ADD_PROPOSAL') ?> </h2>

    <div class="addmobi">
        <a href="#" class="mails" id="mobile_uploader_dummy_button" style="width: 100%">
            <img src="/images/fotogal.png" alt="<?= Yii::t('client', 'FROMGALLERY') ?>" height="40" width="38"><br>
            <?= Yii::t('client', 'FROMGALLERY') ?>
        </a>

        <?php /*
        <a href="#" class="mails">
            <img src="/images/fot.png" alt="<?= Yii::t('client', 'ADD_PHOTO') ?>" height="34" width="40"><br>
            <?= Yii::t('client', 'TAKE_PHOTO') ?>
        </a>
        */ ?>
    </div>

    <div class="add_fots">
        <?php
            echo Html::beginForm();
            if ($model->hasErrors())
                echo Html::tag('div', Html::errorSummary($model, ['header' => '']), ['class' => 'alert alert-danger']);
        ?>

        <?= $this->render('_edit_proposal_uploader', ['model' => $model]) ?>

        <?= $this->render('_edit_proposal_form', ['model' => $model]) ?>

        <div class="right-menu contaks car">
            <?= $this->render('_list_userdata', ['user' => $model->user, 'trans' => $model]) ?>
        </div>  <!--right-menu car -->

        <?= Html::endForm() ?>
    </div>  <!--class="add_fots">-->
</div>  <!--class="content">-->


<?php // Other proposals ?>
<div class="dobav_add">
    <p><?= Yii::t('client', 'ADDED_PROPOSALS') ?></p>

    <div class="owl-carousel">
    <?php foreach ($other_proposals as $proposal) : ?>
        <?php $main_photo = Html::img(Trans::getMainPhotoUrl($proposal['id'], 'sm_')); ?>
        <?php $nds = ($proposal['nds'] == 0) ? Yii::t('client', 'WITHOUT_NDS') : Yii::t('client', 'WITHOUT_NDS') .' ' .$proposal['nds'] .'%'; ?>

        <div>
            <?= Html::a($main_photo, Url::to(['/client/edit-proposal', 'trans_id' => $proposal['id']])) ?>

            <div class="car">
                <div class="text">
                    <div class="harak">
                        <p class="title"><a href="#"><?= $proposal['brand']['name'] .' ' .$proposal['model']['name'] ?></a></p>
                        <a class="add" href=""></a>
                        <span class="le"><?= $proposal['year'] ?></span>
                        <span class="ri"><?= number_format($proposal['mileage'], 0, '.', ' ') ?> km</span>
                        <p class="prcc">
                            <b><?= number_format($proposal['price_brut'], 0, '.', ' ') ?></b>  €
                            <span class="nds ri"><?= $nds ?></span>
                        </p>
                        <!--<p class="minip"><b>16 500</b>  € netto  </p>  -->
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    </div>    <!--class="owl-carousel"> -->

</div>  <!--class="dobav_add">  -->

<div class="clear"></div>



<?php if (!Yii::$app->request->isPjax) : ?>
<?php $this->beginJs(); ?>
<script>
    $('.owl-carousel').owlCarousel({
        loop:true,
        margin:10,
        nav:true,
        navText: ['<span class="l" title="dd" >Раньше</span>', '<span class="r" title="df" >Позже</span>'],
        responsive:{
            0:{
                items:1
            },
            600:{
                items:3
            },
            1000:{
                items:4
            }
        }
    })
</script>
<?php $this->endJs(); ?>
<?php endif; ?>

<?php
    if (!Yii::$app->request->isPjax)
        $this->registerJsFile('/js/owl.carousel.min.js', ['depends' => 'yii\web\JqueryAsset']);
