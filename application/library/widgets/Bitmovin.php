<?php
/**
 * @author: DuyAnh <dkduyanh17@gmail.com>
 */

namespace app\library\widgets;

use yii\base\Widget;;
use yii\helpers\Html;
use yii\helpers\Json;

class Bitmovin extends Widget
{
    /**
     * @var array
     */
    public $options = [];
    /**
     * @var array
     */
    public $jsOptions = [];

    /**
     * @var bool
     */
    public $isAjax = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->initOptions();
        $this->registerAssets();
    }

    /**
     * Initializes the widget options
     */
    protected function initOptions()
    {
        if (!isset($this->options['id'])) {
            $this->options['id'] = 'bitmovin-player-' . $this->getId();
        }
    }

    /**
     * Registers the needed assets
     */
    public function registerAssets()
    {
        echo "\n" . Html::tag('div', '', $this->options) . "\n";
        $js = "var player = bitmovin.player('{$this->options['id']}'); \n";
        $js .= "player.setup(". Json::encode($this->jsOptions).").then(
                    function(value) {
                        // Success
                        console.log('Successfully created bitmovin player instance');
                    },
                    function(reason) {
                    // Error!
                    console.log('Error while creating bitmovin player instance');
            })";

        if($this->isAjax){
            echo Html::tag('script', $js);
            echo Html::tag('script', '', ['src' => 'https://bitmovin-a.akamaihd.net/bitmovin-player/stable/7.4/bitmovinplayer.js']);
        } else {
            $view = $this->getView();
            $view->registerJs($js);
            $view->registerJsFile('https://bitmovin-a.akamaihd.net/bitmovin-player/stable/7.4/bitmovinplayer.js');
        }


    }
}