<?php
    // Parameters
    // $cat_id      - active TransCat id (for the Grid)
    // $cat_name    - active TransCat name (for the text above Grid #2)
    // $cat_blocked - Prohibit to delete and create Subcategories
    // $cat_dp      - TransSubcat DataProvider (source for the Grid #1)
    // $group_dp    - TransCatGroup DataProvider (source for the Grid #2)
    use yii\widgets\Pjax;
    use yii\grid\GridView;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\grid\SerialColumn;
    use yii\bootstrap\Modal;
    use yii\widgets\ActiveForm;
    $lang = Yii::$app->language;

    $ddItems = \app\models\TransCatGroup::getDropdownItems();

    echo Html::tag('h4', Yii::t('admin', 'TRANSCAT_HEADER'));
    echo '<br />';
?>
    
<div class="row">
    <div class="col-xs-5">
        <div class="row btn_add_cnt">
            <button id="btn_add_group" class="btn btn-default" model_name="TransCatGroup">
                <span class="glyphicon glyphicon-plus"></span> <?= Yii::t('admin', 'ADD_GROUP') ?>
            </button>
        </div>

        <?php
        // TransCatGroups (#1)
        Pjax::begin(['id' => 'group_grid_cnt']);

        echo GridView::widget([
            'id' => 'group_grid',
            'dataProvider' => $group_dp,
            'layout' => '{items} {pager}',
            'columns' => [
                [
                    'class' => SerialColumn::className(),
                    'options' => ['style' => 'width:50px;'],
                ],
                'ru',
                'de',
                [   // Buttons Edit, Delete
                    'content' => function ($model, $key, $index, $column) use ($lang) {
                        $a_text = '<span class="glyphicon glyphicon-pencil"></span>';
                        $ret = Html::a($a_text, '#', ['class' => 'group_edit_link', 'title' => Yii::t('site', 'EDIT'), 'model_name' => 'TransCatGroup']);

                        $a_text = '<span class="glyphicon glyphicon-remove"></span>';
                        $ret .= Html::a($a_text, '#', ['class' => 'group_delete_link', 'title' => Yii::t('site', 'DELETE'), 'name_value' => $model->$lang, 'model_name' => 'TransCatGroup']);
                        return $ret;
                    },
                    'options' => ['style' => 'width:75px;'],
                ],
            ],
        ]);

        Pjax::end();
        ?>
    </div>

    <div class="col-xs-7">
        <div class="btn_add_cnt">
            <button id="btn_add_cat" class="btn btn-default" model_name="TransCat">
                <span class="glyphicon glyphicon-plus"></span> <?= Yii::t('admin', 'ADD_CATEGORY') ?>
            </button>
        </div>

    <?php
        // TransCat Grid (#2)
        Pjax::begin(['id' => 'category_grid_cnt']);
        
        echo GridView::widget([
            'id' => 'category_grid',
            'dataProvider' => $cat_dp,
            'layout' => '{items} {pager}',
            'columns' => [
                [
                    'class' => SerialColumn::className(),
                    'options' => ['style' => 'width:50px;'],
                ],
                'ru',
                'de',
                [
                    'attribute' => 'group_id',
                    'content' => function($model, $key, $index, $column) use ($ddItems) {
                        return Html::activeDropDownList($model, 'group_id', $ddItems, ['class' => 'form-control group_dropdown', 'prompt' => '', 'data_ini_value' => $model->group_id]);
                    }
                ],
                [   // Buttons Edit, Delete, Show Models
                    'content' => function ($model, $key, $index, $column) use ($lang) {
                        $a_text = '<span class="glyphicon glyphicon-pencil"></span>';
                        $ret = Html::a($a_text, '#', ['class' => 'cat_edit_link', 'title' => Yii::t('site', 'EDIT'), 'model_name' => 'TransCat']);

                        if (empty($model->mobile_key)) {
                            $a_text = '<span class="glyphicon glyphicon-remove"></span>';
                            $ret .= Html::a($a_text, '#', ['class' => 'cat_delete_link', 'title' => Yii::t('site', 'DELETE'), 'name_value' => $model->$lang, 'model_name' => 'TransCat']);
                        }
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
    // We will use the same form for the TransCat & TransSubcat tables,
    // so creating the necessary fields for the both of them
    $category_model = new \app\models\TransCat();
    $group_model = new app\models\TransCatGroup();

    Modal::begin([
        'id' => 'modalform_cnt',
        'footer' => Html::button('OK', ['id' => 'modalform_btnok', 'class' => 'btn btn-primary', 'style' => 'width: 100px;']),
    ]);

    $form = ActiveForm::begin([
        'id' => 'modalform',
        'action' => '',
        'enableClientValidation' => false,
        'validateOnBlur' => false,
        'validateOnChange' => false
    ]);
    
    // model_name
    echo Html::hiddenInput('model_name', '', ['id' => 'model_name']);
    
    // ids hidden fields
    echo $form->field($category_model, 'id')->hiddenInput()->label(false);
    echo $form->field($group_model, 'id')->hiddenInput()->label(false);

    // Name fields. Will show only one of them depends on situation
    echo '<div id="cat_cnt" class="row" style="display:none">';
    echo $form->field($category_model, 'ru', ['options' => ['class' => 'col-xs-6']])->textInput(['class' => 'form-control']);
    echo $form->field($category_model, 'de', ['options' => ['class' => 'col-xs-6']])->textInput(['class' => 'form-control']);
    echo '</div>';
    
    echo '<div id="group_cnt" class="row" style="display:none;">';
    echo $form->field($group_model, 'ru', ['options' => ['class' => 'col-xs-6']])->textInput(['class' => 'form-control']);
    echo $form->field($group_model, 'de', ['options' => ['class' => 'col-xs-6']])->textInput(['class' => 'form-control']);
    echo '</div>';    
    
    Activeform::end();
    
    Modal::end();



    ////////////////////////////////////////////////////////////////////////
    // This vars are used in JS-script below
    $url = Url::current();
    $updateModelUrl = Url::to(['/auxx/update-model', 'view' => 'model_id', 'validate' => false]);
?>


<?php if (!Yii::$app->request->isPjax) : ?>
<?php $this->beginJs(); ?>
<script>
    //          BTN_ADD  &  EDIT_LINK  CLICKING
    // btnAddHead click & btnAddFeature (show modal form)
    $("#btn_add_cat, #btn_add_group").on("click", function() {
        $("#transcat-id, #transcat-ru, #transcat-de").val("");
        $("#transcatgroup-id, #transcatgroup-ru, #transcatgroup-de").val("");

        var model_name = $(this).attr("model_name");
        return showModal(model_name);
    });

    // EditLink click (show modal form)
    $("body").on("click", "a.cat_edit_link, a.group_edit_link", function(e) {
        e.preventDefault();
        var tr = $(this).closest("tr")
        var id = tr.attr("data-key");
        var ru = tr.children().eq(1).text();
        var de = tr.children().eq(2).text();

        $("#transcat-id, #transcatgroup-id").val(id);    // The only 1 id will be used later (transcat-id or transcatgroup-id)
        $("#transcat-ru, #transcatgroup-ru").val(ru);    // The only 1 name will be used later (transcat-name or transsubcat-name)
        $("#transcat-de, #transcatgroup-de").val(de);

        var model_name = $(this).attr("model_name");
        return showModal(model_name);
    });

    function showModal(model_name) {
        if (model_name=="TransCat") {
            $("#group_cnt").hide();
            $("#cat_cnt").show();
        } else {
            $("#group_cnt").show();
            $("#cat_cnt").hide();
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

                if ($("#model_name").val()=="TransCatGroup")
                    window.location.reload();
                else
                    $.pjax.reload("#category_grid_cnt");
            }
        });
    });


    ////////////////////////////////////////////////////////////////////////
    //          DELETE Group and DELETE Category
    $("body").on("click", "a.group_delete_link, a.cat_delete_link", function(e) {
        e.preventDefault();
        var tr = $(this).closest("tr");
        var model_id = tr.attr("data-key");
        var model_name = $(this).attr("model_name");
        var name_value = $(this).attr("name_value");

        if (model_name == "TransCat") {
            var mess = "<?= Yii::t('admin', 'DELETE_CATEGORY') ?>" + name_value;
        } else if ($("select.group_dropdown[data_ini_value=" +model_id +"]").length > 0) {
            alert("<?= Yii::t('admin', 'DELETE_GROUP_BLOCK') ?>");
            return false;
        } else {
            var mess = "<?= Yii::t('admin', 'DELETE_GROUP') ?> " + name_value;
        }

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
                else if (model_name == "TransCat")
                    tr.remove();
                else
                    window.location.reload();
            }
        });
    });


    $("body").on("change", "select.group_dropdown", function() {
        var dropDown = $(this);
        var id = dropDown.closest("tr").attr("data-key");
        var group_id = dropDown.val();
        var ini_val = dropDown.attr("data_ini_value");

        $.ajax({
            url: "<?= $updateModelUrl ?>",
            type: "post",
            dataType: "json",
            data: {
                model_name: "TransCat",
                "TransCat[id]": id,
                "TransCat[group_id]": group_id
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert(textStatus);
            },
            success: function(response) {
                if(response.result != "ok") {
                    alert(response); return false;
                    dropDown.val(ini_val);
                } else {
                    dropDown.attr("data_ini_value", group_id);
                }
            }
        });
    });
</script>
<?php $this->endJs(); ?>
<?php endif; ?>