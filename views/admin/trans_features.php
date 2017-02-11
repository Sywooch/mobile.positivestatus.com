<?php
    // Parameters
    // $cat_id      - active TransCat id (for the dropDown)
    // $head_id     - active TransFeatureH id (for the Grid)
    // $head_name   - active TransFeatureH name (for the text above Grid #2)
    // $cat_dd      - Source for TransCat dropDown (an array)
    // $head_dp     - TransFeatureH DataProvider (source for the Grid #1)
    // $feature_dp  - TransFeature DataProvider (source for the Grid #2)
    use yii\widgets\Pjax;
    use yii\grid\GridView;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\grid\SerialColumn;
    use yii\bootstrap\Modal;
    use yii\widgets\ActiveForm;
    $lang = Yii::$app->language;
    
    
    echo Html::tag('h4', Yii::t('admin', 'DOP_FILTERS'));
    echo '<br />';
?>
    
<div class="row">
    <div class="col-xs-6">
    <?php
        // TransCat dropDown and TransFeatureH Grid (#1)
        echo '<div class="row btn_add_cnt">';
            echo '<div class="col-xs-6">';
            echo Html::dropDownList('trans_cat', $cat_id, $cat_dd, ['id' => 'transcat_dd', 'class' => 'form-control']);
            echo '</div>';
            
            echo '<div class="col-xs-6">';
            echo Html::button(
                '<span class="glyphicon glyphicon-plus"></span> ' .Yii::t('admin', 'ADD_FILTER'), 
                ['id' => 'btn_add_head', 'class' => 'btn btn-default', 'model_name' => 'TransFeatureH']
            );
            echo '</div>';
        echo '</div>';
        
        Pjax::begin(['id' => 'trans_head_grid_cnt']);
        
        echo GridView::widget([
            'id' => 'trans_head_grid',
            'dataProvider' => $head_dp,
            'layout' => '{items} {pager}',
            'columns' => [
                [
                    'class' => SerialColumn::className(),
                    'options' => ['style' => 'width:50px;'],
                ],
                'ru',
                'de',
                [   // Buttons Edit, Delete, Show Models
                    'content' => function ($model, $key, $index, $column) use ($lang) {
                        $a_text = '<span class="glyphicon glyphicon-pencil"></span>';
                        $ret = Html::a($a_text, '#', ['class' => 'head_edit_link', 'title' => Yii::t('site', 'EDIT'), 'model_name' => 'TransFeatureH']);
                        $a_text = '<span class="glyphicon glyphicon-remove"></span>';
                        $ret .= Html::a($a_text, '#', ['class' => 'head_delete_link', 'title' => Yii::t('site', 'DELETE'), 'name_value' => $model->$lang, 'model_name' => 'TransFeatureH']);                       
                        $a_text = '<span class="glyphicon glyphicon-arrow-right"></span>';
                        $ret .= Html::a($a_text, Url::current(['head_id' => $model->id]), ['class' => 'head_showfeatures_link', 'title' => Yii::t('site', 'SHOW_FEATURES'), 'data-pjax' => 0]);
                        return $ret;
                    },
                    'options' => ['style' => 'width:112px;'],               
                ],
            ],
        ]);
        
        Pjax::end();
    ?>
    </div>
    
    
    <div class="col-xs-6">
    <?php
        // TransFeatureH name and TransFeature Grid (#2)
        echo '<div class="row btn_add_cnt">';
            echo '<div class="col-xs-6">';
            echo Html::textInput('head_name', $head_name, ['class' => 'form-control', 'disabled' => 'disabled']);
            echo '</div>';
            
            echo '<div class="col-xs-6">';
            echo Html::button(
                '<span class="glyphicon glyphicon-plus"></span> ' .Yii::t('admin', 'ADD_FEATURE'), 
                ['id' => 'btn_add_feature', 'class' => 'btn btn-default', 'model_name' => 'TransFeature']
            );
            echo '</div>';
        echo '</div>';
        
        Pjax::begin(['id' => 'trans_feature_grid_cnt']);
        
        echo GridView::widget([
            'id' => 'trans_feature_grid',
            'dataProvider' => $feature_dp,
            'layout' => '{items} {pager}',
            'columns' => [
                [
                    'class' => SerialColumn::className(),
                    'options' => ['style' => 'width:50px;'],
                ],
                'ru',
                'de',
                [   // Buttons Edit, Delete, Show Models
                    'content' => function ($model, $key, $index, $column) use ($lang) {
                        $a_text = '<span class="glyphicon glyphicon-pencil"></span>';
                        $ret = Html::a($a_text, '#', ['class' => 'feature_edit_link', 'title' => Yii::t('site', 'EDIT'), 'model_name' => 'TransFeature']);
                        $a_text = '<span class="glyphicon glyphicon-remove"></span>';
                        $ret .= Html::a($a_text, '#', ['class' => 'feature_delete_link', 'title' => Yii::t('site', 'DELETE'), 'name_value' => $model->$lang, 'model_name' => 'TransFeature']);                       
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
    // We will use the same form for the TransFeatureH & TransFeature tables,
    // so creating the necessary fields for the both of them
    $head_model = new \app\models\TransFeatureH();
    $feature_model = new app\models\TransFeature();

    Modal::begin([
        'id' => 'modalform_cnt',
        'footer' => Html::button('OK', ['id' => 'modalform_btnok', 'class' => 'btn btn-primary', 'style' => 'width: 100px;']),
    ]);

    $form = ActiveForm::begin([
        'id' => 'modalform',
        'action' => '',
    ]);
    
    // model_name
    echo Html::hiddenInput('model_name', '', ['id' => 'model_name']);
    
    // ids hidden fields
    echo $form->field($head_model, 'id')->hiddenInput()->label(false);
    echo $form->field($head_model, 'cat_id')->hiddenInput()->label(false);
    echo $form->field($feature_model, 'id')->hiddenInput()->label(false);
    echo $form->field($feature_model, 'hid')->hiddenInput()->label(false);
    
    // Name fields. Will show only one of them depends on situation
    echo '<div id="head_cnt" class="row" style="display:none">';
    echo $form->field($head_model, 'ru', ['options' => ['class' => 'col-xs-6']])->textInput(['class' => 'form-control']);
    echo $form->field($head_model, 'de', ['options' => ['class' => 'col-xs-6']])->textInput(['class' => 'form-control']);
    echo '</div>';
    
    echo '<div id="feature_cnt" class="row" style="display:none;">';
    echo $form->field($feature_model, 'ru', ['options' => ['class' => 'col-xs-6']])->textInput(['class' => 'form-control']);
    echo $form->field($feature_model, 'de', ['options' => ['class' => 'col-xs-6']])->textInput(['class' => 'form-control']);
    echo '</div>';    
    
    Activeform::end();
    
    Modal::end();



    ////////////////////////////////////////////////////////////////////////
    // This vars are used in JS-script below
    $url = Url::current(['cat_id' => null, 'head_id' => null]);
    $urlCat = Url::current(['cat_id' => $cat_id, 'head_id' => null]);
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
    // btnAddHead click & btnAddFeature (show modal form)
    $("#btn_add_head, #btn_add_feature").on("click", function() {
        $("#transfeatureh-id, #transfeatureh-ru, #transfeatureh-de").val("");
        $("#transfeatureh-cat_id").val("<?= $cat_id ?>");

        $("#transfeature-id, #transfeature-ru, #transfeature-de").val("");
        $("#transfeature-hid").val("<?= $head_id ?>");

        var model_name = $(this).attr("model_name");
        return showModal(model_name);
    });

    // brandEditLink click (show modal form)
    $("body").on("click", "a.head_edit_link, a.feature_edit_link", function() {
        var tr = $(this).closest("tr")
        var id = tr.attr("data-key");
        var ru = tr.children().eq(1).text();
        var de = tr.children().eq(2).text();

        $("#transfeatureh-id, #transfeature-id").val(id);    // The only 1 id will be used later (transfeatureh-id or transfeature-id)
        $("#transfeatureh-ru, #transfeature-ru").val(ru);    // The only 1 name will be used later (transfeatureh-name or transfeature-name)
        $("#transfeatureh-de, #transfeature-de").val(de);
        $("#transfeatureh-cat_id").val("<?= $cat_id ?>");
        $("#transfeature-hid").val("<?= $head_id ?>");

        var model_name = $(this).attr("model_name");
        return showModal(model_name);
    });

    function showModal(model_name) {
        if (model_name=="TransFeatureH") {
            $("#feature_cnt").hide();
            $("#head_cnt").show();
        } else {
            $("#feature_cnt").show();
            $("#head_cnt").hide();
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
                var isHead = ($("#model_name").val()=="TransFeatureH");

                if ($("#transfeatureh-id").val()=="" && isHead)     // Creating new head
                    window.location.href = "<?= $urlCat ?>" + "&head_id=" +response.model_id;
                else if (isHead)                                    // Updating head
                    $.pjax.reload("#trans_head_grid_cnt");
                else                                                // Creating or Updating Feature
                    $.pjax.reload("#trans_feature_grid_cnt");
            }
        });
    });


    ////////////////////////////////////////////////////////////////////////
    //          DELETE Head and DELETE Feature
    $("body").on("click", "a.head_delete_link, a.feature_delete_link", function(e) {
        e.preventDefault();
        var tr = $(this).closest("tr");
        var model_id = tr.attr("data-key");
        var model_name = $(this).attr("model_name");
        var name_value = $(this).attr("name_value");

        if (model_name == "TransFeatureH")
            var mess = "<?= Yii::t('admin', 'DELETE_BRAND') ?>" + name_value + " <?= Yii::t('admin', 'DELETE_FILTER_DOP') ?>";
        else
            var mess = "<?= Yii::t('admin', 'DELETE_FEATURE') ?>" + name_value;

        if (!confirm(mess))
            return false;

        $.ajax({
            url: "<?= Url::to(['/auxx/delete-model-by-id']) ?>",
            type: "post",
            dataType: "html",
            data: "model_name=" +model_name +"&model_id=" +model_id,
            error: function(jqXHR, textStatus, errorThrown) {
                alert(textStatus);
            },
            success: function(response) {
                if (response!="ok")
                    alert(response);
                else if (model_name == "TransFeature")
                    tr.remove();
                else
                    window.location.href = "<?= $urlCat ?>";
            }
        });
    });
</script>
<?php $this->endJs(); ?>
<?php endif; ?>

