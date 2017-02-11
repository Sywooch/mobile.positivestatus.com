<?php
    // Parameters:
    // $model  - Trans::findOne() OR new Trans();
    use app\models\TransCat;
    use yii\helpers\Html;
?>


<?php // Category labels ?>
<?php /*
<div id="categorypanel_cnt" class="kategor_che" style="margin: 18px 0 34px 0;">
    <?= TransCat::getProposalFormCategoryPanel() ?>
</div>
*/ ?>


<?php
    // Hidden fields for dropdown selected values
    echo Html::activeHiddenInput($model, 'cat_id');

    echo '<div id="hiddenfields_cnt">';
        echo Html::activeHiddenInput($model, 'model_id');
        echo Html::activeHiddenInput($model, 'brand_id');
        echo Html::activeHiddenInput($model, 'category_id');
        echo Html::activeHiddenInput($model, 'transmiss_id');
        echo Html::activeHiddenInput($model, 'interior_id');
        echo Html::activeHiddenInput($model, 'fuel_id');
        echo Html::activeHiddenInput($model, 'climate_id');
        echo Html::activeHiddenInput($model, 'wheel_id');
        echo Html::activeHiddenInput($model, 'year');
        echo Html::activeHiddenInput($model, 'month');
        echo Html::activeHiddenInput($model, 'emission_id');
        echo Html::activeHiddenInput($model, 'sticker_id');
        echo Html::activeHiddenInput($model, 'cab_id');
        echo Html::activeHiddenInput($model, 'axle_id');
        echo Html::activeHiddenInput($model, 'bunk_id');
        echo Html::activeHiddenInput($model, 'hydraulic_id');
        echo Html::activeHiddenInput($model, 'length_id');
        echo Html::activeHiddenInput($model, 'licweight_id');
        echo Html::activeHiddenInput($model, 'load_id');
        echo Html::activeHiddenInput($model, 'seat_id');
        //echo Html::activeHiddenInput($model, 'length');
        //echo Html::activeHiddenInput($model, 'motohours');
    echo '</div>';


    // Dropdown panel
    echo $this->render('//layouts/_dropdown_filter_panel', ['ul_class' => 'nav2 navadd']);
?>



<?php if (!Yii::$app->request->isPjax) : ?>
<?php $this->beginJs(); ?>
<script>
    // Functions fillDropDowns() & fillModelDropDown()
    // $("body").on("click", "#dropdowns_cnt ul ul a")
    // are in /views/layouts/_dropdown_filter_panel.php
    var cat_id = $("#trans-cat_id").val();

    if(cat_id == "") {
        $("#dropdowns_cnt > ul > li").hide();
    }
    else {
        var model_id = $("#trans-model_id").val();
        var brand_id = (model_id == "" || model_id == "0") ? "" : $("#trans-brand_id").val();
        $("#category_" +cat_id).prop("checked", true);
        fillDropDowns(cat_id, brand_id, true, 1);       // fillDropDowns(cat_id, brand_id, setDropdownTitles, getYearMonth)
    }


//    $("#categorypanel_cnt [type=radio]").on("change", function() {
//        $("#hiddenfields_cnt [type=hidden]").val("0");
//        var cat_id = $(this).attr("data-category_id");
//        fillDropDowns(cat_id, "", false, 1);            // fillDropDowns(cat_id, brand_id, setDropdownTitles, getYearMonth)
//    });
</script>
<?php $this->endJs(); ?>
<?php endif; ?>