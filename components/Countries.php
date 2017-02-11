<?php

namespace app\components;

use Yii;


class Countries
{
    private static $_data = [
        'DE' => 'Germany',
        'BE' => 'Belgium',
        'FR' => 'France',
        'ND' => 'Netherlands',
        'IT' => 'Italy',
        'RU' => 'Russia',
    ];

    public static function getListData() {
        return self::$_data;
    }

    public static function getCountryByCode($code) {
        $code = strtoupper($code);
        return (array_key_exists($code, self::$_data)) ? self::$_data[$code] : null;
    }

    public static function getWholeEurope() {
        return Yii::t('site', 'WHOLE_EUROPE');
    }

    public static function getAllCountryOptions() {
        $whole_europe = self::getWholeEurope();

        $options = <<< HERE
            <option value='AT' data-image="/images/blank.gif" data-imagecss="flag at" data-title="Austria">Austria</option>
            <option value='GRr' data-image="/images/blank.gif" data-imagecss="flag gr" data-title="Greece">Greece</option>
            <option value='MT' data-image="/images/blank.gif" data-imagecss="flag mt" data-title="Malta">Malta</option>
            <option value='SK' data-image="/images/blank.gif" data-imagecss="flag sk" data-title="Slovakia">Slovakia</option>
            <option value='AL' data-image="/images/blank.gif" data-imagecss="flag al" data-title="Albania">Albania</option>
            <option value='DK' data-image="/images/blank.gif" data-imagecss="flag dk" data-title="Denmark">Denmark</option>
            <option value='MD' data-image="/images/blank.gif" data-imagecss="flag md" data-title="Moldova">Moldova</option>
            <option value='SI' data-image="/images/blank.gif" data-imagecss="flag si" data-title="Slovenia">Slovenia</option>
            <option value='AD' data-image="/images/blank.gif" data-imagecss="flag ad" data-title="Andorra">Andorra</option>
            <option value='IE' data-image="/images/blank.gif" data-imagecss="flag ie" data-title="Ireland">Ireland</option>
            <option value='MC' data-image="/images/blank.gif" data-imagecss="flag mc" data-title="Monaco">Monaco</option>
            <option value='UA' data-image="/images/blank.gif" data-imagecss="flag ua" data-title="Ukraine">Ukraine</option>
            <option value='BY' data-image="/images/blank.gif" data-imagecss="flag by" data-title="Belarus">Belarus</option>
            <option value='IS' data-image="/images/blank.gif" data-imagecss="flag is" data-title="Iceland">Iceland</option>
            <option value='ND' data-image="/images/blank.gif" data-imagecss="flag nl" data-title="Netherlands">Netherlands</option>
            <option value='FI' data-image="/images/blank.gif" data-imagecss="flag fi" data-title="Finland">Finland</option>
            <option value='BE' data-image="/images/blank.gif" data-imagecss="flag be" data-title="Belgium">Belgium</option>
            <option value='ES' data-image="/images/blank.gif" data-imagecss="flag es" data-title="Spain">Spain</option>
            <option value='NO' data-image="/images/blank.gif" data-imagecss="flag no" data-title="Norway">Norway</option>
            <option value='FR' data-image="/images/blank.gif" data-imagecss="flag fr" data-title="France">France</option>
            <option value='BG' data-image="/images/blank.gif" data-imagecss="flag bg" data-title="Bulgaria">Bulgaria</option>
            <option value='IT' data-image="/images/blank.gif" data-imagecss="flag it" data-title="Italy">Italy</option>
            <option value='PL' data-image="/images/blank.gif" data-imagecss="flag pl" data-title="Poland">Poland</option>
            <option value='HR' data-image="/images/blank.gif" data-imagecss="flag hr" data-title="Croatia (Hrvatska)">Croatia (Hrvatska)</option>
            <option value='BA' data-image="/images/blank.gif" data-imagecss="flag ba" data-title="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
            <option value='LV' data-image="/images/blank.gif" data-imagecss="flag lv" data-title="Latvia">Latvia</option>
            <option value='PT' data-image="/images/blank.gif" data-imagecss="flag pt" data-title="Portugal">Portugal</option>
            <option value='CS' data-image="/images/blank.gif" data-imagecss="flag cs" data-title="Serbia and Montenegro">Serbia and Montenegro</option>
            <option value='VA' data-image="/images/blank.gif" data-imagecss="flag va" data-title="Vatican City State (Holy See)">Vatican City</option>
            <option value='LT' data-image="/images/blank.gif" data-imagecss="flag lt" data-title="Lithuania">Lithuania</option>
            <option value='RU' data-image="/images/blank.gif" data-imagecss="flag ru" data-title="Russian Federation">Russian Federation</option>
            <option value='CZ' data-image="/images/blank.gif" data-imagecss="flag cz" data-title="Czech Republic">Czech Republic</option>
            <option value='GB' data-image="/images/blank.gif" data-imagecss="flag gb" data-title="Great Britain (UK)">Great Britain (UK)</option>
            <option value='LI' data-image="/images/blank.gif" data-imagecss="flag li" data-title="Liechtenstein">Liechtenstein</option>
            <option value='RO' data-image="/images/blank.gif" data-imagecss="flag ro" data-title="Romania">Romania</option>
            <option value='CH' data-image="/images/blank.gif" data-imagecss="flag ch" data-title="Switzerland">Switzerland</option>
            <option value='HU' data-image="/images/blank.gif" data-imagecss="flag hu" data-title="Hungary">Hungary</option>
            <option value='LU' data-image="/images/blank.gif" data-imagecss="flag lu" data-title="Luxembourg">Luxembourg</option>
            <option value='SM' data-image="/images/blank.gif" data-imagecss="flag sm" data-title="San Marino">San Marino</option>
            <option value='SE' data-image="/images/blank.gif" data-imagecss="flag se" data-title="Sweden">Sweden</option>
            <option value='DE' data-image="/images/blank.gif" data-imagecss="flag de" data-title="Germany">Germany</option>
            <option value='MK' data-image="/images/blank.gif" data-imagecss="flag mk" data-title="Macedonia">Macedonia</option>
            <option value='CS' data-image="/images/blank.gif" data-imagecss="flag cs" data-title="Serbia">Serbia</option>
            <option value='EE' data-image="/images/blank.gif" data-imagecss="flag ee" data-title="Estonia">Estonia</option>
            <option value='' selected="selected" data-image="/images/blank.gif" data-imagecss="flag eu" data-title="Euro"> $whole_europe </option>
HERE;

        return $options;
    }
}



