<?php
/**
 * Created by PhpStorm.
 * User: DuyAnh
 * Date: 11/14/2017
 * Time: 9:55 AM
 */

namespace app\library\widgets;

class ActiveForm extends \yii\bootstrap\ActiveForm
{
    public $errorSummaryCssClass = 'callout callout-danger';
    public $options = ['class' => 'form-horizontal'];
    public $fieldConfig = [
        'options' => ['class' => 'form-group'],
        'template' => "{label}\n<div class=\"col-sm-6 col-xs-12\">{input}{hint}</div>\n<div>{error}</div>",
        'labelOptions' => ['class' => 'control-label col-sm-3 col-xs-12'],
        'inputOptions' => ['class' => 'form-control col-sm-7 col-xs-12'],
    ];
}
