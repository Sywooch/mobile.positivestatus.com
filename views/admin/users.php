<?php
    // Parameters :
    // $user_dp - User ActiveDataProvider
    // $searchModel - UserSearch Model for the filter
    use app\models\User;
    use yii\grid\GridView;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\widgets\Pjax;

    use app\models\Pay;

Pjax::begin([
        'id' => 'user_grid_cnt',
    ]);
    
    echo Html::tag('h4', Yii::t('admin', 'USER_LIST'));
    
    echo GridView::widget([
        'id' => 'user_grid',
        'dataProvider' => $user_dp,
        'filterModel' => $searchModel,
        'filterPosition' => GridView::FILTER_POS_HEADER,
        'layout' => '{errors} {items} {pager}',
        'rowOptions' => function ($model, $key, $index, $grid) {
            return ['user_email' => $model->email];
        },
        'columns' => [
            'id'=>[
                'attribute' => 'id',
                'label' => 'ID',
                'format' => 'raw',
                'content' => function($model, $key, $index, $column) {
                    $id = $model->id;
                    $date = $model->registered_at;
                    return str_pad(sprintf("%03d", $id), 9, $date, STR_PAD_LEFT);
                }
            ],
            'account_id' => [
                'attribute' => 'account_id', 
                'content' => function($model, $key, $index, $column) {
                    $dd_options = ['class' => 'form-control input-sm', 'ini_value' => $model->account_id];
                    if ($model->id == Yii::$app->user->identity->id)
                        $dd_options['disabled'] = 'disabled';
                    
                    return Html::dropDownList('dd_account', $model->account_id, User::getAccounts(), $dd_options);
                },
                'label' => Yii::t('user', 'LABEL_ACCOUNT'),
                'filter' => Html::activeDropDownList($searchModel, 'account_id', User::getAccounts(), ['class' => 'form-control input-sm', 'prompt' => Yii::t('site', 'ALL'), 'title' => Yii::t('site', 'FILTER')]),
            ],
            'pays.status' =>[
                'attribute' => 'pays.status',
                'label' => 'Оплата',
                'format' => 'raw',

                'content' => function($model, $key, $index, $column) {

                    if (!$model->pays == null && $model->pays->status == 0 || $model->pays->status == 2) {
                        $date_expire = $model->pays->getPeriodExpire();
                        return Html::tag('span','Осталось дней:'.$date_expire,['style'=>'font-size: 11px']).'<br>'.Html::a('Подтведить ', '', ['style'=>'margin:0;font-size:15px;font-weight:600;', 'title' => 'Подтвердить', 'target' => '_blank', 'data-pay'=> $model->pays->id, 'class' => 'confirm_payment_link']);
                    }
                }
            ],
            'pays.expire'=>[
                'attribute' => 'pays.expire',
                'label' => 'Срок действия',
                'format' => 'raw',

                'content' => function($model, $key, $index, $column) {
                    if ($model->getPayedTo() )
                        return 'до '.$model->getPayedTo();
                }
            ],

//            'profile.zip',
//            'profile.address',
            'name',
            'email' => [
                'attribute' => 'email',
                'filter' => Html::activeTextInput($searchModel, 'email', ['class' => 'form-control input-sm', 'title' => Yii::t('site', 'FILTER')])
            ],
//            'registered' => ['attribute' => 'registered', 'label' => Yii::t('user', 'LABEL_REGISTERED')],
            'lastvisit' => ['attribute' => 'lastvisit', 'label' => Yii::t('user', 'LABEL_LASTVISIT')], 
            'status' => [
                'attribute' => 'status',

                'content' => function($model, $key, $index, $column) {
                    $dd_options = ['class' => 'form-control input-sm', 'ini_value' => $model->status];
                    if ($model->id == Yii::$app->user->identity->id)
                        $dd_options['disabled'] = 'disabled';                    
                    
                    return Html::dropDownList('dd_status', $model->status, User::getStatuses(), $dd_options);
                },
                'label' => Yii::t('user', 'LABEL_STATUS'),
                'filter' => Html::activeDropDownList($searchModel, 'status', User::getStatuses(), ['class' => 'form-control input-sm', 'prompt' => Yii::t('site', 'ALL'), 'title' => Yii::t('site', 'FILTER')]),
            ],

            [
                'content' => function($model, $key, $index, $column) {
                    $a_text = Html::tag('span', '', ['class' => 'glyphicon glyphicon-remove']);
                    return Html::a($a_text, '#', ['class' => 'delete_user_link', 'title' => Yii::t('site', 'DELETE')]);
                },                
            ],
        ],
    ]);
                
    Pjax::end();
