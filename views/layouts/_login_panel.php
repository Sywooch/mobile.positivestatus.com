<?php
    use yii\helpers\Html;
    use yii\helpers\Url;
?>

<?php if (!Yii::$app->user->isGuest) : ?>

    <div class="reg-block">
    <ul id="in_login">
    <?php
        $bm_count = Yii::$app->user->identity->bmark;
        $bm_link_text = Yii::t('site', 'BOOKMARK') .' (' .$bm_count .') ';
        $list_count = Yii::$app->user->identity->cnt;
        $list_link_text = Yii::t('site', 'LIST') .' (' .$list_count .') ';

        $link = Html::a($bm_link_text, Url::to(['client/bookmark']), ['id' => 'bm_count_link', 'data-count' => $bm_count]);
        echo Html::tag('li', $link, ['class' => 'zakl']);
        $link = Html::a($list_link_text, Url::to(['client/list']), ['id' => 'list_count_link', 'data-count' => $list_count]);
        echo Html::tag('li', $link, ['class' => 'spis']);
        $link = Html::a(Yii::t('site', 'PROFILE'), Url::to(['user/profile', 'id' => Yii::$app->user->id]));
        echo Html::tag('li', $link, ['class' => 'profil']);
        $link = Html::a(Yii::t('site', 'LOGOUT'), Url::to(['user/logout']));
        echo Html::tag('li', $link, ['class' => 'exit']);
    ?>
    </ul>
    </div>

<?php else : ?>

    <div class="reg-block w1">
        <ul>
            <li class="login-link" id="login">
                <?php echo Html::a('<svg class="login-pic" width="23" height="25">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#login-icon" />
                    </svg>
                    LOGIN', '#'); ?>
<!--                <a href="#">-->
<!--                    <svg class="login-pic" width="23" height="25">-->
<!--                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#login" />-->
<!--                    </svg>-->
<!--                    LOGIN-->
<!--                </a>-->
            </li>
            <li class="login-link hid">
                <div class="error" style="display: block">
                    <?php //Yii::t('user', 'INCORRECT_DATA') ?>
                </div>

                <?= Html::beginForm('', 'post', ['id' => 'loginform', 'onsubmit' => 'return false;']) ?>
                    <fieldset>
                        <input type="text" id="loginform-email" name="LoginForm[email]" placeholder="Email">
                        <input type="password" id="loginform-password" name="LoginForm[password]" placeholder="<?= Yii::t('user', 'LABEL_PASS') ?>" class="noy">
                    </fieldset>

                    <div class="save">
                        <div class="po50">
                            <div class="checkbox">

                                <input type="checkbox" value="0" name="LoginForm[rememberMe]" id="dfd">
                                <label id="dfd_label" for="dfd">
                                    <span class="pseudo-checkbox white"></span>
                                    <span class="label-text"><?= Yii::t('user', 'REMEMBER_ME') ?></span>
                                </label>
                            </div>
                        </div>

                        <div class="po50">
                            <a href="#" id="zabil"><?= Yii::t('user', 'FORGOT_PASS') ?></a>
                        </div>
                    </div>
                <?= Html::endForm(); ?>
            </li>

            <li class="registration-link rega pad">
                <?php
                    if (Yii::$app->controller->route == 'site/start')
                        echo Html::a('<svg class="avatar" width="23" height="25">
                                                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#avatar" />
                                            </svg>'.Yii::t('site', 'REGISTER'), '#', ['class' => 'standart_register_link registra ']);
                    else
                        echo Html::a('<svg class="avatar" width="23" height="25">
                                                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#avatar" />
                                            </svg>'.Yii::t('site', 'REGISTER'), Url::to(['user/register']), ['class' => 'registra '])
                ?>
            </li>
        </ul>
    </div>


    <div class="reg-block w2">
        <ul>
            <li class="login-link">
                <?= Html::beginForm('', 'post', ['id' => 'recoveryform', 'onsubmit' => 'return false;']) ?>
                    <fieldset>
                        <input type="text" name="User[email]" class="bigmail" placeholder="Email">
                        <input id="recoveryform_btnok" type="button" value="SEND"  class="sbn" />
                    </fieldset>
                <?= Html::endForm(); ?>
            </li>
            <li class="registration-link rega">
                <?= Html::a(Yii::t('site', 'REGISTER'), Url::to(['site/start', 'scenario' => 'registration'])) ?>
                <a href="#" class="zabil"  id="avtor"><?= Yii::t('user', 'LOGIN') ?></a>
            </li>
        </ul>
    </div>


    <?php
        if (!Yii::$app->request->isPjax && Yii::$app->controller->route != 'site/start')
            $this->beginJs();
    ?>
    <script>
        // Remember Me
        $("#dfd_label").on("click", function() {
            var new_val = ($("#dfd").val() == "0") ? "1" : "0";
            $("#dfd").val(new_val);
        });

        //открываем форму входа по LOGIN кнопке
        $("#login").click(function(evv) {
            evv.preventDefault();
            var login_mode = ($(".hid").css("display") != "none");

            if (!login_mode) {
                $(".hid").fadeIn().children(".error").hide();
            }
            else if($("#loginform-email").val()=="" || $("#loginform-password").val()=="") {
                $(".hid").fadeOut();
            }
            else {
                $.ajax({
                    url: "<?= Url::to(['user/login']) ?>",
                    type: "post",
                    dataType: "html",
                    data: $("#loginform").serialize(),
                    success: function(response) {
                        if (response == "ok") {
                            window.location.reload(true);
                        } else {
                            var n = noty({
                                text: response,
                                layout: 'top',
                                closeWith: ['click', 'button'],
                                type: 'warning',
                                theme: 'relax',
                            });
                        }
                    }
                });
            }
            //$(".reg-block .registration-link a.zabil").css( "display", "block");
            //$(".reg-block .registration-link.rega.pad").toggleClass("pad");
            //$("#login a").addClass( "act" );
            // $(".registration-link.rega").toggleClass( "sds");
        });

        $("#avtor").click(function(bbb) {
            bbb.preventDefault();
            $(".reg-block.w2").hide();
            $(".reg-block.w1").show();
        });


        //открываем форму забыли пароль
        $("#zabil").click(function(evv) {
            evv.preventDefault();
            $(".reg-block.w2").fadeToggle();
            $(".w1").hide();
        });

        $("#recoveryform_btnok").on("click", function(e) {
            e.preventDefault();
            if ($(this).prev(".bigmail").val() == "")
                return false;

            $.ajax({
                url: "<?= Url::to(['user/forgot-pass']) ?>",
                type: "post",
                dataType: "html",
                data: $("#recoveryform").serialize(),
                success: function(response) {
                    var n = noty({
                        text: response,
                        layout: 'top',
                        closeWith: ['click', 'button'],
                        type: 'warning',
                        theme: 'relax',
                    });
                }
            });
        });
    </script>
    <?php
    if (Yii::$app->controller->route != 'site/start')
        $this->endJs();
    ?>

<?php endif ;   // elseif (!Yii::$app->user->isGuest) ?>
