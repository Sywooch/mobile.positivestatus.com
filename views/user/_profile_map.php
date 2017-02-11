<?php
    /**
     * @var $profile UserProfile
     */
    use yii\helpers\Html;

    echo "\n";
    echo Html::activeHiddenInput($profile, 'lat') ."\n";
    echo Html::activeHiddenInput($profile, 'lng') ."\n";
    echo Html::activeHiddenInput($profile, 'map_zoom') ."\n";
?>


<div id="map_pro" class="mpof"></div>


<?php $this->beginJs(); ?>
<script>
    var map;
    var marker;
    var geocoder;
    var ini_latlng = new google.maps.LatLng(<?= $profile->lat ?>, <?= $profile->lng ?>);

    window.geocoder = new google.maps.Geocoder();
    window.map = new google.maps.Map(document.getElementById("map_pro"),
        {
            zoom: <?= $profile->map_zoom ?>,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            center: ini_latlng,
            //scrollwheel: false
        }
    );

    window.marker = new google.maps.Marker({
        map: window.map,
        position: ini_latlng,
        visible: true
    });

    window.map.addListener('zoom_changed', function() {
        $("#userprofile-map_zoom").val(window.map.getZoom());
    });
</script>
<?php $this->endJs(); ?>


<?php
    $this->registerJsFile('http://maps.google.com/maps/api/js?sensor=false');
    $this->registerJsFile('http://google-maps-utility-library-v3.googlecode.com/svn/trunk/infobox/src/infobox.js');
