<?php

/**
 * @author Taron Saribekyan <saribekyantaron@gmail.com>
 * @copyright Copyright &copy; 2014, Taron Saribekyan. All rights reserved.
 * @license http://opensource.org/licenses/BSD-3-Clause The BSD 3-Clause License
 * @version 1.0.0
 */
class TranslatedMessages extends CActiveRecord {

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
        return Yii::app()->getComponent('messages')->translatedMessageTable; //'tsy_translated_messages';
    }

    public function rules() {
        return array(
            array('id, language', 'required'),
            array('id', 'numerical', 'integerOnly' => true),
            array('language', 'length', 'max' => 16),
            array('translation', 'safe'),
            array('id, language, translation', 'safe', 'on' => 'search'),
        );
    }

    public function relations() {
        return array(
            'sourceMessage' => array(self::BELONGS_TO, 'SourceMessages', 'id'),
        );
    }

    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'language' => 'Language',
            'translation' => 'Translation',
        );
    }

    public function search() {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('language', $this->language, true);
        $criteria->compare('translation', $this->translation, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

}
