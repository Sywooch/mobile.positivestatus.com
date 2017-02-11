<?php
    /**
     * @var $profile UserProfile
     */
    use yii\helpers\Html;
?>


<!-- Website, Facebook, Twitter -->
<?php foreach ($profile->sites as $site) : ?>
    <label class="sites"> <?= $profile->getAttributeLabel('sites') ?>
        <?= Html::activeTextInput($profile, "sites[]", ['value' => $site, 'class' => 'http']) ?>
    </label>
<?php endforeach ; ?>

<?php foreach ($profile->facebooks as $fb) : ?>
    <label class="faceb"> <?= $profile->getAttributeLabel('facebooks') ?>
        <?= Html::activeTextInput($profile, "facebooks[]", ['value' => $fb]) ?>
    </label>
<?php endforeach ; ?>

<?php foreach ($profile->twitters as $tw) : ?>
    <label class="twit2"> <?= $profile->getAttributeLabel('twitters') ?>
        <?= Html::activeTextInput($profile, "twitters[]", ['value' => $tw]) ?>
    </label>
<?php endforeach ; ?>


<!-- Add Link -->
<div class="plus one" data-profile_id="<?= $profile->id ?>">
    <span>
        <a href="#" class="add" id="ssil">+ <?= Yii::t('user', 'LINK') ?>
            <ul class="downbox addpole">
                <li class="add_facebook"> <?= $profile->getAttributeLabel('facebooks') ?> </li>
                <li class="add_twitter"> <?= $profile->getAttributeLabel('twitters') ?> </li >
                <li class="add_site"> <?= $profile->getAttributeLabel('sites') ?> </li>
            </ul>
        </a>
    </span>

    (Facebook, Twitter <?= Yii::t('user', 'ETC') ?>)
</div>


<?php $this->beginJs(); ?>
    <script>
        $("#ssil").click(function(e) {
            e.preventDefault();
            $(this).children("ul.downbox").slideDown(400);
        });

        $("body").on( "click", "li.add_facebook", function(e) {
            e.preventDefault();
            $(this).parent('ul.downbox').slideUp(100);

            var name = $(this).text();
            var input = $('<input type="text" value="">').attr('name', 'UserProfile[facebooks][]');
            var clone = $('<label class="faceb"></label>').html(name).append(input);
            $(this).closest(".plus").prev().append(clone);
        });

        $("body").on( "click", "li.add_twitter", function(e) {
            e.preventDefault();
            $(this).parent('ul.downbox').slideUp(100);

            var name = $(this).text();
            var input = $('<input type="text" value="">').attr('name', 'UserProfile[twitters][]');
            var clone = $('<label class="twit2"></label>').html(name).append(input);
            $(this).closest(".plus").prev().append(clone);
        });

        $("body").on( "click", "li.add_site", function(e) {
            e.preventDefault();
            $(this).parent('ul.downbox').slideUp(100);

            var name = $(this).text();
            var input = $('<input type="text" value="">').attr('name', 'UserProfile[sites][]');
            var clone = $('<label class="http"></label>').html(name).append(input);
            $(this).closest(".plus").prev().append(clone);
        });
    </script>
<?php $this->endJs(); ?>