<?php
    // Parameters
    // $cat_id      - active TransCat id (for the dropDown)
    // $brand_id    - active TransBrand id (for the Grid)
    // $brand_name  - active TransBrand id (for the text above Grid #2)
    // $cat_dd      - Source for TransCat dropDown (an array)
    // $brand_dp    - TransBrand DataProvider (source for the Grid #1)
    // $model_dp    - TransModel DataProvider (source for the Grid #2)
    use yii\widgets\Pjax;
    use yii\grid\GridView;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\grid\SerialColumn;
    use yii\bootstrap\Modal;
    use yii\widgets\ActiveForm;
    
    
    $header = Html::tag('h4', Yii::t('admin', 'BRAND_MODELS'));
//    $upload_btn = Html::button(Yii::t('user', 'UPLOAD_MOBILEDE'), ['id' => 'btn_upload_mobilede', 'class' => 'btn btn-primary']);
//    echo Html::tag('div', $header .$upload_btn, ['class' => 'header_cnt']);
    echo $header;
?>
    
<div class="row">
    <div class="col-xs-4 col-xs-push-2">
    <?php
        // TransCat dropDown and TransBrand Grid (#1)
        echo Html::dropDownList('trans_cat', $cat_id, $cat_dd, ['id' => 'transcat_dd', 'class' => 'form-control']);
        
        echo '<div class="btn_add_cnt">';
        echo Html::button(
            '<span class="glyphicon glyphicon-plus"></span> ' .Yii::t('admin', 'ADD_BRAND'), 
            ['id' => 'btn_add_brand', 'class' => 'btn btn-default', 'model_name' => 'TransBrand']
        );
        echo '</div>';
        
        Pjax::begin(['id' => 'trans_brand_grid_cnt']);
        
        echo GridView::widget([
            'id' => 'trans_brand_grid',
            'dataProvider' => $brand_dp,
            'layout' => '{items} {pager}',
            'columns' => [
                [
                    'class' => SerialColumn::className(),
                    'options' => ['style' => 'width:50px;'],
                ],
                'name',
                [   // Buttons Edit, Delete, Show Models
                    'content' => function ($model, $key, $index, $column) {
                        $a_text = '<span class="glyphicon glyphicon-pencil"></span>';
                        $ret = Html::a($a_text, '#', ['class' => 'brand_edit_link', 'title' => Yii::t('site', 'EDIT'), 'model_name' => 'TransBrand']);
                        $a_text = '<span class="glyphicon glyphicon-remove"></span>';
                        $ret .= Html::a($a_text, '#', ['class' => 'brand_delete_link', 'title' => Yii::t('site', 'DELETE'), 'name_value' => $model->name, 'model_name' => 'TransBrand']);                       
                        $a_text = '<span class="glyphicon glyphicon-arrow-right"></span>';
                        $ret .= Html::a($a_text, Url::current(['brand_id' => $model->id]), ['class' => 'brand_showmodels_link', 'title' => Yii::t('site', 'SHOW_MODELS'), 'data-pjax' => 0]);
                        return $ret;
                    },
                    'options' => ['style' => 'width:112px;'],               
                ],
            ],
        ]);
        
        Pjax::end();
    ?>
    </div>
    
    
    <div class="col-xs-4 col-xs-offset-2">
    <?php
        // TransBrand name and TransModel Grid (#2)
        echo Html::textInput('brand_name', $brand_name, ['class' => 'form-control', 'disabled' => 'disabled']);

        echo '<div class="btn_add_cnt">';
        echo Html::button(
            '<span class="glyphicon glyphicon-plus"></span> ' .Yii::t('admin', 'ADD_MODEL'), 
            ['id' => 'btn_add_model', 'class' => 'btn btn-default', 'model_name' => 'TransModel']
        );
        echo '</div>';
        
        Pjax::begin(['id' => 'trans_model_grid_cnt']);
        
        echo GridView::widget([
            'id' => 'trans_model_grid',
            'dataProvider' => $model_dp,
            'layout' => '{items} {pager}',
            'columns' => [
                [
                    'class' => SerialColumn::className(),
                    'options' => ['style' => 'width:50px;'],
                ],
                'name',
                [   // Buttons Edit, Delete, Show Models
                    'content' => function ($model, $key, $index, $column) {
                        $a_text = '<span class="glyphicon glyphicon-pencil"></span>';
                        $ret = Html::a($a_text, '#', ['class' => 'model_edit_link', 'title' => Yii::t('site', 'EDIT'), 'model_name' => 'TransModel']);
                        $a_text = '<span class="glyphicon glyphicon-remove"></span>';
                        $ret .= Html::a($a_text, '#', ['class' => 'model_delete_link', 'title' => Yii::t('site', 'DELETE'), 'name_value' => $model->name, 'model_name' => 'TransModel']);                       
                        return $ret;
                    },
                    'options' => ['style' => 'width:75px;'],               
                ],
            ],
        ]);
        
        Pjax::end();    
    ?>        
    </div>    
</div>


<?php
    // MODAL FORM for adding and editing
    // We will use the same form for the TransBrand & TransModel tables,
    // so creating the necessary fields for the both of them
    $brand_model = new \app\models\TransBrand();
    $model_model = new app\models\TransModel();

    Modal::begin([
        'id' => 'modalform_cnt',
        'size' => Modal::SIZE_SMALL,
        'footer' => Html::button('OK', ['id' => 'modalform_btnok', 'class' => 'btn btn-primary', 'style' => 'width: 100px;']),
    ]);

    $form = ActiveForm::begin([
        'id' => 'modalform',
        'action' => '',
    ]);
    
    // model_name
    echo Html::hiddenInput('model_name', '', ['id' => 'model_name']);
    
    // ids hidden fields
    echo $form->field($brand_model, 'id')->hiddenInput()->label(false);
    echo $form->field($brand_model, 'cat_id')->hiddenInput()->label(false);
    echo $form->field($model_model, 'id')->hiddenInput()->label(false);
    echo $form->field($model_model, 'brand_id')->hiddenInput()->label(false);
    
    // Name fields. Will show only one of them depends on situation
    echo $form->field($brand_model, 'name')->textInput(['style' => 'display:none;']);
    echo $form->field($model_model, 'name', ['options' => ['style' => 'margin-top:-15px;']])->textInput(['style' => 'display:none;'])->label(false);
    
    Activeform::end();
    
    Modal::end();




    ////////////////////////////////////////////////////
    // CSS
