<?php
/**
 * @var $trans_models ActiveQuery Trans::find()
 * @var $transcat_model ActiveRecord TransCat::findOne(['get_param' => $trans_cat])
 * @var $filter_model ActiveRecord new Trans(['scenario' => 'filter']);
 */
    use app\components\AdvertWidget;
    use yii\helpers\Url;
?>

<div class="fix_map_new" style="visibility: visible; opacity: 1;">
    <aside class="itm_cars side_rec">
        <?= AdvertWidget::widget(['filter_model' => $filter_model]) ?>
    </aside>


    <aside class="map_find">
        <div class="container">
            <div id="closer_map"></div>
            <div class="show_filt"></div>

            <?php // Sliders & Dropdown Filters ?>
            <?= $this->render('_proposal_slider', ['transcat_model' => $transcat_model, 'filter_model' => $filter_model]) ?>
        </div>

        <div style="position: relative; height: 100%">
            <div class="clos_map_mob" id="mapcloser"></div>
            <div id="all_car_map"></div>
        </div>
    </aside>

</div>


<?php if (!Yii::$app->request->isPjax) : ?>
<?php $this->beginJs(); ?>
<script>
    var myCenter=new google.maps.LatLng(49.89, 8.8208);

    function initialize() {
        //получаем наш div куда будем карту добавлять
        var mapCanvas = document.getElementById('all_car_map');

        // задаем параметры карты
        var mapOptions = {
            //Это центр куда спозиционируется наша карта при загрузке
            center: myCenter,

            //увеличение под которым будет карта, от 0 до 18
            // 0 - минимальное увеличение - карта мира
            // 18 - максимально детальный масштаб
            zoom: 13,
            //Тип карты - обычная дорожная карта
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        //Инициализируем карту
        var map = new google.maps.Map(mapCanvas, mapOptions);

        //Объявляем массив с нашими местами и маркерами
        var markers = [], myPlaces = [];
        //Добавляем места в массив
        myPlaces.push(new Place('Waldstraße ', 49.8431, 8.9111, 'MobileMaker'));

        var image = '/images/baloon.png';

        //Теперь добавим маркеры для каждого места
        for (var i = 0, n = myPlaces.length; i < n; i++) {
            var marker = new google.maps.Marker({
                //расположение на карте
                position: new google.maps.LatLng(myPlaces[i].latitude, myPlaces[i].longitude),
                map: map,
                icon: image,
                //То что мы увидим при наведении мышкой на маркер
                title: myPlaces[i].name
            });

            //Добавим попап, который будет появляться при клике на маркер
            var infowindow = new google.maps.InfoWindow({
                content: '<h1>' + myPlaces[i].name + '</h1><br/>' + myPlaces[i].description
            });

            //привязываем попап к маркеру на карте
            makeInfoWindowEvent(map, infowindow, marker);

            markers.push(marker);

            // map.panTo(new google.maps.LatLng(49.4431, 8.2111));
            // google.maps.event.addListener(map, 'resize', function(){
            //map.setCenter( marker.getPosition() );
//});
        }
    }

    function makeInfoWindowEvent(map, infowindow, marker) {
        //Привязываем событие КЛИК к маркеру
        google.maps.event.addListener(marker, 'click', function() {
            infowindow.open(map, marker);
        });
    }

    //Это класс для удобного манипулирования местами
    function Place(name, latitude, longitude, description){
        this.name = name;  // название
        this.latitude = latitude;  // широта
        this.longitude = longitude;  // долгота
        this.description = description;  // описание места
    }

    //Когда документ загружен полностью - запускаем инициализацию карты.
    google.maps.event.addDomListener(window, 'load', initialize);

    // Bock to Proposals. #filter_form is in _proposal_slider.php
    $("#closer_map").on("click", function() {
        $("#filter_form").attr("action", "<?= Url::current(['map' => false]) ?>").submit();
    });
</script>
<?php $this->endJs(); ?>
<?php endif; ?>

<?php $this->registerJsFile('http://maps.google.com/maps/api/js'); ?>
