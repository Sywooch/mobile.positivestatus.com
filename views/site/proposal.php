<?php
    /**
     * @var $trans_dp ActiveDataProvider Trans
     * @var $transcat_model ActiveRecord TransCat::findOne(['get_param' => $trans_cat])
     * @var $filter_model ActiveRecord new Trans(['scenario' => 'filter']);
     */
    use app\components\AdvertWidget;
    use app\components\Y;
    use app\models\Trans;
    use app\models\TransCat;
    use yii\helpers\Url;
    use yii\widgets\ListView;
    use yii\widgets\Pjax;

    $trans_total = Trans::find()->where('cat_id=' .$transcat_model->id .' AND pause<' .time())->count();
    $dp_total = $trans_dp->totalCount;
    $cat_name = TransCat::getNameById($transcat_model->id, true);
?>


<?php // Sliders & Dropdown Filters ?>
<div class="filter" id="filt">
    <div class="container">

        <!--<h2 class="ico1"><?= $cat_name ?></h2> -->

        <div class="show_filtY" id="show_filtos">
            <svg class="show_filtY__pic" width="18" height="19">
                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#filters" />
            </svg>Фильтр
        </div>


        <?= $this->render('_proposal_slider', ['transcat_model' => $transcat_model, 'filter_model' => $filter_model]) ?>
    <div class="tags" id="taags">
        <div class="total">
            <?= Yii::t('site', 'TOTAL') .' <span> ' .$trans_total .' </span> &nbsp; ' ?>
            <?= Yii::t('site', 'FILTER') .' <span> ' .$dp_total .' </span> ' ?>
        </div>
        <ul>
            <li>#Toyota </li>
            <li>#Auris</li>
            <li># 2003</li>
            <li># 2004</li>
            <li># 2005</li>
        </ul>
        <div id="map_link_cnt1" class="map-btn main"><a href="#"><svg class="login-pic" width="23" height="25">
                                                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#placeholder" />
                                                </svg><span><?= Yii::t('site', 'ON_MAP') ?></span></a></div>
         <div id="map_link_cnt2" class="map-btn mobile"><a href="#"><svg class="login-pic" width="23" height="25">
                                                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#placeholder" />
                                                </svg><span><?= Yii::t('site', 'ON_MAP') ?></span></a></div>
    </div>
    </div>
</div>


<div class="container">


    <div class="content">
        <div class="catalog">
            <?php
            Pjax::begin([
                'id' => 'listview_pjax',
            ]);

            echo ListView::widget([
                'dataProvider' => $trans_dp,
                'itemOptions' => ['tag' => false],
                'itemView' => '//client/_list_listview',
                'viewParams' => ['is_owner' => false],
                'layout' => "{items}\n{pager}",
                'pager' => Y::getPagerSettings(),
            ]);

            Pjax::end();
            ?>

            <br />
        </div>  <!--div class="catalog"-->
    </div>  <!--div class="content"-->

<!--    <aside class="itm_cars side_rec">-->
        <?php
//        AdvertWidget::widget(
//                [
//                        'filter_model' => $filter_model
//                ]);
        ?>
<!--    </aside>-->
</div>  <!--div class="container"-->



<?php if (!Yii::$app->request->isPjax) : ?>
<?php $this->beginJs(); ?>
<script>
    // Map. #filter_form is in _proposal_slider.php
    $("#map_link_cnt1 a, #map_link_cnt2 a").on("click", function(e) {
        e.preventDefault();
        $("#filter_form").attr("action", "<?= Url::current(['map' => true]) ?>").submit();
    });
</script>
<?php $this->endJs(); ?>
<?php endif; ?>
