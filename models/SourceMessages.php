<?php

/**
 * @author Taron Saribekyan <saribekyantaron@gmail.com>
 * @copyright Copyright &copy; 2014, Taron Saribekyan. All rights reserved.
 * @license http://opensource.org/licenses/BSD-3-Clause The BSD 3-Clause License
 * @version 1.0.0
 */
class SourceMessages extends CActiveRecord {

    public $language;
    public $translation;

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
        return Yii::app()->getComponent('messages')->sourceMessageTable; //'tsy_source_messages';
    }

    public function rules() {
        return array(
            array('category', 'length', 'max' => 256),
            array('message, translation', 'safe'),
            array('id, category, message, translation', 'safe', 'on' => 'search'),
        );
    }

    public function relations() {
        return array(
            'translatedMessages' => array(self::HAS_MANY, 'TranslatedMessages', 'id'),
        );
    }

    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'category' => 'Category',
            'message' => 'Message',
        );
    }

    public function search() {
        $criteria = new CDbCriteria;
//      $criteria->with = array('translatedMessages');

        $criteria->compare('id', $this->id);
        $criteria->compare('category', $this->category);
        $criteria->compare('message', $this->message);
//		$criteria->compare('translatedMessages.language',$this->language);
//		$criteria->compare('translatedMessages.translation',$this->translation);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => false,
        ));
    }

}
