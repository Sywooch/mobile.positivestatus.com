<?php
    // Parameters:
    // $is_owner    - whether to show edit panel (edit, pause, delete links) or not
    use app\components\Y;
    use app\models\Trans;
    use app\models\TransBrand;
    use app\models\TransFeatureH;
    use yii\helpers\Html;
    use yii\helpers\Url;

    $hide_user_contact = (Yii::$app->controller->route == 'client/list');

    
    $mainPhotoUrl = Trans::getMainPhotoUrl($model->id, 'sm_');
    if (empty($mainPhotoUrl))
        $mainPhotoUrl = Y::$no_trans_photo;

    $detailsUrl = Url::to(['/site/details', 'trans_id' => $model->id]); // for Site Controller only
    $showBookmarkLink = !Yii::$app->user->isGuest && (Yii::$app->user->id != $model->user_id)
        && !$is_owner && empty($model->bookmarkExists);
    $showDeleteBookmarkLink = Yii::$app->controller->id=='client' && Yii::$app->controller->action->id=='bookmark';
    $showDetails = (Yii::$app->controller->id=='site') || 
        (Yii::$app->controller->id=='client' && Yii::$app->controller->action->id=='bookmark');
  
//    $width = Yii::$app->params['mainPhotoSmall']['width'];
//    $height = Yii::$app->params['mainPhotoSmall']['height'];
    $curr = $model->getCurr();

    $data_photos = '';
    $photos = Trans::getPhotoUrls($model->id, 'sm_');
    if (!empty($photos)) {
        foreach ($photos as $photo) {
            $a = explode('.', basename($photo));
            $data_photos .= $a[0] .',';
        }

        $data_photos = trim($data_photos, ',');
    }