/*
        <option value='at' data-image="/images/blank.gif" data-imagecss="flag at" data-title="Austria">Austria</option>
        <option value='gr' data-image="/images/blank.gif" data-imagecss="flag gr" data-title="Greece">Greece</option>
        <option value='mt' data-image="/images/blank.gif" data-imagecss="flag mt" data-title="Malta">Malta</option>
        <option value='sk' data-image="/images/blank.gif" data-imagecss="flag sk" data-title="Slovakia">Slovakia</option>
        <option value='al' data-image="/images/blank.gif" data-imagecss="flag al" data-title="Albania">Albania</option>
        <option value='dk' data-image="/images/blank.gif" data-imagecss="flag dk" data-title="Denmark">Denmark</option>
        <option value='md' data-image="/images/blank.gif" data-imagecss="flag md" data-title="Moldova">Moldova</option>
        <option value='si' data-image="/images/blank.gif" data-imagecss="flag si" data-title="Slovenia">Slovenia</option>
        <option value='ad' data-image="/images/blank.gif" data-imagecss="flag ad" data-title="Andorra">Andorra</option>
        <option value='ie' data-image="/images/blank.gif" data-imagecss="flag ie" data-title="Ireland">Ireland</option>
        <option value='mc' data-image="/images/blank.gif" data-imagecss="flag mc" data-title="Monaco">Monaco</option>
        <option value='ua' data-image="/images/blank.gif" data-imagecss="flag ua" data-title="Ukraine">Ukraine</option>
        <option value='by' data-image="/images/blank.gif" data-imagecss="flag by" data-title="Belarus">Belarus</option>
        <option value='is' data-image="/images/blank.gif" data-imagecss="flag is" data-title="Iceland">Iceland</option>
        <option value='nl' data-image="/images/blank.gif" data-imagecss="flag nl" data-title="Netherlands">Netherlands</option>
        <option value='fi' data-image="/images/blank.gif" data-imagecss="flag fi" data-title="Finland">Finland</option>
        <option value='be' data-image="/images/blank.gif" data-imagecss="flag be" data-title="Belgium">Belgium</option>
        <option value='es' data-image="/images/blank.gif" data-imagecss="flag es" data-title="Spain">Spain</option>
        <option value='no' data-image="/images/blank.gif" data-imagecss="flag no" data-title="Norway">Norway</option>
        <option value='fr' data-image="/images/blank.gif" data-imagecss="flag fr" data-title="France">France</option>
        <option value='bg' data-image="/images/blank.gif" data-imagecss="flag bg" data-title="Bulgaria">Bulgaria</option>
        <option value='it' data-image="/images/blank.gif" data-imagecss="flag it" data-title="Italy">Italy</option>
        <option value='pl' data-image="/images/blank.gif" data-imagecss="flag pl" data-title="Poland">Poland</option>
        <option value='hr' data-image="/images/blank.gif" data-imagecss="flag hr" data-title="Croatia (Hrvatska)">Croatia (Hrvatska)</option>
        <option value='ba' data-image="/images/blank.gif" data-imagecss="flag ba" data-title="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
        <option value='lv' data-image="/images/blank.gif" data-imagecss="flag lv" data-title="Latvia">Latvia</option>
        <option value='pt' data-image="/images/blank.gif" data-imagecss="flag pt" data-title="Portugal">Portugal</option>
        <option value='cs' data-image="/images/blank.gif" data-imagecss="flag cs" data-title="Serbia and Montenegro">Serbia and Montenegro</option>
        <option value='va' data-image="/images/blank.gif" data-imagecss="flag va" data-title="Vatican City State (Holy See)">Vatican City</option>
        <option value='lt' data-image="/images/blank.gif" data-imagecss="flag lt" data-title="Lithuania">Lithuania</option>
        <option value='ru' data-image="/images/blank.gif" data-imagecss="flag ru" data-title="Russian Federation">Russian Federation</option>
        <option value='cz' data-image="/images/blank.gif" data-imagecss="flag cz" data-title="Czech Republic">Czech Republic</option>
        <option value='gb' data-image="/images/blank.gif" data-imagecss="flag gb" data-title="Great Britain (UK)">Great Britain (UK)</option>
        <option value='li' data-image="/images/blank.gif" data-imagecss="flag li" data-title="Liechtenstein">Liechtenstein</option>
        <option value='ro' data-image="/images/blank.gif" data-imagecss="flag ro" data-title="Romania">Romania</option>
        <option value='ch' data-image="/images/blank.gif" data-imagecss="flag ch" data-title="Switzerland">Switzerland</option>
        <option value='hu' data-image="/images/blank.gif" data-imagecss="flag hu" data-title="Hungary">Hungary</option>
        <option value='lu' data-image="/images/blank.gif" data-imagecss="flag lu" data-title="Luxembourg">Luxembourg</option>
        <option value='sm' data-image="/images/blank.gif" data-imagecss="flag sm" data-title="San Marino">San Marino</option>
        <option value='se' data-image="/images/blank.gif" data-imagecss="flag se" data-title="Sweden">Sweden</option>
        <option value='de' data-image="/images/blank.gif" data-imagecss="flag de" data-title="Germany">Germany</option>
        <option value='mk' data-image="/images/blank.gif" data-imagecss="flag mk" data-title="Macedonia">Macedonia</option>
        <option value='cs' data-image="/images/blank.gif" data-imagecss="flag cs" data-title="Serbia">Serbia</option>
        <option value='ee' data-image="/images/blank.gif" data-imagecss="flag ee" data-title="Estonia">Estonia</option>
        <option value='eu' selected="selected" data-image="/images/blank.gif" data-imagecss="flag eu" data-title="Euro">Вся Европа</option>
 */

