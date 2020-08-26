<?php


namespace app\library\widgets;


use Yii;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;

class DateRangePicker extends \kartik\daterange\DateRangePicker
{
    protected function initRange()
    {
        parent::initRange();
        if (ArrayHelper::keyExists('ranges', $this->pluginOptions)){
            $this->pluginOptions['ranges'] = ArrayHelper::merge($this->pluginOptions['ranges'], [
                Yii::t('kvdrp', 'This Year') => [new JsExpression("moment().startOf('year')"), new JsExpression("moment().endOf('day')")],
            ]);
        }
    }
}
