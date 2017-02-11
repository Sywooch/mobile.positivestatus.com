<?php
/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>

QWER qwer qwer qWERQWER

<?php
$this->registerJsFile('http://maps.google.com/maps/api/js');
$this->registerJsFile('/js/adap_m.js', ['depends' => 'yii\web\YiiAsset']);
$this->registerJsFile('/js/start.js', ['depends' => 'yii\web\YiiAsset']);
?>

<?php $this->beginJs(); ?>
<script>

    function initialize() {
        //получаем наш div куда будем карту добавлять
        var mapCanvas = document.getElementById('mpp');
        // задаем параметры карты
        var mapOptions = {
            //Это центр куда спозиционируется наша карта при загрузке
            center: new google.maps.LatLng(49.90, 8.7108),

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
        var markers = [],
            myPlaces = [];
        //Добавляем места в массив
        myPlaces.push(new Place('Waldstraße ', 49.8831, 8.8168, 'MobileMaker'));



        //Теперь добавим маркеры для каждого места
        for (var i = 0, n = myPlaces.length; i < n; i++) {
            var marker = new google.maps.Marker({
                //расположение на карте
                position: new google.maps.LatLng(myPlaces[i].latitude, myPlaces[i].longitude),
                map: map,
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
        var markers = [],
            myPlaces = [];
        //Добавляем места в массив
        myPlaces.push(new Place('Waldstraße ', 49.8431, 8.9111, 'MobileMaker'));

        var image = 'images/baloon.png';

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

    $(document).ready(function(e) {
        $("#countries").msDropdown(); //image can have css class; Please check source code.


    });

</script>
<?php $this->endJs(); ?>
