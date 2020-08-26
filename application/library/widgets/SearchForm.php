<?php

namespace app\library\widgets;

use yii\helpers\Json;
use yii\widgets\ActiveFormAsset;

/**
 * Class SearchForm
 * @package app\library\widgets
 * @author dkduyanh17@gmail.com
 */
class SearchForm extends ActiveForm
{
    public $gridViewId;

    public function registerClientScript()
    {
        parent::registerClientScript();
        if($this->gridViewId !== null){
            $id = $this->options['id'];
            $view = $this->getView();
            $view->registerJs("jQuery('#{$id}').on('beforeSubmit', function() {
                $(this).append($('#{$this->gridViewId}-filters').clone().hide());
            });");
        }
    }
}