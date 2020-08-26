<?php
/**
 * Created by PhpStorm.
 * User: DuyAnh
 * Date: 11/14/2017
 * Time: 9:55 AM
 */

namespace app\library\widgets;

class GridView extends \yii\grid\GridView
{
    public $layout = "{pager}\n{summary}\n{items}\n{pager}";
    public $tableOptions = [
        'class' => 'table table-bordered table-hover'
    ];
    public $pager = [
        // Set maximum number of page buttons that can be displayed
        'maxButtonCount' => 5, //(\Yii::$app->mobileDetect->isDesktop() ? 10 : 5),
    ];
}