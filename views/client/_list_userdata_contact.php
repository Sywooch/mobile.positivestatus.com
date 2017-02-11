<?php
    // Parameters:
    // $model   - UserContact
    use yii\helpers\Html;
    use app\models\UserContact;

    $image = UserContact::getAvatarFileName($model->id, 'url', true);
?>


<div class="row">
    <div class="col-xs-4 contact_img">
        <?= Html::img($image); ?>
    </div>
    
    <div class="col-xs-8 contact_name">
        <?= $model->name; ?>
    </div>
</div>

<div class="contact_phone">
    <?= $model->phone; ?>
</div>

<div class="contact_cell">
    <?= $model->cell; ?>
</div>


<?php if (!Yii::$app->request->isPjax) : ?>
<?php $this->beginCss(); ?>
<style>
    div.contact_name {font-size:20px; }
    div.contact_phone, div.contact_cell {padding-top: 12px;}
</style>
<?php $this->endCss(); ?>
<?php endif; ?>
