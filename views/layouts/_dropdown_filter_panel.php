<?php
/**
 * Called from
 * views/client/_edit_proposal_form_panel
 * views/site/_proposal_slider
 *
 * Parameters :
 * $dropdowns_cnt_style
 * $ul_class
 * @var $transcat_model ActiveRecord TransCat::findOne(['get_param' => $trans_cat])
 *
 * @var $this \yii\web\View
 */
use app\models\TransCat;
use yii\bootstrap\Nav;
use yii\helpers\Html;
use yii\helpers\Url;

    if (!isset($dropdowns_cnt_style))
        $dropdowns_cnt_style = '';
    if (!isset($ul_class))
        $ul_class = '';
?>


<?php
    if (isset($transcat_model) && $transcat_model->group_id > 0) {
        $items = [];
        $lang = Yii::$app->language;
        $submenu = TransCat::find()->where(['group_id' => $transcat_model->group_id])->asArray()->all();

        foreach ($submenu as $item) {
            $url = Url::to(['/site/proposal', 'trans_cat' => $item['get_param']]);
            $items[] = [
                'label' => $item[$lang],
                'url' => $url,
                'options' => ['class' => (Url::current() == $url) ? 'active' : '']
            ];
        }

        echo Nav::widget([
            'items' => $items,
            'options' => ['class' =>'nav nav-tabs nav-tabs_my'],
        ]);
    }
?>


