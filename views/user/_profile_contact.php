<?php
    /**
     * @var $contact UserContact
     * @var $form Activeform
     * @var $delete_contact bool - value for drawing "Delete Contact" link
     */
    use app\components\Y;
    use app\models\UserContact;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\widgets\ActiveForm;

    if (!isset($delete_contact))
        $delete_contact = true;

    $isAjax = Yii::$app->request->isAjax;
    if ($isAjax) {
        $form = ActiveForm::begin(['action' => '#']);
        $contact = $model;
    }

    $lang_str = '';
    foreach ($contact['langs'] as $lang)
        $lang_str .= ' &nbsp;' .$lang;

    $lang_counter = $cell_counter = 1;
    $contact_id = $contact->id;
?>


<div class="imt_kont" data-contact_id="<?= $contact->id ?>">
    <label class="plus la_lang"> <?= $contact->getAttributeLabel('name') ?> *
        <b class="nom"></b>
        <i class="wrp">
            <!-- Language line -->
            <span>
                <a href="#" class="add drop langu"><?= $contact->getAttributeLabel('langs') ?></a>
            </span>
            <b>
                <span class="lang_line">
                    <?= $lang_str ?>
                </span>

                <!-- Language checkboxes -->
                <ul class="downbox">
                    <?php foreach (UserContact::getLangList() as $short => $full) : ?>
                        <?php $cid = 'langcheck_' .$contact->id .'_' .$lang_counter++; ?>
                        <?php $checked = in_array($short, $contact->langs); ?>

                        <li>
                            <div class="checkbox">
                                <?= Html::activeCheckbox($contact, "[$contact_id]langs[]", ['id' => $cid, 'class' => 'add', 'value' => $short, 'checked ' => $checked, 'uncheck' => null, 'label' => null]) ?>
                                <label for="<?= $cid ?>" data-short="<?= $short ?>">
                                    <?= $full ?>
                                    <span class="pseudo-checkbox white"></span>
                                </label>
                            </div>
                        </li>

                    <?php endforeach; ?>
                </ul>
            </b>
        </i>

        <!-- Contact name -->
        <?= Html::activeTextInput($contact, "[$contact_id]name", ['class' => 'orig']) ?>
    </label>


    <!-- Avatar -->
    <div class="logo_prof">
        <?php
            if ($delete_contact)
                echo '<a class="delete_contact_link delet">' . Yii::t('site', 'DELETE') .'</a>';

            $avatar_url = Y::getAvatarUrl() .Y::getAvatarFile($contact->id) .'?rand=' .uniqid();
            $avatar_img = Html::img($avatar_url, ['id' => 'avatar_img_'.$contact_id, 'style' => 'width: 100%; height: 100%']);
            echo Html::a($avatar_img, '#');
        ?>

        <script>
            new AjaxUpload('#avatar_img_<?= $contact_id; ?>', {
                action: '<?= Url::to('/auxx/change-avatar'); ?>',
                name: 'userfile',
                responseType: 'json',
                data: {
                    contact_id: '<?= $contact_id; ?>',
            <?= Yii::$app->request->csrfParam; ?>: "<?= Yii::$app->request->csrfToken; ?>"
            },
            onSubmit : function(file,ext){
                if (! (ext && /^(jpg|png|jpeg|gif)$/i.test(ext))){
                    alert("<?= Yii::t('site', 'IMAGE_EXTESIONS'); ?>");
                    return false;   // cancel upload
                }
            },
            onComplete: function(file, response) {
                if (response.result == "ok")
                    $("#avatar_img_<?= $contact_id; ?>").attr("src", response.avatar_url);
                else
                    alert(response.message);
            }
            });
        </script>
    </div>



    <fieldset>
	    <?php
            if (empty($contact->phones))
                $contact->phones[] = '';
            if (empty($contact->cells))
                $contact->cells[] = '';
        ?>
		
        <!-- Phones -->
        <?php foreach ($contact->phones as $phone) : ?>
            <label class="phons"> <?= $contact->getAttributeLabel('phones') ?>
                <?= Html::activeTextInput($contact, "[$contact_id]phones[]", ['class' => 'orig', 'value' => $phone]) ?>
            </label>
        <?php endforeach; ?>

        <!-- Cells, Vibers, Whatsapps -->
        <?php foreach ($contact->cells as $c => $cell) : ?>
            <?php $vb_cid = 'vibercheck_' .$contact->id .'_' .$cell_counter; ?>
            <?php $wapp_cid = 'wappcheck_' .$contact->id .'_' .$cell_counter++; ?>

            <label class="mobil"> <?= $contact->getAttributeLabel('cells') ?>
                <span class="right">
                     <div class="checkbox">
                         <?php $checked = (isset($contact->vibers[$c]) && $contact->vibers[$c]=='1') ? '1' : '0'; ?>
                         <?= Html::activeCheckbox($contact, "[$contact_id]vibers[]", ['id' => $vb_cid, 'value' => $checked, 'label' => null]) ?>

                         <label for="<?= $vb_cid ?>">
                             <span class="pseudo-checkbox white"></span>
                         </label>
                     </div>
                     <i class="viber"></i>

                    <div class="checkbox ch2">
                        <?php $checked = (isset($contact->whatsapps[$c]) && $contact->whatsapps[$c]=='1') ? '1' : '0'; ?>
                        <?= Html::activeCheckbox($contact, "[$contact_id]whatsapps[]", ['id' => $wapp_cid, 'value' => $checked, 'label' => null]) ?>

                        <label for="<?= $wapp_cid ?>">
                            <span class="pseudo-checkbox white"></span>
                        </label>
                    </div>
                    <i class="what"></i>
                </span>

                <?= Html::activeTextInput($contact, "[$contact_id]cells[]", ['class' => 'orig', 'value' => $cell]) ?>
            </label>
        <?php endforeach; ?>


        <!--label class="sky">
            Skype<b></b>
            <input value="" name="sk2" id="sky7655" type="text">
        </label>
        <label class="BlackBerry">
            BlackBerry pin<b></b>
            <input value="" name="bb2" id="BlackBerry8927" type="text">
        </label>
        <label class="emailm">Email<b></b><input value="" name="em22" id="emailm1055" type="text"></label-->


        <!-- Skypes -->
        <?php foreach ($contact->skypes as $skype) : ?>
            <label class="sky"> <?= $contact->getAttributeLabel('skypes') ?> <a href="#" class="delete_contact_item" title="<?= Yii::t('site', 'DELETE') ?>"> x </a>
                <?= Html::activeTextInput($contact, "[$contact_id]skypes[]", ['value' => $skype]) ?>
            </label>
        <?php endforeach; ?>


        <!-- Berries -->
        <?php foreach ($contact->berries as $berry) : ?>
            <label class="BlackBerry"> <?= $contact->getAttributeLabel('berries') ?> <a href="#" class="delete_contact_item" title="<?= Yii::t('site', 'DELETE') ?>"> x </a>
                <?= Html::activeTextInput($contact, "[$contact_id]berries[]", ['value' => $berry]) ?>
            </label>
        <?php endforeach; ?>


        <!-- Emails -->
        <?php foreach ($contact->emails as $email) : ?>
            <label class="emailm"> <?= $contact->getAttributeLabel('emails') ?> <a href="#" class="delete_contact_item" title="<?= Yii::t('site', 'DELETE') ?>"> x </a>
                <?= Html::activeTextInput($contact, "[$contact_id]emails[]", ['value' => $email]) ?>
            </label>
        <?php endforeach; ?>
    </fieldset>

    <!-- Add Contact, Add Connection -->
    <div class="plus">
        <span><a href="#" class="add kont_clon">+ <?= Yii::t('user', 'CONTACT') ?></a></span>

        <span class="wrp">
            <a id="add_contact_link" href="#" class="add drop langu">+ <?= Yii::t('user', 'CONNECTION') ?>
                <ul class="downbox addpole">
                    <li class="add_phone"> <?= $contact->getAttributeLabel('phones') ?> </li>
                    <li class="add_cell"> <?= $contact->getAttributeLabel('cells') ?> </li >
                    <li class="add_skype"> <?= $contact->getAttributeLabel('skypes') ?> </li>
                    <li class="add_berry"> <?= $contact->getAttributeLabel('berries') ?> </li>
                    <?php /* <li class="add_email"> <?= $contact->getAttributeLabel('emails') ?> </li> */ ?>
                </ul>
            </a>
        </span>
    </div>
