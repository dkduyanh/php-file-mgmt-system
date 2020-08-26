<?php


namespace app\library\widgets;


use yii\bootstrap\BootstrapPluginAsset;
use yii\helpers\Json;

class Modal extends \yii\bootstrap\Modal
{


    /**
     * BootStrapWidgetTrait.php
     * Registers a specific Bootstrap plugin and the related events
     * @param string $name the name of the Bootstrap plugin
     */
    protected function registerPlugin($name)
    {
        $view = $this->getView();

        BootstrapPluginAsset::register($view);

        $id = $this->options['id'];

        if ($this->clientOptions !== false) {
            $options = empty($this->clientOptions) ? '' : Json::htmlEncode($this->clientOptions);
            $js = "jQuery('#$id').$name($options);";

            //DuyAnh :: TODO: Since bootstrap remote modal is deprecated so we must process by ajax loading here

            $view->registerJs($js);
        }

        $this->registerClientEvents();
    }
}