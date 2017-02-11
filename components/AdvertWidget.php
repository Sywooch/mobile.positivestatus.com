<?php

namespace app\components;

Use app\models\Trans;
use yii\base\Widget;

class AdvertWidget extends Widget
{
    public $filter_model;
    private $trans_dp;

    public function getViewPath() {
        return parent::getViewPath() .DIRECTORY_SEPARATOR .'advert_widget';
    }

    public function init() {
        parent::init();

        // Здесь нужно сформировать $this->trans_dp выборкой из Trans на основе $this->filter_model
    }

    public function run() {
        // return $this->render('index');
        return $this->render('_listview');
    }
}