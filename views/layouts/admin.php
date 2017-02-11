<?php
/* @var $this \yii\web\View */
/* @var $content string */
    use yii\helpers\Html;

    \app\assets\AdminAsset::register($this);
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>

    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>

<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <div class="header">
        <div class="container" style="padding: 0">
            <?php echo $this->render('_mainmenu'); ?>
            <?php echo $this->render('_adminmenu'); ?>
        </div>
    </div>


    <div style="min-height: 400px; padding: 15px 25px; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif">
        <?= $content ?>
    </div>


    <div class="footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?>  <a href=""> <?= Yii::t('site', 'AGREEMENTS') ?> </a></p>
            <div class="social">
                <ul>
                    <li class="fb"><a href=""></a></li>
                    <li class="tw"><a href=""></a></li>
                    <li class="goo"><a href=""></a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
