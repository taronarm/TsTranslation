<?php

/**
 * @author Taron Saribekyan <saribekyantaron@gmail.com>
 * @copyright Copyright &copy; 2014, Taron Saribekyan. All rights reserved.
 * @license http://opensource.org/licenses/BSD-3-Clause The BSD 3-Clause License
 * @version 1.0.0
 */
class ExtLanguages extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function getDbConnection() {
        $connectionID = Yii::app()->getComponent('messages')->connectionID;
        if (Yii::app()->getComponent($connectionID) === null) {
            throw new TsTranslationException('There is no connection component defined with name "' . $connectionID . '", which you passed in "messages" component as "connectionID".');
        }
        return Yii::app()->getComponent($connectionID);
    }

    public function tableName() {
        return 'tse_ext_languages';
    }

    public function rules() {
        return array(
            array('status, ordering, asDefault', 'numerical', 'integerOnly' => true),
            array('name, nativeName', 'length', 'max' => 50),
            array('code2', 'length', 'max' => 3),
            array('code3', 'length', 'max' => 4),
            array('flagLink, createDate', 'safe'),
            array('id, name, nativeName, code2, code3, flagLink, status, ordering, createDate, asDefault', 'safe', 'on' => 'search'),
        );
    }

    public function relations() {
        return array(
        );
    }

    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'nativeName' => 'Native Name',
            'code2' => 'Code',
            'code3' => 'Code (3 symbol)',
            'flagLink' => 'Flag Link',
            'status' => 'Status',
            'ordering' => 'Order',
            'createDate' => 'Create Date',
            'default' => 'Default',
        );
    }

    public function search() {
        $criteria = new CDbCriteria;
        $sort = new CSort;
        $sort->defaultOrder = 'ordering ASC';
        $sort->attributes = array('name', 'nativeName', 'code2', 'code3', 'status', 'ordering', 'createDate');

        $criteria->compare('id', $this->id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('nativeName', $this->nativeName, true);
        $criteria->compare('code2', $this->code2, true);
        $criteria->compare('code3', $this->code3, true);
        $criteria->compare('flagLink', $this->flagLink, true);
        $criteria->compare('status', $this->status);
        $criteria->compare('ordering', $this->asDefault);
        $criteria->compare('createDate', $this->createDate, true);
        $criteria->compare('asDefault', $this->asDefault);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'sort' => $sort,
            'pagination' => false,
        ));
    }

}
