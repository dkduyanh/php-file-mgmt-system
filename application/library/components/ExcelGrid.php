<?php

namespace app\library\components;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQueryInterface;
use yii\grid\Column;
use yii\helpers\ArrayHelper;
use yii\base\Model;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ExcelGrid extends \yii\grid\GridView
{
    public $columns_array;
    public $properties;
    public $autoFilter;
    public $footerOptions;
    public $report;
    public $startColumn;
    public $filename = 'excel';
    public $extension = 'xlsx';
    private $_provider;
    private $_visibleColumns;
    private $_beginRow = 1;
    private $_endRow;
    private $_endCol;
    private $_objPHPExcel;
    private $_objPHPExcelSheet;
    private $_objPHPExcelWriter;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $this->init_provider();
        $this->init_excel_sheet();
        $this->initPHPExcelWriter('Xlsx');
        if ($this->report){
            $this->generateHeaderReport();
            $this->generateBodyReport();
        }else{
            $this->generateHeader();
            $this->generateBody();
        }
        $writer = $this->_objPHPExcelWriter;
        $this->setHttpHeaders();
        $writer->save('php://output');
        Yii::$app->end();
        parent::run();
    }

    public function init_provider()
    {
        $this->_provider = clone($this->dataProvider);
    }

    public function init_excel_sheet()
    {
        $this->_objPHPExcel = new Spreadsheet();
        $creator = '';
        $title = '';
        $subject = '';
        $description = 'Excel Grid Generated By BHD extension';
        $category = '';
        $keywords = '';
        $manager = '';
        $company = 'BHD';
        $created = date("Y-m-d H:i:s");
        $lastModifiedBy = '';
        extract($this->properties);
        $this->_objPHPExcel->getProperties()
            ->setCreator($creator)
            ->setTitle($title)
            ->setSubject($subject)
            ->setDescription($description)
            ->setCategory($category)
            ->setKeywords($keywords)
            ->setManager($manager)
            ->setCompany($company)
            ->setCreated($created)
            ->setLastModifiedBy($lastModifiedBy);
        $this->_objPHPExcelSheet = $this->_objPHPExcel->getActiveSheet();
    }

    public function initPHPExcelWriter($writer)
    {
        $this->_objPHPExcelWriter = IOFactory::createWriter(
            $this->_objPHPExcel,
            $writer
        );
    }

    public function generateHeaderReport()
    {
        $this->setVisibleColumns();
        $sheet = $this->_objPHPExcelSheet;
        $colFirst = self::columnName(1);
        $this->_endCol = 0;
        foreach ($this->_visibleColumns as $column) {
            $this->_endCol++;
            $head = ($column instanceof \yii\grid\DataColumn) ? $this->getColumnHeader($column) : $column->header;
            $sheet->setCellValue(self::columnName($this->_beginRow) . $this->_endCol, $head, true);
        }
        $sheet->freezePane($colFirst . ($this->_beginRow + 1));
    }

    public function generateBodyReport(){
        $columns = $this->_visibleColumns;

        $models = array_values($this->_provider->getModels());
        if (count($columns) == 0) {
           $this->_objPHPExcelSheet->setCellValue('A1', $this->emptyText, true);
           reset($models);
           return 0;
        }

        $keys = $this->_provider->getKeys();
        $this->_endRow = 0;
        foreach ($models as $index => $model) {
            $key = $keys[$index];
            $this->generateRowReport($model, $key, $index + $this->startColumn);
           /* foreach ($this->_visibleColumns as $k2 => $column) {
                if ($column instanceof \yii\grid\SerialColumn || $column instanceof \yii\grid\ActionColumn) {
                    continue;
                } else {
                    $format = $column->format;
                    $value = ($column->content === null) ?
                        $this->formatter->format($column->getDataCellValue($model, $key, $index), $format) :
                        call_user_func($column->content, $model, $key, $index, $column);
                }
                if (empty($value) && !empty($column->attribute) && $column->attribute !== null) {
                    $value = ArrayHelper::getValue($model, $column->attribute, '');
                }
                $cells[$k2][] = $value;
                $this->_endCol++;
                $this->_objPHPExcelSheet->setCellValue(self::columnName($this->_endRow) . ($k2 + 1), strip_tags($value), true);
            }*/
            $this->_endRow++;
        }
        // Set autofilter on
       // $this->_objPHPExcelSheet->fromArray($cells, NULL, $this->startColumn);
        return ($this->_endRow > 0) ? count($models) : 0;
    }

    public function generateRowReport($model, $key, $index)
    {
        /* @var $column Column */
        $this->_endCol = 0;
        foreach ($this->_visibleColumns as $column) {
            if ($column instanceof \yii\grid\SerialColumn || $column instanceof \yii\grid\ActionColumn) {
                continue;
            } else {
                $format = $column->format;
                $value = ($column->content === null) ?
                    $this->formatter->format($column->getDataCellValue($model, $key, $index), $format) :
                    call_user_func($column->content, $model, $key, $index, $column);
            }
            if (empty($value) && !empty($column->attribute) && $column->attribute !== null) {
                $value = ArrayHelper::getValue($model, $column->attribute, '');
            }
            $this->_endCol++;
           $this->_objPHPExcelSheet->setCellValue(self::columnName($index) . $this->_endCol, strip_tags($value), true);
        }
    }

    public function generateHeader()
    {
        $this->setVisibleColumns();
        $sheet = $this->_objPHPExcelSheet;
        $colFirst = self::columnName(1);
        $this->_endCol = 0;
        foreach ($this->_visibleColumns as $column) {
            $this->_endCol++;
            $head = ($column instanceof \yii\grid\DataColumn) ? $this->getColumnHeader($column) : $column->header;
            $cell = $sheet->setCellValue(self::columnName($this->_endCol) . $this->_beginRow, $head, true);
        }
        $sheet->freezePane($colFirst . ($this->_beginRow + 1));
    }

    public function generateBody()
    {
        $columns = $this->_visibleColumns;
        $models = array_values($this->_provider->getModels());
        if (count($columns) == 0) {
            $cell = $this->_objPHPExcelSheet->setCellValue('A1', $this->emptyText, true);
            $model = reset($models);
            return 0;
        }
        $keys = $this->_provider->getKeys();
        $this->_endRow = 0;
        foreach ($models as $index => $model) {
            $key = $keys[$index];
            $this->generateRow($model, $key, $index);
            $this->_endRow++;
        }

        $totalRows = $this->_endRow + 2;

        if ($this->showFooter){
            $this->renderFooter($totalRows, $columns);
        }
        // Set autofilter on
        if ($this->autoFilter){
            $this->_objPHPExcelSheet->setAutoFilter(
                self::columnName(1) . $this->_beginRow . ":". self::columnName($this->_endCol) . $this->_endRow
            );
        }

        return ($this->_endRow > 0) ? count($models) : 0;
    }

    public function generateRow($model, $key, $index)
    {
        $cells = [];
        /* @var $column Column */
        $this->_endCol = 0;
        foreach ($this->_visibleColumns as $column) {
            if ($column instanceof \yii\grid\SerialColumn || $column instanceof \yii\grid\ActionColumn) {
                continue;
            } else {
                $format = $column->format;
                $value = ($column->content === null) ?
                    $this->formatter->format($column->getDataCellValue($model, $key, $index), $format) :
                    call_user_func($column->content, $model, $key, $index, $column);
            }
            if (empty($value) && !empty($column->attribute) && $column->attribute !== null) {
                $value = ArrayHelper::getValue($model, $column->attribute, '');
            }
            $this->_endCol++;
            $cell = $this->_objPHPExcelSheet->setCellValue(self::columnName($this->_endCol) . ($index + $this->_beginRow + 1), strip_tags($value), true);
        }
    }

    /**
     * Renders the file footer.
     * @var $models
     * @var $columns
     */
    public function renderFooter($rows, $columns)
    {
        foreach ($columns as $index => $column) {
            $col = self::columnName($index+1);
            $this->_objPHPExcelSheet->setCellValue($col.$rows, $column->footer);
            $this->_objPHPExcelSheet->getStyle($col.$rows)->applyFromArray([
                'font' => [
                    'bold' => true,
                ]]);
        }
    }

    protected function setVisibleColumns()
    {
        $cols = [];
        foreach ($this->columns as $key => $column) {
            if ($column instanceof \yii\grid\SerialColumn || $column instanceof \yii\grid\ActionColumn) {
                continue;
            }
            $cols[] = $column;
        }
        $this->_visibleColumns = $cols;
    }

    public function getColumnHeader($col)
    {
        if (isset($this->columns_array[$col->attribute]))
            return $this->columns_array[$col->attribute];
        /* @var $model yii\base\Model */
        if ($col->header !== null || ($col->label === null && $col->attribute === null)) {
            return trim($col->header) !== '' ? $col->header : $col->grid->emptyCell;
        }
        $provider = $this->dataProvider;
        if ($col->label === null) {
            if ($provider instanceof ActiveDataProvider && $provider->query instanceof ActiveQueryInterface) {
                $model = new $provider->query->modelClass;
                $label = $model->getAttributeLabel($col->attribute);
            } else {
                $models = $provider->getModels();
                if (($model = reset($models)) instanceof Model) {
                    $label = $model->getAttributeLabel($col->attribute);
                } else {
                    $label = $col->attribute;
                }
            }
        } else {
            $label = $col->label;
        }
        return $label;
    }

    public static function columnName($index)
    {
        $i = $index - 1;
        if ($i >= 0 && $i < 26) {
            return chr(ord('A') + $i);
        }
        if ($i > 25) {
            return (self::columnName($i / 26)) . (self::columnName($i % 26 + 1));
        }
        return 'A';
    }

    protected function setHttpHeaders()
    {
        header("Cache-Control: no-cache");
        header("Expires: 0");
        header("Pragma: no-cache");
        header("Content-Type: application/{$this->extension}");
        header("Content-Disposition: attachment; filename={$this->filename}.{$this->extension}");
    }
}
