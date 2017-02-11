<?php
    /**
     * @var $profile UserProfile
     */
    use yii\helpers\Html;

    echo Html::activeHiddenInput($profile, 'country');
?>

<div class="where">
    <!-- Zip -->
    <label class="index"> <?= $profile->getAttributeLabel('country') .' / ' .$profile->getAttributeLabel('zip') ?>*
        <span class="des">
            <span id="min_ind"> <?= Html::encode($profile->country) ?> </span>

            <!-- Zip Countries -->
             <ul class="downbox addpole">
                 <li class="select_country" data-short="DE">
                     <img src="/images/blank.gif" class="flag de fnone">
                     <span class="ddlabel"> <?= Yii::t('user', 'GERMANY') ?> </span>
                 </li>
                 <li class="select_country" data-short="BE">
                     <img src="/images/blank.gif" class="flag be fnone">
                     <span class="ddlabel"> <?= Yii::t('user', 'BELGIUM') ?> </span>
                 </li>
                 <li class="select_country" data-short="FR">
                     <img src="/images/blank.gif" class="flag fr fnone">
                     <span class="ddlabel"> <?= Yii::t('user', 'FRANCE') ?> </span>
                 </li>
                 <li class="select_country" data-short="ND">
                     <img src="/images/blank.gif" class="flag nl fnone">
                     <span class="ddlabel"> <?= Yii::t('user', 'NETHERLANDS') ?> </span>
                 </li>
                 <li class="select_country" data-short="IT">
                     <img src="/images/blank.gif" class="flag it fnone">
                     <span class="ddlabel"> <?= Yii::t('user', 'ITALY') ?> </span>
                 </li>
                 <li class="select_country" data-short="RU">
                     <img src="/images/blank.gif" class="flag ru fnone">
                     <span class="ddlabel"> <?= Yii::t('user', 'RUSSIA') ?> </span>
                 </li>
             </ul>
        </span>

        <?= Html::activeTextInput($profile, 'zip', ['class' => 'indx_inp']) ?>
    </label>


    <!-- Address -->
    <label class="addr"> <?= $profile->getAttributeLabel('address') ?>*
        <?= Html::activeTextInput($profile, 'address') ?>
    </label>
</div>


<?php $this->beginJs(); ?>
    <script>
        // Show Countries panel
        $("body").on( "click", ".where .index .des", function() {
            $(this).find(".downbox").slideDown(400);
            return false;
        });

        // Set Contry name
        $("body").on( "click", "li.select_country", function() {
            var country = $(this).data('short');
            $("#min_ind").text(country);
            $("#userprofile-country").val(country);
            $("#userprofile-zip").focus();
            $(this).parent('ul.downbox').slideUp(100);
            return false;
        });

        // Geocode by Address, store Lat & Lng in hidden fields
        $("#userprofile-address").on("keydown", function(e) {
            if(e.which == 13)
                setMapPositionByAddress();
        });

        // window.geocoder, window.map & window.marker are defined in _profile_map.php
        function setMapPositionByAddress() {
            var address = $.trim($("#userprofile-address").val());

            window.geocoder.geocode( { 'address':address}, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    if ($("#userprofile-lat").val() == results[0].geometry.location.lat() && $("#userprofile-lng").val() == results[0].geometry.location.lng())
                        return true;

                    window.map.setCenter(results[0].geometry.location);
                    window.marker.setOptions({
                        visible: true,
                        title: address,
                        position: results[0].geometry.location
                    });

                    $("#userprofile-lat").val(results[0].geometry.location.lat());
                    $("#userprofile-lng").val(results[0].geometry.location.lng());
                } else {
                    alert("<?= Yii::t('site', 'GEOCODE_ERR') ?>" + status);
                }
            });
        }
    </script>
<?php $this->endJs(); ?>

<?php
    // $("#userprofile-address").on("keydown", function(e)
    // is stored in _profile_map file
    // because we don't have window.geocoder, window.map & window.marker here