</div>  <!--class="imt_kont"> -->


<?php
    if ($isAjax)
        ActiveForm::end();

    $urlAddContact = Url::toRoute(['/auxx/update-model', 'view' => '//user/_profile_contact']);
?>


<?php if (!Yii::$app->request->isPjax) : ?>
<?php $this->beginCss(); ?>
<style>
    .delete_contact_item {
        color: #698cb7; font-size: 15px; font-weight: bold; text-decoration: none;
        margin-left: 6px; vertical-align: super;
    }
</style>
<?php $this->endCss(); ?>
<?php endif; ?>


<?php $this->beginJs(); ?>
<script>
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //  VIBERS  &  WHATSAPPS
    $("[id^=vibercheck], [id^=wappcheck]").removeAttr('name').each(function(ndx, el) {
        var checked = ($(el).val() == '1');
        $(el).prop('checked', checked).prev('[type=hidden]').val($(el).val());
    });
    $("[id^=vibercheck], [id^=wappcheck]").on('change', function() {
        var val = $(this).prop('checked') ? '1' : '0';
        $(this).prev('[type=hidden]').val(val);
    });


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //  CHECK / UCNHECK  LANGUAGE
    $("body").on( "click", ".downbox .checkbox label", function(textclon) {
        var checked = $(this).prev().prop('checked');
        var lang = " &nbsp;" + $(this).data('short');
        var lang_line = $(this).closest('b').children('.lang_line');

        if (checked) {
            var langs = lang_line.html().replace(lang, "");
            lang_line.html(langs);
        }
        else {
            lang_line.append(lang);
        }
    });


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //  ADD NEW CONTACT (click +Contact link)
    $("a.kont_clon").on("click", function() {
        if (!confirm("<?= Yii::t('user', 'ADD_CONTACT') ?> ?"))
            return false;

        var cnt = $(this).closest("div.imt_kont");

        $.ajax({
            url: "<?= $urlAddContact ?>",
            type: "post",
            data: {model_name: "UserContact", "UserContact[user_id]": "<?= $contact->user_id ?>"},
            dataType: "json",
            error: function(jqXHR, textStatus, errorThrown) {
                alert(textStatus);
            },
            success: function(data) {
                if (data.result=="ok") {
                    var html = data.html;
                    var pos1 = html.indexOf("<input");   // cut <form> tag from the beginning
                    if (pos1 > -1)      html = html.slice(pos1);
                    var pos2 = html.indexOf("</form>");     // cut </form> tag from the end
                    if (pos2 > -1)      html = html.slice(0, pos2);

                    $(html).insertAfter(cnt);
                } else
                    alert(data.result);
            }
        });
    });

    //  DELETE  CONTACT
    $(document).on("click", "a.delete_contact_link", function (e) {
        e.preventDefault();
        var cnt = $(this).closest("div.imt_kont");
        var contact_id = cnt.attr("data-contact_id");

        if (confirm("<?= Yii::t('user', 'DELETE_CONFIRM') ?> ?") == false)
            return false;

        $.ajax({
            url: "<?= Url::to('/auxx/delete-model-by-id') ?>",
            type: "post",
            dataType: "html",
            data: {
                model_name: "UserContact",
                model_id: contact_id
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert(textStatus);
            },
            success: function(data) {
                if (data=="ok")
                    cnt.remove();
                else
                    alert(data);
            }
        });
    });


    //  DELETE  CONTACT  ITEM  (Skype, Berry, Email)
    $("body").on("click", "a.delete_contact_item", function(e) {
        e.preventDefault();
        $(this).next("input").val("").closest("label").hide();
    });


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //  ADD CONNECTIONS LINK (add phone, cell, skype, berry, email)
    function RandomString(length) {
        var str = '';
        for ( ; str.length < length; str += Math.random().toString(36).substr(2) );
        return str.substr(0, length);
    };