?>


    <div class="item" data-trans_id="<?= $model->id ?>" data-user_id="<?= $model->user_id ?>">
        <div class="images" style="position:relative;" data-photos="<?= $data_photos ?>">
            <span class="prev"></span>
            <span class="next"></span>

            <?php
                // Main Photo
                if (!empty($mainPhotoUrl)) {
                    echo Html::img($mainPhotoUrl, ['class' => 'real']);
                    //echo $showDetails ? Html::a($img, $detailsUrl, ['data-pjax' => '0']) : $img;
                }
            ?>

            <!-- Paused text and Resume button -->
            <?php if ($model->pause > time()) : ?>
                <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;  padding:60px 0; text-align: center; color: #00E; opacity: 0.7; font-size: 20px; background-color: #CCC">
                    <?php $dop_text = ($model->pause - time()) > 365*60*60*24 ? Yii::t('client', 'PAUSE_FOREVER') : Yii::t('site', 'TO') .' ' .Yii::$app->formatter->asDate($model->pause, 'short'); ?>
                    <?= Yii::t('client', 'PAUSED_TEXT') .'<br />' .$dop_text ?>
                    <br /><br />
                    <span style="border:2px solid #00E; cursor: pointer; padding:2px 10px" class="show_resume" data-param="reset_to_zero"> <?= Yii::t('site', 'RESUME') ?> </span>
                </div>
            <?php endif; ?>
        </div>

        <div class="info">
            <?php if ($showDeleteBookmarkLink) : ?>
                <a href="#" class="delete_bookmark" style="position: absolute; right: 2px; top: -12px">
                    <img src="/img/delete_icon.png" style="height: 25px; width: 25px" />
                </a>
            <?php endif; ?>



            <h2><?= $showDetails ? Html::a($model->fullName, $detailsUrl, ['data-pjax' => 0]) : $model->fullName; ?></h2>

            <div class="cost">
                <b><?= number_format($model->price_brut, 0, '.', ' ') ?></b>
                <span class="euro"> <?= $curr ?> </span>
                <span class="nds"> <?= ($model->nds==0) ? Yii::t('client', 'WITHOUT_NDS') : Yii::t('client', 'NDS') .' ' .$model->nds .'%' ?> </span>
            </div>

            <?php
            // Labels and Descriptions (not empty values only)
            $arr = Trans::getDescriptionsGroup($model);
            //                var_dump($arrs);
            foreach ($arr as $ar){
                if(is_array($ar)){
                    $labels = array_keys($ar);
                    $descs = array_values($ar);
                    $lines = count($labels);
                    echo '<div class="info-line">';
                    for ($n = 0; $n < $lines; $n++) {
                        echo '<span> ' . $descs[$n] . ' </span>';
                    }
                    echo '</div>';
                }else {
                    echo '<div class="info-line"><span> ' . $ar . ' </span></div>';

                }
            }
            //                    $labels = array_keys($arr);
            //                    $descs = array_values($arr);
            //                    $lines = count($labels);
            //                    // <div class="info-line"><i>Год выпуска:</i><span>2007</span></div>
            //                    for ($n = 0; $n < $lines; $n++) {
            //                        echo '<div class="info-line"><span> ' . $descs[$n] . ' </span></div>';
            //                    }

            ?>



            <?php if ($is_owner) : ?>
            <div class="edits">
                <span class="reda" data-href="<?= Url::to(['/client/edit-proposal', 'trans_id' => $model->id]) ?>"><i></i> <?= Yii::t('site', 'EDIT_SHORT') ?> </span>
                <span class="pause" data-window_class=".paus"><i></i> <?= Yii::t('client', 'PAUSE') ?> </span>
                <span class="delet" data-window_class=".dele"><i></i> <?= Yii::t('site', 'DELETE') ?> </span>
                <span style="color:#E00" title="Не было в начальном задании. Нужны объяснения как оно должно работать" class="rekls" data-window_class=".rekla"><i></i> <?= Yii::t('site', 'ADVERT') ?> </span>

                <?php       // DELETE      ?>
                <div class="windos dele">
                    <div class="shaf_bg">
                        <div class="clos"></div>
                        <p class="ttitl"> <?= Yii::t('client', 'DELETE1') ?> </p>
                        <p class="tex"> <?= Yii::t('client', 'DELETE2') ?> </p>
                        <div class="udal delete_button" id="udal"> <?= Yii::t('site', 'DELETE') ?> </div>
                    </div>
                </div>


                <?php       // PAUSE      ?>
                <div class="windos paus">
                    <div class="shaf_bg">
                        <div class="clos"></div>
                        <p class="ttitl"> <?= Yii::t('client', 'PAUSE1') ?> </p>
                        <p class="tex"> <?= Yii::t('client', 'PAUSE2') ?> </p>

                        <ul class="nav2 publ">
                            <li class="static">
                                <a href="#"> <?= Yii::t('client', 'PAUSE_FOREVER') ?> </a>
                                <ul>
                                    <li><a href="#" class="pause_link" data-param="+10 years"> <?= Yii::t('client', 'PAUSE_FOREVER') ?> </a></li>
                                    <li><a href="#" class="pause_link" data-param="+1 day"> <?= Yii::t('client', 'PAUSE_FORDAY') ?> </a></li>
                                    <li><a href="#" class="pause_link" data-param="+7 day"> <?= Yii::t('client', 'PAUSE_FOR7DAYS') ?> </a></li>
                                    <li><a href="#" class="pause_link" data-param="+14 day"> <?= Yii::t('client', 'PAUSE_FOR14DAYS') ?> </a></li>
                                    <li><a href="#" class="pause_link" data-param="+1 month"> <?= Yii::t('client', 'PAUSE_FORMONTH') ?> </a></li>
                                </ul>
                            </li>
                        </ul>
                        <div class="udal pause_button" data-param="+10 years"> <?= Yii::t('client', 'STOP') ?> </div>
                    </div>
                </div>


                <?php       // ADVERT      ?>
                <div class="windos rekla">
                <div class="shaf_bg">
                    <div class="clos"></div>
                    <p class="ttitl"> <?= Yii::t('site', 'ADVERT') ?> </p>

                    <ul class="nav2 rekk">
                        <?php       // CATEGORIES      ?>
                        <li class="static">
                            <a href="#"> <?= Yii::t('client', 'INALL_CATEGORIES') ?> </a>
                            <?= TransFeatureH::getFeatureSrc($model->cat_id, 'category_id') ?>
                        </li>

                        <?php       // BRANDS      ?>
                        <li class="static">
                            <a href="#"> <?= Yii::t('site', 'BRANDS') ?> </a>
                            <ul class="brand_list">
                                <?= TransBrand::getDropdownHtml($model->cat_id) ?>
                            </ul>
                        </li>

                        <?php       // MODELS      ?>
                        <li class="static">
                            <a href="#"> <?= Yii::t('site', 'MODELS') ?> </a>
                            <ul class="model_list"></ul>
                        </li>

                        <?php       // CLICKS      ?>
                        <li class="static">
                            <a href="#">1000 <?= Yii::t('client', 'CLICKS_FOR') ?> 50 euro</a>
                            <ul class="testo">
                                <li>
                                    <a href="#">1000 <?= Yii::t('client', 'CLICKS_FOR') ?> 50 euro</a>
                                </li>
                                <li>
                                    <a href="#">2000 <?= Yii::t('client', 'CLICKS_FOR') ?> 90 euro</a>
                                </li>
                                <li>
                                    <a href="#">3000 <?= Yii::t('client', 'CLICKS_FOR') ?> 130 euro</a>
                                </li>
                            </ul>
                        </li>
                    </ul>

                </div>
                </div>
            </div> <!--class="edits -->
            <?php endif; ?>

        </div><!-- class="info">-->

