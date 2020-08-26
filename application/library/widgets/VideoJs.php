<?php

/**
 * @link      https://github.com/wbraganca/yii2-videojs-widget
 * @copyright Copyright (c) 2014 Wanderson Bragança
 * @license   https://github.com/wbraganca/yii2-videojs-widget/blob/master/LICENSE
 */

namespace app\library\widgets;

use app\assets\VideoJsAsset;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\base\InvalidConfigException;

/**
 * The yii2-videojs-widget is a Yii 2 wrapper for the video.js
 * See more: http://www.videojs.com/
 *
 * @author Wanderson Bragança <wanderson.wbc@gmail.com>
 */
class VideoJs extends Widget
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
     * @var array
     */
    public $tags = [];

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
            $this->options['id'] = 'videojs-' . $this->getId();
        }
    }

    /**
     * Registers the needed assets
     */
    public function registerAssets()
    {
        echo "\n" . Html::beginTag('video', $this->options);
        if (!empty($this->tags) && is_array($this->tags)) {
            foreach ($this->tags as $tagName => $tags) {
                if (is_array($this->tags[$tagName])) {
                    foreach ($tags as $tagOptions) {
                        $tagContent = '';
                        if (isset($tagOptions['content'])) {
                            $tagContent = $tagOptions['content'];
                            unset($tagOptions['content']);
                        }
                        echo "\n" . Html::tag($tagName, $tagContent, $tagOptions);
                    }
                } else {
                    throw new InvalidConfigException("Invalid config for 'tags' property.");
                }
            }
        }
        echo "\n" . Html::endTag('video');

        $jsOptions = '{}';
        if (!empty($this->jsOptions)) {
            $jsOptions = Json::encode($this->jsOptions);
        }

        $js = "videojs(document.getElementById('{$this->options['id']}'), {$jsOptions}, function(){});";

        if($this->isAjax){
            echo Html::tag('script', $js);
        } else {
            $view = $this->getView();
            $view->registerJs($js);
            VideoJsAsset::register($view);
        }
    }
}
