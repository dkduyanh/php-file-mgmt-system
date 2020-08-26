<?php
/**
 * Created by PhpStorm.
 * User: DuyAnh
 * Date: 11/14/2017
 * Time: 9:55 AM
 */

namespace app\library\widgets;

class DetailView extends \yii\widgets\DetailView
{
    public $template = '<tr><th style="width: 200px;" {captionOptions}>{label}</th><td{contentOptions}>{value}</td></tr>';
}