<?php if (!$hide_user_contact) : ?>
        <div class="man-right">
            <div class="manager">
                <a href="#" title="" class="manager__piclink">
                    <img class="manager__picture" src="/images/elena.jpg" width="80" height="80" alt="">
                </a>
                <p class="manager__name">Elena Brjuchovic</p>
                <span class="manager__languages">Eng, Deu, Rus, Esp</span>
                <p class="manager__firm">MobileMakler</p>
                <p class="manager__contacts">
                    <a class="manager__link-cont" href="tel:+4906071392273">
                        +49 (0) 607 139 22 73 <i class="manager__viber"></i> <i class="manager__what"></i>
                    </a>
                    </p>
                    <p class="manager__contacts">
                        <a class="manager__link-cont" href="tel:+4906071392273">
                            +49 (0) 607 139 22 73
                        </a>
                    </p>
                    <p class="manager__contacts">
                        <a class="manager__link-cont" href="skype:MobileMakler.com?call">
                          
                            MobileMakler.com
                        </a>
                    </p>
                   <!-- <p class="manager__contacts manager__contacts_mail">
                        <a class="manager__link-cont manager__link-cont_mail" href="mailto:MobileMakler@gmail.com">
                        <svg class="manager__skype-pic" width="16" height="16">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#envelope"></use>
                        </svg>
                        Отправить сообщение
                        </a>
                    </p>-->
            </div> <!-- class="manager"> -->
        </div>
           <div class="actions">
                    <a href="#" class="actions__item">
                        <svg class="manager__skype-pic" width="16" height="16">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#placeholder" />
                        </svg>
                        <?php if (Yii::$app->controller->route != 'client/list') : ?>
                            <?= $model->user->profile->getFullAddress() ?>
                        <?php endif; ?>
                    </a>
                     <?php if ($showBookmarkLink) : ?>
                    <a href="#" class="actions__item actions__item_bookmarks">
                        <svg class="manager__skype-pic" width="16" height="16">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#bookmark-outline" />
                        </svg>
                        В закладки
                    </a>
                     <a class="manager__link-cont manager__link-cont_mail" href="mailto:MobileMakler@gmail.com">
                        <svg class="manager__skype-pic" width="16" height="16">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#envelope"></use>
                        </svg>
                        Отправить сообщение
                     </a>
                    <?php endif; ?>


                </div>

<?php endif; ?>

    </div> <!--class="item"-->


