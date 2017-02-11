<?php
use app\models\TransCat;
use yii\helpers\Html;
    use yii\helpers\Url;
    
    $curr_lang = \Yii::$app->params['app_langs'][\Yii::$app->language];
    $this_roue = (Yii::$app->controller->route == 'site/proposal');
    $trans_cat = Yii::$app->request->get('trans_cat');

    $car_class = ($this_roue && $trans_cat == 'cars') ? 'legko active' : 'legko';
    $bus_class = ($this_roue && in_array($trans_cat, ['buses', 'motorhomes'])) ? 'bus active' : 'bus';
    $truck_class = ($this_roue && in_array($trans_cat, ['vans', 'trucks', 'tractors', 'trailers', 'semitrailers'])) ? 'gruz active' : 'gruz';
    $spec_class = ($this_roue && in_array($trans_cat, ['constructionmachines', 'agricultures', 'forklifts'])) ? 'spec active' : 'spec';
    $bike_class = ($this_roue && $trans_cat == 'bikes') ? 'moto active' : 'moto';
    $boat_class = ($this_roue && in_array($trans_cat, ['motorboats', 'sailingships', 'catamarans', 'boats', 'rowingboats', 'outboards'])) ? 'lodka active' : 'lodka';
?>

    <div class="top-header">
        <div class="logo">

            <a href="<?= Yii::$app->homeUrl ?>">
                <svg  width="51" height="51">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#logomin"></use>
                </svg>
                <h2>MobileMakler</h2>
            </a>
        </div>

        <?php /*
        <div class="langu" style="top: 18px;">
            <div class="clos"></div>
            <span class="act"> <?= $curr_lang ?> </span>
            <ul>
                <li><a href="<?= Url::current(['lang' => 'ru']) ?>"><?= Yii::$app->params['app_langs']['ru'] ?></a>
                </li>
                <li><a href="<?= Url::current(['lang' => 'de']) ?>"><?= Yii::$app->params['app_langs']['de'] ?></a>
                </li>
            </ul>
        </div>
        */ ?>

        <?= $this->render('_login_panel') ?>
    </div>
    <a class="show_filtY2" href="#filt" id="show_filtos2">
        <svg class="show_filtY2__pic" width="33" height="34">
            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#filters"></use>
        </svg>
    </a>
    <a href="http://mobile.vlad-tests.ru/ru/cars#" class="nav-toggle" aria-hidden="true" >
        <svg class="show_filtY2__pic" width="33" height="34">
            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#menu"></use>
        </svg>
    </a>

    <div id="nav">
        <ul>
            <li class="home dv act"><?= Html::a('<i></i>' .Yii::t('site', 'MAIN'), Url::home()) ?></li>
            <li class="avtor dv hides"><a href="#"><i></i>Авторизация</a></li>
            <li class="<?= $car_class ?>"><?= TransCat::getMainmenuItem('car') ?></li>
            <li class="<?= $bus_class ?>"><?= TransCat::getMainmenuItem('bus') ?></li>
            <li class="<?= $truck_class ?>"><?= TransCat::getMainmenuItem('truck') ?></li>
            <li class="<?= $spec_class ?>"><?= TransCat::getMainmenuItem('spec') ?></li>
            <li class="<?= $bike_class ?>"><?= TransCat::getMainmenuItem('bike') ?></li>
            <li class="<?= $boat_class ?>"><?= TransCat::getMainmenuItem('boat') ?></li>
            <li class="sogl dv hides"><a href="#"><i></i>Cоглашение</a></li>
            <li class="kont dv hides"><a href="#"><i></i>Контакты</a></li>
        </ul>
    </div>


    <?php $this->beginJs(); ?>
    <script>
        var nav = responsiveNav("#nav", { // Selector
            animate: true, // Boolean: Use CSS3 transitions, true or false
            transition: 400, // Integer: Speed of the transition, in milliseconds
            label: "Menu", // String: Label for the navigation toggle
            insert: "before", // String: Insert the toggle before or after the navigation
            customToggle: "", // Selector: Specify the ID of a custom toggle
            closeOnNavClick: false, // Boolean: Close the navigation when one of the links are clicked
            openPos: "relative", // String: Position of the opened nav, relative or static
            navClass: "nav-collapses", // String: Default CSS class. If changed, you need to edit the CSS too!
            navActiveClass: "js-nav-active", // String: Class that is added to <html> element when nav is active
            jsClass: "js", // String: 'JS enabled' class which is added to <html> element
            init: function(){}, // Function: Init callback
            open: function(){}, // Function: Open callback
            close: function(){} // Function: Close callback
        });
            var lastScrollTop1 = 0;
            $(window).scroll(function(event){
            var st1 = $(this).scrollTop();
            if (st1 > lastScrollTop1){
             // downscroll code
                    function func111() {
                   nav.close();
                }
                setTimeout(func111, 200);
      }
    });
    </script>
    <?php $this->endJs(); ?>