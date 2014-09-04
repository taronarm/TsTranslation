<?php
	
	/******************************/
	
	return array(
	
		/******************************/
		
		/**
		 * Add `tstranslation` to preload
		 */
		'preload' => array('log','tstranslation'),
		
		/**
		 * Add controller map for `tstranslation`
		 */
		'controllerMap' => array(
			'tstranslation' => 'tstranslation.controllers.TsTranslationController'
		),
		
		/****************************/

		'components'=>array(
			
			/******************************/
		
			/**
			 * Add `tstranslation` component
			 */
			'tstranslation'=>array(
				/**
				* Set `tstranslation` class
				*/
				'class' => 'ext.tstranslation.components.TsTranslation',
				
				/**
				 * Set `accessRules` parameter (NOT REQUIRED),
				 * parameter effects to dynamic content translation and language managment
				 *
				 * AVAILABLE VALUES:
				 * - '*' means all users
				 * - '@' means all registered users
				 * - `username`. Example: 'admin' means Yii::app()->user->name === 'admin'
				 * - `array of usernames`. Example: array('admin', 'manager') means in_array(array('admin', 'manager'), Yii::app()->user->name)
				 * - your custom expression. Example: array('expression' => 'Yii::app()->user->role === "admin"')
				 * DEFAULT VALUE: '@'
				*/
				'accessRules' => '@',
				
				/**
				 * Set `languageChangeFunction` (NOT REQUIRED),
				 * function processing language change
				 *
				 * AVAILABLE VALUES:
				 * - `true` means uses extension internal function (RECOMENDED)
				 * - `array()` means user defined function. Example: array('TestClass', 'testMethod'), 'TestClass' and 'testMethod' must be exist and imported to project
				 * DEFAULT VALUE: `true`
				*/
				'languageChangeFunction' => true,
			),
			
			/******************************/
			
			'urlManager' => array(
				/**
				* Set `urlManager` class
				*/
				'class' => 'TsUrlManager',
				
				/**
				 * Set `showLangInUrl` parameter (NOT REQUIRED),
				 *
				 * AVAILABLE VALUES:
				 * - `true` means language code shows in url. Example: .../mysite/en/article/create
				 * - `false` means language code not shows in url. Example: .../mysite/article/create
				 * DEFAULT VALUE: `true`
				*/
				'showLangInUrl' => true,
				
				/**
				 * Set `prependLangRules` parameter (NOT REQUIRED),
				 * this parameter takes effect only if `showLangInUrl` parameter is `true`.
				 * It strongly recomended to add language rule to `rules` parameter handly
				 *
				 * AVAILABLES VALUES:
				 * - `true` means automaticly prepends `_lang` parameter before all rules.
				 *      Example: '<_lang:\w+><controller:\w+>/<id:\d+>' => '<controller>/view',
				 * - `false` means `_lang` parameter you must add handly
				 * DEFAULT VALUE: `true`
				*/
				'prependLangRules' => true,
				
				/******************************/
			),
			/******************************/
		
			/**
			 * Add `messages` component
			 */
			'messages' => array(
				/**
				* Set `messages` class
				*/
				'class' => 'TsDbMessageSource',
				
				/**
				* Set `Missing Messages` translation action
				*/
				'onMissingTranslation' => array('TsTranslation', 'addTranslation'),
				
				/**
				 * Set `notTranslatedMessage` parameter (NOT REQUIRED),
				 *
				 * AVAILABLE VALUES:
				 * - `false / null` means nothing shows if message translation is empty
				 * - `text` means shows defined text if message translation is empty.
				 *      Example: 'Not translated data!'
				 * DEFAULT VALUE: `null`
				*/
				'notTranslatedMessage' => 'Not translated data!',
                
				/**
				 * Set `ifNotTranslatedShowDefault` parameter (NOT REQUIRED),
				 *
				 * AVAILABLE VALUES:
				 * - `false` means shows `$this->notTranslatedMessage` if message translation is empty
				 * - `true` means shows default language translation if message translation is empty.
				 * DEFAULT VALUE: `true`
				*/
				'ifNotTranslatedShowDefault' => false,
				
			),

		), /********** END OF COMPONENTS ********************/
		
		/**
		* Set `language` and `sourceLanguage` (NOT REQUIRED)
		*/
		'language' => 'en',
		'sourceLanguage' => 'en',
		
		/******************************/
		
	);