//    $("#add_contact_link").click(function(e) {
//        e.preventDefault();
//        $(this).children("ul.downbox").slideDown(400);
//    });

    $("body").on( "click", "li.add_phone", function(e) {
        e.preventDefault();
        $(this).parent('ul.downbox').slideUp(100);

        var random = RandomString(8);
        var clone = $(this).closest('.imt_kont').find('label.phons').first().clone();
        $(this).closest('.imt_kont').find('fieldset').append(clone).children('label:last').find('[id]').each(function(ndx, el) {
            $(el).val("").attr('id', $(el).attr('id') +'_' +random);
        });
    });

    $("body").on( "click", "li.add_cell", function(e) {
        e.preventDefault();
        $(this).parent('ul.downbox').slideUp(100);

        var random = RandomString(8);
        var clone = $(this).closest('.imt_kont').find('label.mobil').first().clone();
        $(this).closest('.imt_kont').find('fieldset').append(clone).children('label:last').find('[id], [for]').each(function(ndx, el) {
            $(el).val("");
            if ($(el).attr('id') !== undefined)     $(el).attr('id', $(el).attr('id') +'_' +random);
            if ($(el).attr('for') !== undefined)    $(el).attr('for', $(el).attr('for') +'_' +random);
        });
    });

    var delete_link = $(' <a href="#" class="delete_contact_item" title="<?= Yii::t('site', 'DELETE') ?>"> x </a>');

    $("body").on( "click", "li.add_skype", function(e) {
        e.preventDefault();
        $(this).parent('ul.downbox').slideUp(100);

        var contact_id =  $(this).closest(".imt_kont").data('contact_id');
        var input = $('<input type="text" value="">').attr('name', 'UserContact[' +contact_id +'][skypes][]');
        var clone = $('<label class="sky">Skype</label>').append(delete_link).append(input);
        $(this).closest(".imt_kont").find("fieldset").append(clone);
    });

    $("body").on( "click", "li.add_berry", function(e) {
        e.preventDefault();
        $(this).parent('ul.downbox').slideUp(100);

        var contact_id =  $(this).closest(".imt_kont").data('contact_id');
        var input = $('<input type="text" value="">').attr('name', 'UserContact[' +contact_id +'][berries][]');
        var clone = $('<label class="BlackBerry">BlackBerry pin</label>').append(delete_link).append(input);
        $(this).closest(".imt_kont").find("fieldset").append(clone);
    });

    $("body").on( "click", "li.add_email", function(e) {
        e.preventDefault();
        $(this).parent('ul.downbox').slideUp(100);

        var contact_id =  $(this).closest(".imt_kont").data('contact_id');
        var input = $('<input type="text" value="">').attr('name', 'UserContact[' +contact_id +'][emails][]');
        var clone = $('<label class="emailm">Email</label>').append(delete_link).append(input);;
        $(this).closest(".imt_kont").find("fieldset").append(clone);
    });

</script>
<?php $this->endJs(); ?>


<?php
    $this->registerJsFile('/js/ajaxupload.js', ['position' => yii\web\View::POS_BEGIN]);