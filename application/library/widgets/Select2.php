<?php
/**
 * Created by PhpStorm.
 * User: DuyAnh
 * Date: 11/14/2017
 * Time: 9:55 AM
 */

namespace app\library\widgets;

use app\assets\Select2Asset;
use yii\helpers\Html;
use yii\bootstrap\InputWidget;
use yii\helpers\Json;
use yii\web\JsExpression;

class Select2 extends InputWidget
{

    /**
     * @var array the items (for list inputs)
     */
    public $items = [];

    /**
     * @var Minimum search term length
     */
    public $minimumInputLength = false;

    /**
     * @var Provides support for ajax data sources.
     */
    public $ajax;

    /**
     * @var Customizes the way that search results are rendered.
     */
    public $templateResult;

    /**
     * @var dynamically create new options from text input by the user in the search box
     */
    public $tags;

    public function run()
    {
        $this->registerAssets();

        Html::addCssClass($this->options, 'form-control');
        if ($this->hasModel()) {
            return Html::activeDropDownList($this->model, $this->attribute, $this->items, $this->options);
        }
        return Html::dropDownList($this->name, $this->value, $this->items, $this->options);
    }

    public function registerAssets(){
        $view = $this->getView();
        Select2Asset::register($view);

        $id = $this->options['id'];
        $preselectScript = '';
        $select2Options = new \ArrayObject();
        if($this->ajax){
            $defaultAjaxParams = [
                'method' => 'GET',
                'url' => '',
                'dataType' => 'json',
                'data' => '{}',
                'delay' => 800
            ];
           $this->ajax = array_merge($defaultAjaxParams, $this->ajax);
           $select2Options['ajax'] = $this->ajax;

           $attribute = $this->attribute;
           if($this->hasModel() && $this->model->$attribute){

               $preselectScript = "
                   $.ajax({
                        type: '".$this->ajax['method']."',
                        url: '".$this->ajax['url']."',
                        dataType: '".$this->ajax['dataType']."',
                        data: {'id': ".json_encode($this->model->$attribute)."}
                    }).then(function (data) {
                        for(i=0; i<data.results.length; i++){;
                            var option = new Option(data.results[i].text, data.results[i].id, true, true);
                            $('#{$id}').append(option);     
                        }
                                          
                        // manually trigger the `select2:select` event
                        $('#{$id}').trigger({
                            type: 'select2:select',
                            params: {
                                data: data
                            }
                        });
                    });           
               ";
           }
        }
        if($this->templateResult){
            $select2Options['templateResult'] = new JsExpression($this->_templateResult());
            $select2Options['escapeMarkup'] = new JsExpression('function (markup) { return markup; }');
        }

        if($this->tags){
            $select2Options['tags'] = true;
            //$preselectScript .= "";
        }

        if($this->items && is_array($this->items)){
            $select2Options['data'] = $this->items;
        }

        $select2Options = Json::encode($select2Options);
        $view->registerJs("
            $('#{$id}').select2($select2Options);            
            
            $preselectScript
        ");
    }

    protected function _templateResult()
    {
        return <<< SCRIPT
                    function formatTemplate(data) {                        
                        if (data.loading) {
                            return data.text;
                        }
                        
                        var markup = '';
                        if(data.image){
                            markup =
                                '<div class="row">' +
                                    '<div class="col-sm-5">' +
                                        '<img src="' + data.image + '" class="img-rounded" style="width:30px" />' +
                                        '<b style="margin-left:5px">' + data.text + '</b>' +
                                    '</div>' +
                                '</div>';
                        } else {
                            markup =
                                '<div class="row">' +
                                    '<div class="col-sm-12">' +
                                        '<b style="margin-left:5px">' + data.text + '</b>' +
                                    '</div>' +
                                '</div>';
                        }
                        if (data.description) {
                          markup += '<p>' + data.description + '</p>';
                        }
                        return '<div style="overflow:hidden;">' + markup + '</div>';
                    }
SCRIPT;
    }

}