if (!Yii::$app->request->isPjax) {
    $trans_models_css = <<< TMCSS
        .header_cnt {padding:12px 0 36px 0;}
        .header_cnt h4 {display:inline; margin-left:96px;}
        .header_cnt button {float:right; margin-right:96px;}
TMCSS;

    $this->registerCss($trans_models_css);
}



    ////////////////////////////////////////////////////////////////////////
    // This vars are used in JS-script below
    $url = Url::current(['cat_id' => null, 'brand_id' => null]);
    $urlCat = Url::current(['cat_id' => $cat_id, 'brand_id' => null]);
    $updateModelUrl = Url::to(['/auxx/update-model', 'view' => 'model_id', 'validate' => false]);
?>


<?php if (!Yii::$app->request->isPjax) : ?>
<?php $this->beginJs(); ?>
<script>
    // TransCat dropDown changing
    $("#transcat_dd").on("change", function() {
        window.location.href = "<?= $url ?>" + "?cat_id=" +$(this).val();
    });


    ////////////////////////////////////////////////////////////////////////
    //          BTN_ADD  &  EDIT_LINK  CLICKING
    // btnAddBrand click & btnAddModel (show modal form)
    $("#btn_add_brand, #btn_add_model").on("click", function() {
        $("#transbrand-id, #transbrand-name").val("");
        $("#transbrand-cat_id").val("<?= $cat_id ?>");
        $("#transmodel-id, #transmodel-name").val("");
        $("#transmodel-brand_id").val("<?= $brand_id ?>");

        var model_name = $(this).attr("model_name");
        return showModal(model_name);
    });

    // brandEditLink click (show modal form)
    $("body").on("click", "a.brand_edit_link, a.model_edit_link", function() {
        var tr = $(this).closest("tr")
        var id = tr.attr("data-key");
        var name = tr.children().eq(1).text();

        $("#transbrand-id, #transmodel-id").val(id);        // The only 1 id will be used later (transbrand-id or transmodel-id)
        $("#transbrand-name, #transmodel-name").val(name);    // The only 1 name will be used later (transbrand-name or transmodel-name)
        $("#transbrand-cat_id").val("<?= $cat_id ?>");
        $("#transmodel-brand_id").val("<?= $brand_id ?>");

        var model_name = $(this).attr("model_name");
        return showModal(model_name);
    });

    function showModal(model_name) {
        if (model_name=="TransBrand") {
            $("#transmodel-name").hide();
            $("#transbrand-name").show();
        } else {
            $("#transmodel-name").show();
            $("#transbrand-name").hide();
        }

        $("#model_name").val(model_name);
        $("#modalform div").removeClass("has-success");
        $("#modalform_cnt").modal("show");
    }


    ////////////////////////////////////////////////////////////////////////
    //          MODAL  FORM  BtnOk click
    $("#modalform_btnok").on("click", function() {
        $.ajax({
            url: "<?= $updateModelUrl ?>",
            type: "post",
            dataType: "json",
            data: $("#modalform").serialize(),
            error: function(jqXHR, textStatus, errorThrown) {
                alert(textStatus);
            },
            success: function(response) {
                if(response.result != "ok") {
                    alert(response); return false;
                }

                $("#modalform_cnt").modal("hide");
                var isBrand = ($("#model_name").val()=="TransBrand");

                if ($("#transbrand-id").val()=="" && isBrand)       // Creating new brand
                    window.location.href = "<?= $urlCat ?>" + "&brand_id=" +response.model_id;
                else if (isBrand)                                   // Updating brand
                    $.pjax.reload("#trans_brand_grid_cnt");
                else                                                // Creating or Updating model
                    $.pjax.reload("#trans_model_grid_cnt");
            }
        });
    });


    ////////////////////////////////////////////////////////////////////////
    //          DELETE Brand and DELETE Model
    $("body").on("click", "a.brand_delete_link, a.model_delete_link", function(e) {
        e.preventDefault();
        var tr = $(this).closest("tr");
        var model_id = tr.attr("data-key");
        var model_name = $(this).attr("model_name");
        var name_value = $(this).attr("name_value");

        if (model_name == "TransBrand")
            var mess = "<?= Yii::t('admin', 'DELETE_BRAND') ?>" + name_value + " <?= Yii::t('admin', 'DELETE_BRAND_DOP') ?>";
        else
            var mess = "<?= Yii::t('admin', 'DELETE_MODEL') ?>" + name_value;

        if (!confirm(mess))
            return false;

        $.ajax({
            url: "<?= Url::to(['/auxx/delete-model-by-id']) ?>",
            type: "post",
            dataType: "json",
            data: "model_name=" +model_name +"&model_id=" +model_id,
            error: function(jqXHR, textStatus, errorThrown) {
                alert(textStatus);
            },
            success: function(response) {
                if (response!="ok")
                    alert(response);
                else if (model_name == "TransModel")
                    tr.remove();
                else
                    window.location.href = "<?= $urlCat ?>";
            }
        });
    });
</script>
<?php $this->endJs(); ?>
<?php endif; ?>