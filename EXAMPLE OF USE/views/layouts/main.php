<?php
	/************************************/

	/**
	 * Add `TsLanguageWidget` in layout file
	 * This is language selector (changer) witget
	 */
	$this->widget('tstranslation.widgets.TsLanguageWidget', array(
		/**
		 * You can define `type` parameter which must be
		 * `dropdown` - languages shows as bootstrap dropdown
		 * `inline` - languages shows inline
		 */
		'type' => 'dropdown'
	));
	
	/************************************/
	
	/**
	 * The rest code as always
	 */

	echo $content;
	

?>