?>


<?php if (!Yii::$app->request->isPjax) : ?>
<?php $this->beginJs(); ?>
<script>
    // Change Account, Change Status
    $("body").on("change", "select[name=dd_account], select[name=dd_status]", function() {
        var obj = $(this);
        var isAccount = obj.attr("name") == "dd_account";
        var isStatus = obj.attr("name") == "dd_status";

        var user_id = obj.closest("tr").attr("data-key");
        var user_email = obj.closest("tr").attr("user_email");
        var old_code = obj.attr("ini_value");
        var old_name = obj.children("[value=\"" +old_code +"\"]").text();
        var new_code = obj.val();
        var new_name = obj.children("[value=\"" +new_code +"\"]").text();

        var mess = isAccount ? "<?= Yii::t('admin', 'CHANGE_ACCOUNT') ?>" : "<?= Yii::t('admin', 'CHANGE_STATUS') ?>";
        mess += "\n" +user_email +"\n" +old_name +" -> " +new_name;
        if (!confirm(mess)) {
            obj.children("[value=\"" +old_code +"\"]").prop("selected", true);
            return false;
        }

        var attrib = isAccount ? "&User[account_id]=" : "&User[status]=";

        $.ajax({
            url: "<?= Url::to(['auxx/update-model']) ?>",
            type: "post",
            dataType: "json",
            data: "model_name=User&User[id]=" +user_id +attrib +new_code,
            error: function(jqXHR, textStatus, errorThrown) {
                alert(textStatus);
            },
            success: function(response) {
                if (response == "ok") {
                    obj.attr("ini_value", new_code);
                } else {
                    obj.children("[value=\"" +old_code +"\"]").prop("selected", true);
                    alert(response);
                }
            }
        });
    });


    //  Delete User
    $("body").on("click", "a.delete_user_link", function(e) {
        e.preventDefault();
        var user_id = $(this).closest("tr").attr("data-key");
        var user_email = $(this).closest("tr").attr("user_email");

        var mess = "<?= Yii::t('admin', 'DELETE_USER') ?>" +"\n" +user_email
        if (!confirm(mess))
            return false;

        $.ajax({
            url: "<?= Url::to(['auxx/delete-model-by-id']) ?>",
            type: "post",
            dataType: "html",
            data: "model_name=User&model_id=" +user_id,
            error: function(jqXHR, textStatus, errorThrown) {
                alert(textStatus);
            },
            success: function(response) {
                if (response == "ok")
                    $.pjax.reload({container: "#user_grid_cnt"});
                else
                    alert(response);
            }
        });
    });
    //Confirm payment
    $("body").on("click", "a.confirm_payment_link", function(e) {
        e.preventDefault();
        var obj = $(this);

        var pay_id = obj.attr("data-pay");
        console.log(pay_id);
        var user_id = obj.closest("tr").attr("data-key");
        console.log(user_id);
        xfr = $.ajax({
            url: "<?= Url::to(['auxx/confirm-test-payment']) ?>",
            type: "post",
            dataType: "json",
            data: "pay_id="+pay_id+"&user_id=" +user_id,
            error: function(jqXHR, textStatus, errorThrown) {
                alert(textStatus);
            },
            success: function(response) {
                if (response == "ok") {
                    console.log("ok")
                } else {
                    console.log("error");
                    alert(response);
                }
            }
        });
        console.log(xfr);
    });
</script>
    <?php $this->endJs(); ?>
<?php endif; ?>