<?php if (!Yii::$app->request->isPjax && $is_owner) : ?>
<?php $this->beginJs(); ?>
<script>
    // Go to Edit Page
    $("body").on( "click", "div.edits span.reda", function(e) {
        var href = $(this).data("href");
        window.location.assign(href);
    });

    // Show window
    $("body").on( "click", "div.edits .pause, div.edits .delet, div.edits .rekls", function(e) {
        var window_class = $(this).data("window_class");
        $(this).parent().find(window_class).addClass("open");
    });

    // Close window
    $("body").on( "click", "div.edits .clos", function(e) {
        $(this).parents(".windos").removeClass("open")
    });

    // Select interval for pause
    $("body").on( "click", "a.pause_link", function(e) {
        e.preventDefault();
        $(this).closest("ul.nav2").next("div.udal").attr("data-param", $(this).attr("data-param"));
    });


    // Set Trans Pause OR Resume Showing
    $("body").on( "click", "div.pause_button, span.show_resume", function() {
        var mess = $(this).hasClass("show_resume") ? "<?= Yii::t('client', 'RESUME_SHOWING') ?>" : "<?= Yii::t('client', 'STOPPED') ?>";
        if (!confirm(mess))
            return false;

        var trans_id = $(this).closest("div.item").attr("data-trans_id");
        var this_param = $(this).attr("data-param");
        var window = $(this).hasClass("show_resume") ? null : $(this).closest(".windos");

        $.ajax({
            url: "<?= Url::to(['/auxx/set-trans-pause']) ?>",
            type: "post",
            dataType: "html",
            data: {trans_id: trans_id, param: this_param},
            error: function(a,b,c) {alert(b)},
            success: function(response) {
                if (response == "ok") {
                    if (window != null)
                        window.removeClass("open");

                    $.pjax.reload("#listview_pjax");
                }
                else {
                    alert(response);
                }
            }
        });
    });


    // Delete proposal
    $("body").on( "click", "div.delete_button", function() {
        var trans_id = $(this).closest("div.item").attr("data-trans_id");
        var window = $(this).closest(".windos");

        $.ajax({
            url: "<?= Url::to(['/auxx/delete-model-by-id']) ?>",
            type: "post",
            dataType: "html",
            data: {model_name: "Trans", model_id: trans_id},
            error: function(a,b,c) {alert(b)},
            success: function(response) {
                if (response != "ok") {
                    alert(response);
                }
                else {
                    refreshTopLink($("#list_count_link"), -1);        // Function is in main.js
                    $.pjax.reload("#listview_pjax");
                }
            }
        });
    });


    /////////////////////////////////////////////////////////////////////////
    //              ADVERT
    // Update Model-list on Selecting Brand
    $("body").on( "click", "ul.brand_list li a", function(e) {
        e.preventDefault();
        var brand_id = $(this).data("brand_id");
        var model_list_cnt = $(this).closest("li.static").next("li.static");
        var model_list = model_list_cnt.children("ul.model_list");

        $.ajax({
            url: "<?= Url::to(['/auxx/get-model-dropdown-html']) ?>",
            type: "post",
            dataType: "json",
            data: {brand_id: brand_id},
            error: function(a,b,c) {alert(b)},
            success: function(data) {
                if (data.result != "ok")
                    alert(data.message);
                else
                    model_list.html(data.message);

                var display_style = (data.message == "") ? "none" : "block";
                model_list_cnt.css("display", display_style);
            }
        });
    });
</script>
<?php $this->endJs(); ?>
<?php endif; ?>



<?php if (!Yii::$app->request->isPjax && $showBookmarkLink) : ?>
    <?php $this->beginJs(); ?>
    <script>
        //              ADD  BOOKMARK
        $("body").on("click", "a.actions__item_bookmarks", function(e) {
            e.preventDefault();
            var cnt = $(this).closest("div.item");
            if (!confirm("<?= Yii::t('site', 'ADD_TOBOOKMARK') ?> ?"))
                return false;

            var params = {
                link: $(this),
                url: "<?= Url::to(['/auxx/update-model']) ?>",
                user_id: "<?= Yii::$app->user->id ?>",
                trans_id: cnt.attr("data-trans_id"),
                message: "<?= Yii::t('site', 'BOOKMARK_CREATED') ?>"
            };

            addBookmark(params);        // Function is declared in main.js
        });
    </script>
    <?php $this->endJs(); ?>
<?php endif; ?>



<?php if (!Yii::$app->request->isPjax && $showDeleteBookmarkLink) : ?>
    <?php $this->beginJs(); ?>
    <script>
        //              DELETE  BOOKMARK
        $("body").on("click", "a.delete_bookmark", function(e) {
            e.preventDefault();
            var cnt = $(this).closest("div.item");
            if (!confirm("<?= Yii::t('site', 'DELETE_BOOKMARK') ?> ?"))
                return false;

            var params = {
                url: "<?= Url::to(['/auxx/delete-bookmark']) ?>",
                user_id: "<?= Yii::$app->user->id ?>",
                trans_id: cnt.attr("data-trans_id"),
                pjax_selector: "#listview_pjax"
            };

            deleteBookmark(params);     // Function is declared in main.js
        });
    </script>
    <?php $this->endJs(); ?>
<?php endif; ?>



<?php
    if (!Yii::$app->request->isPjax)
        $this->registerJsFile('/js/nslide.js', ['depends' => 'yii\web\JqueryAsset']);
