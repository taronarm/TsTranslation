<?php

/**
 * @author Taron Saribekyan <saribekyantaron@gmail.com>
 * @copyright Copyright &copy; 2014, Taron Saribekyan. All rights reserved.
 * @license http://opensource.org/licenses/BSD-3-Clause The BSD 3-Clause License
 * @version 1.0.0
 */
class AllLanguages extends CActiveRecord {

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
        return 'tss_all_languages';
    }

    public function rules() {
        return array(
            array('name, code2', 'required'),
            array('name, nativeName', 'length', 'max' => 50),
            array('code2', 'length', 'max' => 3),
            array('id, name, nativeName, code2', 'safe', 'on' => 'search'),
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
            'code2' => 'Language Code (2 symbol)',
        );
    }

    public function search() {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('nativeName', $this->nativeName, true);
        $criteria->compare('code2', $this->code2, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

}
