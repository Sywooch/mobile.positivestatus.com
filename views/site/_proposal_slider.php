<?php
/**
 * @var $transcat_model ActiveRecord TransCat::findOne(['get_param' => $trans_cat])
 * @var $filter_model ActiveRecord new Trans(['scenario' => 'filter']);
 */
    use app\components\Countries;
    use yii\helpers\Html;

    if ($transcat_model->min_year == $transcat_model->max_year) {
        $transcat_model->min_year = date('Y')-10; $transcat_model->max_year = date('Y');
    }
    if ($transcat_model->min_price == $transcat_model->max_price) {
        $transcat_model->min_price = 0; $transcat_model->max_price = 10000;
    }

    $year_ini_value = $transcat_model->min_year .';' .$transcat_model->max_year;
    $price_ini_value = $transcat_model->min_price .';' .$transcat_model->max_price;

    if (empty($filter_model->slider_years))
        $filter_model->slider_years = $year_ini_value;
    if (empty($filter_model->slider_prices))
        $filter_model->slider_prices = $price_ini_value;
?>


    <?= Html::beginForm('', 'post', ['id' => 'filter_form', 'class'=>'']) ?>

    <div class="sliders-block down">
        <div class="year-slider">
            <div class="year-slider__select" id="hide_select">
                <select id="trans_country_code" name="Trans[country_code]" value="<?= $filter_model->country_code ?>" data-ini_value="<?= Countries::getWholeEurope() ?>" style="width:230px">
                    <?= Countries::getAllCountryOptions() ?>
                </select>
            </div>

            <span class="slider-text"><?= Yii::t('site', 'YEAR') ?></span>
            <div class="layout-slider">
                <?= Html::activeTextInput($filter_model, 'slider_years', ['type' => 'slider', 'data-ini_value' => $year_ini_value]) ?>
            </div>
        </div>

        <div class="price-slider">
            <div class="text"><?= Yii::t('site', 'PRICE') ?></div>

            <div class="checkbox checkbox_price-slider">
                <?= Html::checkbox('Trans[nds_only]',$filter_model->nds_only, ['id' => 'trans-nds_only']) ?>

                <label for="trans-nds_only">
                    <span class="pseudo-checkbox white"></span>
                    <span class="label-text"><?= Yii::t('site', 'NDS_ONLY') ?></span>
                </label>
            </div>

            <div class="layout-slider">
                <?= Html::activeTextInput($filter_model, 'slider_prices', ['type' => 'slider', 'data-ini_value' => $price_ini_value]) ?>
            </div>
        </div>  <!--div class="price-slider"-->
    </div>  <!--div class="sliders-block down"-->



    <div class="bottom-filter down">
        <div class="filter-menu">
            <a class="toggleMenu" href="#"><?= Yii::t('site', 'MENU') ?></a>

            <div class="box">
                <input type="button" id="btn_reset_filters" class="reset_btn" value="<?= Yii::t('site', 'RESET') ?>">
                <input type="submit" id="sbt" name="sbm" class="btngeen" value="<?= Yii::t('site', 'FIND') ?>" >
            </div>

            <?php
                // hidden fields for _dropdown_filter_panel
                echo '<div id="filter_form_hidden_inputs_cnt" style="display: none;">';
                echo Html::activeHiddenInput($filter_model, 'category_id');
                echo Html::activeHiddenInput($filter_model, 'brand_id');
                echo Html::activeHiddenInput($filter_model, 'model_id');
                echo Html::activeHiddenInput($filter_model, 'transmiss_id');
                echo Html::activeHiddenInput($filter_model, 'fuel_id');
                echo Html::activeHiddenInput($filter_model, 'interior_id');
                echo Html::activeHiddenInput($filter_model, 'wheel_id');
                echo Html::activeHiddenInput($filter_model, 'climate_id');
                echo Html::activeHiddenInput($filter_model, 'emission_id');
                echo Html::activeHiddenInput($filter_model, 'sticker_id');
                echo Html::activeHiddenInput($filter_model, 'cab_id');
                echo Html::activeHiddenInput($filter_model, 'axle_id');
                echo Html::activeHiddenInput($filter_model, 'bunk_id');
                echo Html::activeHiddenInput($filter_model, 'hydraulic_id');
                echo Html::activeHiddenInput($filter_model, 'length_id');
                echo Html::activeHiddenInput($filter_model, 'licweight_id');
                echo Html::activeHiddenInput($filter_model, 'load_id');
                echo Html::activeHiddenInput($filter_model, 'seat_id');
                echo Html::activeHiddenInput($filter_model, 'length');
                echo Html::activeHiddenInput($filter_model, 'motohours');
                echo '</div>';

                echo $this->render('//layouts/_dropdown_filter_panel', ['dropdowns_cnt_style' => 'display:none', 'ul_class' => 'nav2', 'transcat_model' => $transcat_model]);
            ?>
        </div>
    </div>
    <div class="clear"></div>

    <?= Html::endForm() ?>



<?php if (!Yii::$app->request->isPjax) : ?>
<?php $this->beginJs(); ?>
<script>
    $("#trans-slider_years").slider({from: <?= $transcat_model->min_year ?>, to: <?= $transcat_model->max_year ?>, limits: false, step: 1});
    $("#trans-slider_prices").slider({from: <?= $transcat_model->min_price ?>, to: <?= $transcat_model->max_price ?>, step: 1000, dimension: '&nbsp;€'});
    //var o = $("#trans_country_code").msDropdown({visibleRows: 13}).data("dd").setIndexByValue("<?= $filter_model->country_code ?>");

    // Functions fillDropDowns() & fillModelDropDown()
    // $("body").on("click", "#dropdowns_cnt ul ul a")
    // are in /views/layouts/_dropdown_filter_panel.php
    var cat_id = <?= $transcat_model->id ?>;
    var model_id = $("#trans-model_id").val();
    var brand_id = (model_id == "" || model_id == "0") ? "" : $("#trans-brand_id").val();
    fillDropDowns(cat_id, brand_id, true);
    $("#dropdowns_cnt").css("visibility", "visible");


    $("#btn_reset_filters").on("click", function() {
        // Country
        $('#trans_country_code').msDropDown().data("dd").set("value", "");

        // Years, Prices, NDS
        var years = $("#trans-slider_years").attr("data-ini_value").split(";");
        var prices = $("#trans-slider_prices").attr("data-ini_value").split(";");
        $("#trans-slider_years").slider("value", years[0], years[1]);
        $("#trans-slider_prices").slider("value", prices[0], prices[1]);
        $("#trans-nds_only").prop("checked", false);

        // Dropdown filters
        $("#dropdowns_cnt li.items_cnt").each(function(index, el) {
            $(el).children("a").text($(el).attr("data-ini_text"));
        });
        $("#filter_form_hidden_inputs_cnt").children("input:hidden").val("");
        $("#filter_form").submit();
    });
</script>
<?php $this->endJs(); ?>
<?php endif; ?>