<div id="dropdowns_cnt" style="<?= $dropdowns_cnt_style ?>">
    <ul class="<?= $ul_class ?>">
        <li class="items_cnt" data-trans_field="category_id" data-ini_text="<?= Yii::t('client', 'CATEGORY_ID') ?>">
            <?= Html::a(Yii::t('client', 'CATEGORY_ID'), '#') ?>
            <ul class="kategoti-first"></ul>
        </li>

        <li class="static items_cnt" data-trans_field="brand_id" data-ini_text="<?= Yii::t('site', 'BRANDS') ?>">
            <?= Html::a(Yii::t('site', 'BRANDS'), '#') ?>
            <ul class="f16"></ul>
            <?php /*<div class="forma where ">
                <?= Yii::t('admin', 'LABEL_BRANDID') ?>: <input name="marrk" id="sd">
            </div> */ ?>
        </li>

        <li class="static model items_cnt" data-trans_field="model_id" data-ini_text="<?= Yii::t('site', 'MODELS') ?>">
            <?= Html::a(Yii::t('site', 'MODELS'), '#') ?>
            <ul id="model_dropdown" class="f16"></ul>
        </li>

        <li class="items_cnt" data-trans_field="transmiss_id" data-ini_text="<?= Yii::t('client', 'TRANSMISS_ID') ?>">
            <?= Html::a(Yii::t('client', 'TRANSMISS_ID'), '#') ?>
            <ul></ul>
        </li>

        <li class="items_cnt" data-trans_field="fuel_id" data-ini_text="<?= Yii::t('client', 'FUEL_ID') ?>">
            <?= Html::a(Yii::t('client', 'FUEL_ID'), '#') ?>
            <ul></ul>
        </li>

        <li class="items_cnt" data-trans_field="interior_id" data-ini_text="<?= Yii::t('client', 'INTERIOR_ID') ?>">
            <?= Html::a(Yii::t('client', 'INTERIOR_ID'), '#') ?>
            <ul></ul>
        </li>

        <li class="items_cnt" data-trans_field="climate_id" data-ini_text="<?= Yii::t('client', 'CLIMATE_ID') ?>">
            <?= Html::a(Yii::t('client', 'CLIMATE_ID'), '#') ?>
            <ul></ul>
        </li>

        <li class="items_cnt" data-trans_field="wheel_id" data-ini_text="<?= Yii::t('client', 'WHEEL_ID') ?>">
            <?= Html::a(Yii::t('client', 'WHEEL_ID'), '#') ?>
            <ul></ul>
        </li>

        <li class="items_cnt" data-trans_field="emission_id" data-ini_text="<?= Yii::t('client', 'EMISSION_ID') ?>">
            <?= Html::a(Yii::t('client', 'EMISSION_ID'), '#') ?>
            <ul></ul>
        </li>

        <li class="items_cnt" data-trans_field="sticker_id" data-ini_text="<?= Yii::t('client', 'STICKER_ID') ?>">
            <?= Html::a(Yii::t('client', 'STICKER_ID'), '#') ?>
            <ul></ul>
        </li>

        <li class="items_cnt" data-trans_field="cab_id" data-ini_text="<?= Yii::t('client', 'CAB_ID') ?>">
            <?= Html::a(Yii::t('client', 'CAB_ID'), '#') ?>
            <ul></ul>
        </li>

        <li class="items_cnt" data-trans_field="axle_id" data-ini_text="<?= Yii::t('client', 'AXLE_ID') ?>">
            <?= Html::a(Yii::t('client', 'AXLE_ID'), '#') ?>
            <ul></ul>
        </li>

        <li class="items_cnt" data-trans_field="bunk_id" data-ini_text="<?= Yii::t('client', 'BUNK_ID') ?>">
            <?= Html::a(Yii::t('client', 'BUNK_ID'), '#') ?>
            <ul></ul>
        </li>

        <li class="items_cnt" data-trans_field="hydraulic_id" data-ini_text="<?= Yii::t('client', 'HYDRAULIC_ID') ?>">
            <?= Html::a(Yii::t('client', 'HYDRAULIC_ID'), '#') ?>
            <ul></ul>
        </li>

        <li class="items_cnt" data-trans_field="length_id" data-ini_text="<?= Yii::t('client', 'LENGTH_ID') ?>">
            <?= Html::a(Yii::t('client', 'LENGTH_ID'), '#') ?>
            <ul></ul>
        </li>

        <li class="items_cnt" data-trans_field="licweight_id" data-ini_text="<?= Yii::t('client', 'LICWEIGHT_ID') ?>">
            <?= Html::a(Yii::t('client', 'LICWEIGHT_ID'), '#') ?>
            <ul></ul>
        </li>

        <li class="items_cnt" data-trans_field="load_id" data-ini_text="<?= Yii::t('client', 'LOAD_ID') ?>">
            <?= Html::a(Yii::t('client', 'LOAD_ID'), '#') ?>
            <ul></ul>
        </li>

        <li class="items_cnt" data-trans_field="seat_id" data-ini_text="<?= Yii::t('client', 'SEAT_ID') ?>">
            <?= Html::a(Yii::t('client', 'SEAT_ID'), '#') ?>
            <ul></ul>
        </li>

        <li class="items_cnt" data-trans_field="length" data-ini_text="<?= Yii::t('client', 'LENGTH') ?>">
            <?= Html::a(Yii::t('client', 'LENGTH'), '#') ?>
            <ul></ul>
        </li>

        <?php if (Yii::$app->controller->route == 'client/edit-proposal') : ?>
            <li class="items_cnt" data-trans_field="year" data-ini_text="<?= Yii::t('client', 'YEAR') ?>">
                <?= Html::a(Yii::t('client', 'YEAR'), '#') ?>
                <ul></ul>
            </li>

            <li class="items_cnt" data-trans_field="month" data-ini_text="<?= Yii::t('site', 'MONTH') ?>">
                <?= Html::a(Yii::t('site', 'MONTH'), '#') ?>
                <ul></ul>
            </li>
        <?php endif; ?>
    </ul>
</div>



