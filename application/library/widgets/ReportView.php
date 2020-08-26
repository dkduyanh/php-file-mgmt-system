<?php
/**
 * Created by PhpStorm.
 * User: TungDev
 * Date: 7/4/2019
 * Time: 11:40 AM
 */

namespace app\library\widgets;


use yii\grid\Column;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * @property mixed attributes
 */
class ReportView extends \yii\grid\GridView{
    /**
     * Renders the table body.
     * @return string the rendering result.
     */
    public function renderTableBody()
    {
        $models = array_values($this->dataProvider->getModels());
        $keys = $this->dataProvider->getKeys();
        $rows = [];
        $cells = [];
        foreach ($this->columns as $column) {
            /* @var $column Column */
            $temp = $column->renderHeaderCell();
            foreach ($models as $index => $model) {
                $key = $keys[$index];
                if ($this->beforeRow !== null) {
                    $row = call_user_func($this->beforeRow, $model, $key, $index, $this);
                    if (!empty($row)) {
                        $rows[] = $row;
                    }
                }
                $temp .= $column->renderDataCell($model, $key, $index + 1);
                $rows[] = $column->renderDataCell($model, $key, $index + 1);
                if ($this->afterRow !== null) {
                    $row = call_user_func($this->afterRow, $model, $key, $index, $this);
                    if (!empty($row)) {
                        $rows[] = $row;
                    }
                }
            }

            $cells[] = Html::tag('tr', $temp);
        }

        if (empty($rows) && $this->emptyText !== false) {
            $colspan = count($this->columns);

            return "<tbody>\n<tr><td colspan=\"$colspan\">" . $this->renderEmpty() . "</td></tr>\n</tbody>";
        }

        return "<tbody>\n".implode("", $cells) . "\n</tbody>";
    }

    /**
     * Renders a table row with the given data model and key.
     * @param mixed $model the data model to be rendered
     * @param mixed $key the key associated with the data model
     * @param int $index the zero-based index of the data model among the model array returned by [[dataProvider]].
     * @return string the rendering result
     */
    public function renderTableRow($model, $key, $index)
    {
        $cells = [];
        /* @var $column Column */
        foreach ($this->columns as $column) {
            $cells[] = $column->renderDataCell($model, $key, $index + 1);
        }
        if ($this->rowOptions instanceof \Closure) {
            $options = call_user_func($this->rowOptions, $model, $key, $index, $this);
        } else {
            $options = $this->rowOptions;
        }
        $options['data-key'] = is_array($key) ? json_encode($key) : (string) $key;
      //  o(Html::tag('tr', implode('', $cells), $options));
        return Html::tag('tr', implode('', $cells), $options);
    }

}
