<?php


namespace app\library\widgets;


use dosamigos\chartjs\ChartJsAsset;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;

class ChartJs extends \dosamigos\chartjs\ChartJs
{
    /**
     * @var bool
     */
    public $isAjax = false;

    /**
     * Registers the required js files and script to initialize ChartJS plugin
     */
    protected function registerClientScript()
    {
        $id = $this->options['id'];
        $view = $this->getView();
        ChartJsAsset::register($view);

        $config = Json::encode(
            [
                'type' => $this->type,
                'data' => $this->data ?: new JsExpression('{}'),
                'options' => $this->clientOptions ?: new JsExpression('{}'),
                'plugins' => $this->plugins
            ]
        );

        $js = "new Chart($('#{$id}'),{$config});";

        if($this->isAjax){
            echo Html::tag('script', $js, ['type' => 'text/javascript']);
        }

        $view->registerJs($js);
    }
}