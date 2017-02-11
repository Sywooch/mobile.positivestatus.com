<?php

namespace app\components;


    /**
     * ClientScriptView class file.
     *
     * @author Tsunu {@link http://www.yiiframework.com/forum/index.php/topic/24429-the-best-way-to-write-javascript/page__p__118436#entry118436}
     * @author MGHollander {@link https://github.com/MGHollander}
     * @version 1.1
     */

/**
 * ClientScriptView class is an extension on Yii's View to register JavaScript and CSS inside a view.
 * It keeps your code hightlighted and readable by your IDE.
 *
 * I've modified the code to remove the script ans style tags using a regular expression, because it may occur
 * that other HTML tags are used within a JavaScript of Style string.
 *
 * Usage:
 * 1. Save the file in your common/components/ dir
 * 2. Add the classname to your config
 *
 * return array(
 *     ...
 *     'components'=>array(
 *         'view'=>array(
 *             'class'=>'common\components\ClientScriptView',
 *             ...
 *         ),
 *         ...
 *     ),
 *     ...
 * );
 *
 * Usage is simular to {@link View::registerJs} and {@link View::registerCss}
 * The <script> and <style> tags are required for highlighting and IDE functionallity and will be removed automatically.
 *
 * Example:
 *
 * <?php $this->beginJs(); ?>
 * <script>
 * // Write your JavaScript here.
 * </script>
 * <?php $this->endJs(); ?>
 *
 * <?php $this->beginJs(View::POS_HEAD, 'unique-id'); ?>
 * <script type="text/javascript">
 * // Write your JavaScript here.
 * </script>
 * <?php $this->endJs(); ?>
 *
 * <?php $this->beginCss(); ?>
 * <style>
 * // Write your CSS here.
 * </style>
 * <?php $this->endCss(); ?>
 *
 * <?php $this->beginCss(['media' => 'screen'], 'unique-id'); ?>
 * <style type="text/css">
 * // Write your CSS here.
 * </style>
 * <?php $this->endCss(); ?>
 */

use yii\web\View;

class ClientScriptView extends View {
    /**
     * @see {@link View::registerJs} key parameter
     * @var string
     */
    protected $key;
    /**
     * @see {@link View::registerJs} position parameter
     * @var integer
     */
    protected $position;
    /**
     * @see {@link View::registerCss} options array
     * @var array
     */
    protected $options;

    /**
     * Set attributes and turn on output buffering.
     */
    public function beginJs($position = self::POS_READY, $key = null) {
        $this->position = $position;
        $this->key = $key;

        ob_start();
        ob_implicit_flush(false);
    }

    /**
     * Get default position if non set, get current buffer contents, delete current output buffer and register the contents.
     * @see {@link CClientScript::registerScript} return description
     */
    public function endJs() {
        parent::registerJs(str_ireplace(['<script>', '</script>'], ['', ''], ob_get_clean()), $this->position, $this->key);
    }

    /**
     * Set attributes and turn on output buffering.
     */
    public function beginCss($options = [], $key = null) {
        $this->options = $options;
        $this->key = $key;

        ob_start();
        ob_implicit_flush(false);
    }

    /**
     * Get default position if non set, get current buffer contents, delete current output buffer and register the contents.
     * @see {@link CClientScript::registerScript} return description
     */
    public function endCss() {
        parent::registerCss(str_ireplace(['<style>', '</style>'], ['', ''], ob_get_clean()), $this->options, $this->key);
    }
}