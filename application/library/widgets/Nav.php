<?php
/**
 * Created by PhpStorm.
 * User: DuyAnh
 * Date: 11/14/2017
 * Time: 9:55 AM
 */

namespace app\library\widgets;


use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class Nav extends \yii\widgets\Menu
{

    public $dropDownCaret;

    /**
     * Renders the content of a menu item.
     * Note that the container and the sub-menus are not rendered here.
     * @param array $item the menu item to be rendered. Please refer to [[items]] to see what data might be in the item.
     * @return string the rendering result
     */
    protected function renderItem($item)
    {
        //DuyAnh :: format label
        $item['label'] = Html::tag('span', $item['label']);

        //DuyAnh :: add icon
        if(isset($item['icon'])){
            $item['label'] = Html::tag('i', '', ['class' => $item['icon']]).$item['label'];
        }

        //DuyAnh :: add dropdown caret
        if(isset($item['items'])){
            $item['label'] .= Html::tag('span', Html::tag('i', '', ['class' => $this->dropDownCaret]), ['class' => 'pull-right-container']);
        }

        //Render item
        if (isset($item['url'])) {
            $template = ArrayHelper::getValue($item, 'template', $this->linkTemplate);

            return strtr($template, [
                '{url}' => Html::encode(Url::to($item['url'])),
                '{label}' => $item['label'],
            ]);
        } else {
            $template = ArrayHelper::getValue($item, 'template', $this->labelTemplate);

            return strtr($template, [
                '{label}' => $item['label'],
            ]);
        }
    }

    public static function createMenu(array $array, $label, $url, array $paramsWithKey = array(), $icon = false)
    {
        $ret = [];
        if(!is_array($url)) $url = [$url];
        foreach($array as $i)
        {
            foreach($paramsWithKey as $p => $v){
                if(isset($i[$v])){
                    $url[$p] = $i[$v];
                }
            }

            $ret[] = [
                'label' => $i[$label],
                'icon' => isset($i[$icon])?$i[$icon]:false,
                'url' => $url
            ];
        }
        return $ret;
    }
}