<?php if (!Yii::$app->request->isPjax) : ?>
<?php $this->beginJs(); ?>
<script>
    $("body").on("click", "#dropdowns_cnt ul ul a", function(e) {
        e.preventDefault();

        if ($(this).hasClass("parent")) {
            $(this).closest("ul").children("li").removeClass("hover");
            $(this).parent().addClass("hover");
        }
        else {
            var trans_field = $(this).closest("li.items_cnt").attr("data-trans_field");
            var input = "#trans-" +trans_field;
            $(input).val($(this).attr("data-id"));
            $(this).closest("li.items_cnt").children("a.rod").html($(this).text());
            $("#dropdowns_cnt li").removeClass('hover');

            if (trans_field == "brand_id") {
                var title = $("#model_dropdown").parent("li").attr("data-ini_text");
                $("#model_dropdown").siblings("a").text(title);
                $("#trans-model_id").val(0);
                fillModelDropDown($(this).attr("data-id"));
            }
        }
    });


    function fillDropDowns(cat_id, brand_id, setDropdownTitles, getYearMonth) {
        var add_new_model = <?php echo Yii::$app->controller->route == 'client/edit-proposal' ? 'true' : 'false'; ?>;

        if (brand_id == undefined)
            brand_id = "";
        if (setDropdownTitles == undefined)
            setDropdownTitles = false;
        if (getYearMonth == undefined)
            getYearMonth = 0;

        $.ajax({
            url: "<?= Url::to(['/auxx/get-proposal-form-dropdowns']) ?>",
            type: "post",
            async: false,
            timeout: 3000,
            dataType: "json",
            data: {cat_id: cat_id, brand_id: brand_id, get_year_month: getYearMonth, add_new_model: add_new_model},
            error: function(a,b,c) {alert(b)},
            success: function(data) {
                if (data.result == "ok") {
                    $("#trans-cat_id").val(cat_id);
                    fillDropDowns_step2(data, setDropdownTitles);        // function is declared below
                }
                else {
                    alert(data.message);
                }
            }
        });
    }

    function fillDropDowns_step2(data, setDropdownTitles) {
        if (setDropdownTitles == undefined)
            setDropdownTitles = false;

        $("#dropdowns_cnt > ul > li").each(function(index, item) {
            var item = $(item);
            item.children("a").text(item.attr("data-ini_text"));

            var key = item.attr("data-trans_field");
            if (key in data)
                item.show().children("ul").html(data[key]);
            else
                item.hide().children("ul").html("");
        });

        // Dropdown Titles
        if (setDropdownTitles) {
            $("#dropdowns_cnt li.items_cnt").each(function(index, item) {
                var item = $(item);
                var model_val = $("#trans-" +item.attr("data-trans_field")).val();     // trans-fuel_id, trans-model_id ... etc

                if (model_val != "0" && model_val != "") {
                    var link = item.find("[data-id=" +model_val +"]");
                    if (link.length == 1)
                        item.children("a.parent").text(link.text());
                }
            });
        }

        $("#dropdowns_cnt").show();
    }


    function fillModelDropDown(brand_id) {
        var add_new_model = <?php echo Yii::$app->controller->route == 'client/edit-proposal' ? 'true' : 'false'; ?>;

        $.ajax({
            url: "<?= Url::to(['/auxx/get-model-dropdown-html']) ?>",
            type: "post",
            dataType: "json",
            data: {brand_id: brand_id, add_new_model: add_new_model},
            error: function(a,b,c) {alert(b);},
            success: function(data) {
                if (data.result != "ok")
                    alert(data.message);
                else if (data.html != "")
                    $("#model_dropdown").html(data.html).append().parent("li").show();
            }
        });
    }
</script>
<?php $this->endJs(); ?>
<?php endif; ?>


<?php
    $url = Url::to(['/auxx/add-new-model']);

    $js = <<<JS
        function addNewModel(e, input) {
            var key = e.keyCode || e.which;
            if (key != 13)
                return false;

            var brand_id = $("#trans-brand_id").val();
            var name = $(input).val();

            $.ajax({
                url: "$url",
                type: "post",
                dataType: "html",
                data: {brand_id: brand_id, name: name},
                error: function(a,b,c) {alert(b);},
                success: function(new_code) {
                    if (isNaN(parseInt(new_code))) {
                        alert(new_code);
                    }
                    else {
                        $(this).val("");
                        var new_item = $("<li>").append($("<a>").attr("data-id", new_code).text(name));
                        $(input).parent().before(new_item);
                        $("#model_dropdown").children("li:last").children("a").click();
                    }
                }
            });
        }
JS;

$this->registerJs($js, \yii\web\View::POS_HEAD);