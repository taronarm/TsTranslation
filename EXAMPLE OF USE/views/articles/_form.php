<?php
	/************************************/

	/**
	 * Add `TsLanguageWidget` with parameter `dynamicTranslate` in article create / update view file
	 * This is language selector (changer) witget, parameter `'dynamicTranslate' => true`
	 * 		- means that widget shows for dynamic content translation
	 */
	$this->widget('tstranslation.widgets.TsLanguageWidget', array(
		'dynamicTranslate' => true,
	));
	
	/************************************/
	
	/**
	 * The rest code as always
	 */
	
?>
