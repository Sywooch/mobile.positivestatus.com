<?php
    // Parameters:
    // $model  - Trans::findOne() OR new Trans();
use app\models\TransCat;
use yii\helpers\Html;

    $tcModel = TransCat::findOne($model->cat_id);
    $hasMotoHours = ($tcModel->mobile_key == 'ConstructionMachine');
?>



<?= $this->render('_edit_proposal_form_panel', ['model' => $model]); ?>

<div class="form_add form">
    <div class="line">
        <div class="floatl marg_t">
            <label class="labl"><?= Yii::t('client', 'TEXT_DE') ?></label>
            <?= Html::activeTextarea($model, 'text_de') ?>
        </div>



        <div class="rig mini">
            <div class="line_for_mob">
                <?php if ($hasMotoHours) : ?>
                <div class="rigos">
                    <label class="labl"><?= Yii::t('client', 'MOTOHOURS') ?></label>
                    <?= Html::activeTextInput($model, 'motohours') ?>
                </div>
                <?php endif; ?>

                <div class="rigos">
                    <label class="labl"><?= Yii::t('client', 'CAPACITY') ?></label>
                    <?= Html::activeTextInput($model, 'capacity') ?>
                </div>

                 <div class="calkos">
                   <div class="vll">
                        <label class="labl"><?= Yii::t('client', 'POWER') ?></label>
                        <?= Html::activeTextInput($model, 'power') ?>
                        <!--input id="kvt" type="number" min="0" placeholder="100" autofocus-->
                        <label style="display:none">
						    <input class="coef" disabled type="text" value="1,36"></label>
                   </div>
                   <div class="vrr">
                        <label class="labl"><?= Yii::t('client', 'POWER_LS') ?></label>
                        <input id="trans-power-ls" value="">
                   </div>
                </div>  <!--id="cako">  -->
                <div id="posito"> <!-- меняет позицию абсолют и в другое место перемещает в моб. версии -->
                    <label class="labl"><?= Yii::t('client', 'MILEAGE') ?></label>
                    <?= Html::activeTextInput($model, 'mileage', ['class' => 'marjo']) ?>
                </div>
            </div>


            <label class="labl"><?= Yii::t('client', 'PRICE_BRUT') ?> </label>
            <?= Html::activeTextInput($model, 'price_brut') ?>
            <?= Html::activeHiddenInput($model, 'nds') ?>
            <?= Html::activeHiddenInput($model, 'price_net') ?>

            <div class="checkbox cacl">
                <input type="checkbox" id="whumis" />

                <label for="whumis">
                    <span id="nds_check_label" class="pseudo-checkbox white"></span>
                        <span class="label-text"><?= Yii::t('client', 'NDS_RET') ?>
                            <ul class="nav2 minis">
                                <li>
                                    <a id="nds_value" href="#"><?= $model->nds ?>%</a>
                                    <ul id="nds_list">
                                        <li><a data-value="16" href="#">16%</a></li>
                                        <li><a data-value="17" href="#">17%</a></li>
                                        <li><a data-value="18" href="#">18%</a></li>
                                        <li><a data-value="19" href="#">19%</a></li>
                                        <li><a data-value="20" href="#">20%</a></li>
                                        <li><a data-value="21" href="#">21%</a></li>
                                        <li><a data-value="22" href="#">22%</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </span>
                    <div class="brutal"><b id="netto"><?= $model->price_net ?></b> € <?= Yii::t('client', 'NETTO') ?></div>
                </label>
            </div>


        </div>

    </div>
    <div class="line">
        <div class="floatl youtline">
            <div class="you_container"><img class="yao" src="/images/youtube.png" width="68" height="28" alt=""/> </div>

            <div class="ssil">
                <label class="labl"><?= Yii::t('client', 'YOUTUBE') ?></label>
                <?= Html::activeTextInput($model, 'youtube') ?>
            </div>
        </div>
         <div class="rig mini saves"><input type="button" onclick="$(this).closest('form').submit();" class="btns" value="<?= Yii::t('site', 'SAVE') ?>">   </div>
    </div>
</div> <!--class="form_add">-->


<?php if (!Yii::$app->request->isPjax) : ?>
    <?php $this->beginJs(); ?>
    <script>
        if ( $("#trans-power").val() == "0" || $("#trans-power").val() == "" )
            $("#trans-power").val("");
        else
            calcPower($("#trans-power"));

        if ($("#trans-nds").val() > 0)
            $("#whumis").prop("checked", true);

        $("#trans-price_brut").on("keyup", function() {
            if (this.value.match(/[^0-9]/g)) {
                this.value = this.value.replace(/[^0-9]/g, '');
            }

            var nds = $("#trans-nds").val();
            calcPriceNet(nds);
        });

        // 18% by default
        $("body").on("change", "#whumis", function() {
            if ($(this).prop("checked")) {
                $("#nds_value").text("18%");
                calcPriceNet(18);
            }
            else {
                calcPriceNet(0);
            }
        });

        $("body").on( "click", "#nds_list a", function(e) {
            e.preventDefault();
            var nds = $(this).attr("data-value");
            calcPriceNet(nds);
        });

        function calcPriceNet(nds) {
            var price_brut = ($("#trans-price_brut").val() == "") ? 0 : parseInt($("#trans-price_brut").val());
            var price_net = Math.round( price_brut / (1+nds/100) );
            $("#netto").text(price_net);

            $("#trans-price_net").val(price_net);
            $("#trans-nds").val(nds);
        }


        $("#trans-power, #trans-power-ls").on("input", function(e) {
            var digits = $(this).val().replace(/[^\d,]/g, '');

            if (isNaN(parseInt(digits))) {
                $("#trans-power, #trans-power-ls").val("");
            }
            else {
                $(this).val(digits);
                calcPower($(this));
            }
        });

        function calcPower(srcInput) {
            var koeff = 1.3596;
            var val = (srcInput.attr("id") == "trans-power-ls") ? parseInt(srcInput.val())/koeff : parseInt(srcInput.val())*koeff;
            var destInput = (srcInput.attr("id") == "trans-power-ls") ? $("#trans-power") : $("#trans-power-ls");
            destInput.val( val.toFixed(0) );
        }
    </script>

    <?php $this->endJs(); ?>
<?php endif; ?>
