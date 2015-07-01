<?php

/**
 * @author Taron Saribekyan <saribekyantaron@gmail.com>
 * @copyright Copyright &copy; 2014, Taron Saribekyan. All rights reserved.
 * @license http://opensource.org/licenses/BSD-3-Clause The BSD 3-Clause License
 * @version 1.0.0
 */
Yii::import('zii.widgets.grid.CGridView');
Yii::import('zii.widgets.grid.CDataColumn');

class TsGridView extends CGridView {

    const TYPE_STRIPED = 'striped';
    const TYPE_BORDERED = 'bordered';
    const TYPE_CONDENSED = 'condensed';
    const TYPE_HOVER = 'hover';

    public $rowCssClass = false;
    public $type;

    public function init() {
        $classes = array('table');
        if (isset($this->type)) {
            if (is_string($this->type)) {
                $this->type = explode(' ', $this->type);
            }
            if (!empty($this->type)) {
                $validTypes = array(self::TYPE_STRIPED, self::TYPE_BORDERED, self::TYPE_CONDENSED, self::TYPE_HOVER);
                foreach ($this->type as $type) {
                    if (in_array($type, $validTypes)) {
                        $classes[] = 'table-' . $type;
                    }
                }
            }
        }
        if (!empty($classes)) {
            $classes = implode(' ', $classes);
            if (isset($this->itemsCssClass)) {
                $this->itemsCssClass .= ' ' . $classes;
            } else {
                $this->itemsCssClass = $classes;
            }
        }
        parent::init();
    }

    protected function initColumns() {
        foreach ($this->columns as $i => $column) {
            if (is_array($column) && !isset($column['class'])) {
                $this->columns[$i]['class'] = 'zii.widgets.grid.CDataColumn';
            }
        }
        parent::initColumns();
    }

    protected function createDataColumn($text) {
        if (!preg_match('/^([\w\.]+)(:(\w*))?(:(.*))?$/', $text, $matches)) {
            throw new CException(Yii::t(
                    'zii', 'The column must be specified in the format of "Name:Type:Label", where "Type" and "Label" are optional.'
            ));
        }
        $column = new CDataColumn($this);
        $column->name = $matches[1];

        if (isset($matches[3]) && $matches[3] !== '') {
            $column->type = $matches[3];
        }
        if (isset($matches[5])) {
            $column->header = $matches[5];
        }
        return $column;
    }

}
