<?php
class Articles extends CActiveRecord
{
	/************************************/
	
    public $title;
    public $introText;
    public $fullText;
    public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
	public function tableName()
	{
		return 'tbl_articles';
	}

	public function rules()
	{
		/**
		 * Add rules for dynamic translation attributes `title`, `introText`, `fullText`
		 */
		return array(
			array('title, introText, fullText, image, createDate', 'safe'),
			array('id, image, title, introText, fullText, createDate', 'safe', 'on'=>'search'),
		);
	}
	
	/************************************/
	
	/**
	 * The rest code as always
	 */
	
}