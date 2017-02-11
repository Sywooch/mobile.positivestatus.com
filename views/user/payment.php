<?php

    use app\components\Y;

    use app\models\User;

    use yii\widgets\ActiveForm;

    use yii\helpers\Html;

    $account_id = Yii::$app->user->identity->account_id;
    $id = Yii::$app->user->identity->getId();

    $time = date("d/m/Y");
    $time_expire = date("d/m/Y", strtotime(" +1 months"));

?>


    <div class="adapt">

        <h2 class="paym"><?= Yii::t('user', 'PAYMENT') ?></h2>

        <h4 class="idds">ID#<span> <?= Y::getPaymentId($id) ?></span></h4>


    </div>



<?php /*

    <div class="cupon">

        <div class="inf">

            <p class="cen"><b>€</b>50</p>



            <p><?= Yii::t('user', 'LABEL_ACCOUNT') .' ' .User::getAccount($account_id) ?> <i>за</i> 1 месяц <br> ??.??.???? <i>по </i>??.??.????</p>

        </div>

        <a class="cup" href="#">

            Активировать<br>купон

        </a>

    </div>

*/ ?>

    <aside class="lefta">
<?php
    //$model = new \app\models\PaymentForm();
    $form = ActiveForm::begin([
        'id' => 'business_plan_form',
        'method' => 'post',
        'action' => \yii\helpers\Url::toRoute('/user/transfer'),

        'validateOnBlur' => false,
        'validateOnChange' => false,
        'options' => [
            'class' => 'forma black"'
        ]
    ]); ?>


            <fieldset>

                <label class="firnam">Firmenname

                    <?php echo $form->field($payment, 'fname')->Textinput()->label(false); ?>
                </label>

                <label class="fionam">Kontoinhaber

                    <?php echo $form->field($payment, 'fioname')->Textinput()->label(false); ?>


                </label>



                <div class="two">

                    <label class="noimg">Stoernumer

                        <?php echo $form->field($payment, 'nvb1')->Textinput()->label(false); ?>

                    </label>

                    <label class="noimg">USt-idNr.

                        <?php echo $form->field($payment, 'dmbs2')->Textinput()->label(false); ?>

                    </label>

                </div>

                <label class="nbanke">Name der Bank

                    <?php echo $form->field($payment, 'bnk')->Textinput()->label(false); ?>

                </label>



                <label class="iban">IBAN

                    <?php echo $form->field($payment, 'ibnn')->Textinput()->label(false); ?>

                </label>



                <div class="two two2">

                    <label class="noimg">BIC

                        <?php echo $form->field($payment, 'bic1')->Textinput()->label(false); ?>

                    </label>

                </div>
            <div class="submit_buttons">
            <?= Html::submitButton('bank transfer',['id'=>'bank_transfer_submit', 'name'=>'bank_transfer','style'=>'display:none;', 'value' => 'bank_transfer', 'data-pjax' => 0]) ?>
            <?= Html::submitButton('paypal',['id'=>'paypal', 'name'=>'paypal', 'style'=>'display:none;', 'value' => 'paypal', 'data-pjax' => 0]) ?>
            </div>

            </fieldset>

    <?php ActiveForm::end() ?>


    </aside>

    <aside class="righta">

        <div class="cheker">

            <p class="top">Rechnung #321</p>



            <?php // All the other Accounts except Business are free ?>

            <h2 class="paym"><?= Yii::t('user', 'LABEL_ACCOUNT') .' ' .User::getAccount(User::ACCOUNT_BUSINESS) ?>

                <div class="dats">

                    <i>с</i> <?= $time?> <br>

                    <i>по </i> <?= $time_expire ?>

                </div>

            </h2>

            <div class="tex">

                <?= Yii::t('user', 'BLOCK_BUSINESS_DETAILS') ?>

            </div>

            <div class="itog">

                <?= Yii::t('user', 'TOTAL') ?>:<span><i>€</i> <b>50 </b></span>

            </div>

        </div>


        <!--class="cheker">  -->

    </aside>


    <div class="sposob">

    <p class="vibor"><?= Yii::t('user', 'SELECT_PAYMENT_TYPE') ?></p>



    <div class="fl_line">

        <a id="bank-transfer-link" class="bank" href="#">

            <img src="/images/bank.png" alt="">

            <?= Yii::t('user', 'USING_TRANSFER') ?></a>

       <!-- <a class="karta" href="#">

            <img src="/images/kart.png" alt="">

            <?//= Yii::t('user', 'USING_CREDITCARD') ?></a>-->

<!--        <a id="paypal-link" class="payp" href="#">-->
<!---->
<!--            <img src="/images/payp.png" alt="">-->
<!---->
<!--            --><?//= Yii::t('user', 'USING_PAYPAL') ?><!--</a>-->
        
        

               
         <?php
        $payTest = 'https://www.sandbox.paypal.com/cgi-bin/websc';
		$pay='https://www.paypal.com/cgi-bin/webscr';
        $userId = Y::getPaymentId($id); // id текущего пользователя

        $receiverEmail = 'info.avz2011@gmail.com';


        $productId = 1;
        $itemName = 'MobileMakler business account';    // название продукта
        $amount = '0.01'; // цена продукта(за 1 шт.)
        $quantity = 1;  // количество

        $returnUrl = 'http://mobile.positivestatus.com/user/payment-success?status=ok&user_id='.$id;
        $customData = ['user_id' => $userId, 'product_id' => $productId];

        ?>
        <form action="<?=$pay?>" method="post" target="_top" class="payp">
            <input type="hidden" name="cmd" value="_xclick">
            <input type="hidden" name="business" value="<?php echo $receiverEmail; ?>">
            <input id="paypalItemName" type="hidden" name="item_name" value="<?php echo $itemName; ?>">
            <input id="paypalQuantity" type="hidden" name="quantity" value="<?php echo $quantity; ?>">
            <input id="paypalAmmount" type="hidden" name="amount" value="<?php echo $amount; ?>">
            <input type="hidden" name="no_shipping" value="1">
            <input type="hidden" name="return" value="<?php echo $returnUrl; ?>">
			<input type="hidden" name="user_id" value="<?php echo $id; ?>">

            <input type="hidden" name="custom" value="<?php echo json_encode($customData);?>">

            <input type="hidden" name="currency_code" value="EUR">
            <input type="hidden" name="lc" value="US">
            <input type="hidden" name="bn" value="PP-BuyNowBF">

            <input type="image" src="/images/payp.png" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
            <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
            <div><?= Yii::t('user', 'USING_PAYPAL') ?> </div>
        </form>





    </div>
    <?php $this->beginJs(); ?>
        <script>
            $("#paypal-link").click( function() {
                $('#paypal').click();
            });
            $("#bank-transfer-link").click( function() {
                $('#bank_transfer_submit').click();
            });

        </script>
<?php $this->endJs(); ?>                                                                              