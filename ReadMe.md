TsTranslation
=============
    
   
Extension for Yii framework (version `1.1.*`).  
Easy to make Yii applications (web pages) multilanguage.
You can discuss extension in [TsTranslation topic](http://www.yiiframework.com/forum/index.php/topic/57076-tstranslation/)  

You can try **[Demo](http://tstranslation.sundevelop.com/)** of extension just now.  
 
Easy to install, easy to use, many functionality...
----

## Installation
* Download latest version of **[TsTranslation extension](https://github.com/TaronSaribekyan/TsTranslation/)** ,
* Unpack `tstranslatin-*.*.*.zip` to extension folder of your Yii project:  `.../protected/extensions/tstranslation`,
* **Import `tstranslation.sql` into your database**,
* Customize your configuration file (default `.../protected/config/main.php`):


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
** Now you can use it **

----

# Widgets

----

### - **TsTranslationWidget**

#### - *Public parameters*
    
    /**
     * force copy assets on every load, set to true in develop mode
     * @var bool
     */
    public $assetsForceCopy = false;
    
    /**
     * show or not dynamic translated content with tabs in widget,
     * default parameter is `false`, its required to not change this parameter
     * @var bool
     */
    public $showDynamicContent = false;
    
    /**
     * include or not bootstrap.js and bootstrap.css,
     * its required to set false if bootstrap.js already loaded in your page
     * @var bool
     */
    public $includeBootstrap = true;
    
    /**
     * load minified or source scripts
     * @var bool
     */
    public $minifyScripts = true;
    
    /**
     * type for TsGridView
     * @var string
     * @see CGrigview
     */
    public $type = 'striped bordered';
    
    /**
     * array of TsGridView html options
     * @var array
     * @see CGrigview
     */
    public $htmlOptions = array();
    
    /**
     * array of TsGridView options
     * @var array
     * @see CGrigview
     */
    public $listOptions = array(
                'ajaxUpdate' => true,
                'enableSorting' => true,
                'summaryText' => false,
            );

    /**
     * enable / disable buttons tooltip
     * @var bool
     */
    public $showTooltips = true;

#### - *Capabilities*

1. Add new language
2. Change language status (active/passive) to show language and language translations in frontend
3. Change languages ordering in frontend
4. Set default shows language
5. Translate messages to existing (added) languages and save
6. Translate message via google
7. Multiple translate messages via google
8. Multiple save translated messages
9. Capability to translate dynamic contents (news, blog articles, ...)

#### - *Usage*

**1. Calling widget**

        $this->widget('tstranslation.widgets.TsTranslationWidget', array(
            'includeBootstrap' => false, // if bootstrap.js loaded
            'showTooltips' => false, // if you want disable bootstrap tooltips
        ));
widget looks like this:  

![](http://tstranslation.sundevelop.com/images/tstranslation_demo/demo_1.PNG)

**2. Add new language**

To add new language click `Add language` button, select language and confirm action.  

![](http://tstranslation.sundevelop.com/images/tstranslation_demo/demo_6.PNG)

**3. Change language status**

To activate / disable language press `Change status` button.
> Recomended deactivate language before translate.

**4. Change default language**

Press `Make default` button and confirm action to set language as site default language.  

![](http://tstranslation.sundevelop.com/images/tstranslation_demo/demo_2.PNG)

**5. Translate messages**

To translate messages press `Translate` button.  
The categories tab will open automatically.  

![](http://tstranslation.sundevelop.com/images/tstranslation_demo/demo_5.PNG)  

If you set in widget `'showDynamicContent' => true`, the dynamic contents are shown  

![](http://tstranslation.sundevelop.com/images/tstranslation_demo/demo_3.PNG)  

Press category tab you want to translate and messages are shown   

![](http://tstranslation.sundevelop.com/images/tstranslation_demo/demo_4.PNG)  

To translate any message **click to message**, and *popup window* are shown  

![](http://tstranslation.sundevelop.com/images/tstranslation_demo/demo_7.PNG)  

There are 3 buttons in popup window:  

![](http://tstranslation.sundevelop.com/images/tstranslation_demo/demo_8.PNG)  

> `Save` button (`Ctrl + S` or `Ctrl + Enter`) - save message if it changed and open next message text in popup, if message not changed popup closes  
> `Translate` button (`Ctrl + G`) - **translate message via Google**  
> `Close` button - discard changes and close popup  
  
There are 2 buttons in every tab:  
  
![](http://tstranslation.sundevelop.com/images/tstranslation_demo/demo_9.PNG) 
  
> `Translate all` - translate all saved messages with google  
  
![](http://tstranslation.sundevelop.com/images/tstranslation_demo/demo_10.PNG) 
  
> `Save all` - save all unsaved messages (unsaved messages shown with **bold** font)  
  
**6. Remove language**

Press `Remove` button to remove language, then in prompt window enter `no` or `yes` to **remove** or **keep** language translated messages.

-----

### - **TsLanguageWidget**

#### - *Public parameters*

    /**
     * force copy assets on every load, set to true in develop mode
     * @var boll
     */
    public $assetsForceCopy = false;
    
    /**
     * widget template, requires {items} in string,
     * example: '<div id="languageChanger">{items}</div>'
     * @var string
     */
    public $template = '{items}';
    
    /**
     * template of language item
     * - {flag} shows flag
     * - {code} shows language code
     * - {name} shows name in English
     * - {nativeName} shows language native name
     * @var string
     */
    public $itemTemplate = '{flag} ({code}) {name} ({nativeName})';
    
    /**
     * display language content if only one language is available
     * @var bool
     */
    public $showIsOne = false;
    
    /**
     * set this parameter to true for dynamic content language selector,
     * for example in create and update views
     * @var bool
     */
    public $dynamicTranslate = false;
    
    /**
     * include or not bootstrap.js and bootstrap.css,
     * its required to set false if bootstrap.js already loaded in your page
     * @var bool
     */
    public $includeBootstrap = false;
    
    /**
     * load minified or source scripts
     * @var bool
     */
    public $minifyScripts = true;
    
    /**
     * tipe of widget view, available parameters are
     * - `dropdown` languages shows as bootstrap dropdown
     * - `inline` languages shows inline
     * @var string
     */
    public $type = 'inline';
    
#### - *Capabilities*

1. Show language changer in frontend
2. Show language changer for dynamic translation 
3. Two type of view: 'dropdown' and 'inline'

#### - *Usage*

1. For **dynamic translate** language selector:
    	$this->widget('tstranslation.widgets.TsLanguageWidget', array(
    		'dynamicTranslate' => true,
            'includeBootstrap' => false, // if in your project bootstrap.js loaded already
    	));

2. For **frontend language changer**:
    	$this->widget('tstranslation.widgets.TsLanguageWidget', array(
            'includeBootstrap' => false, // if you want to use 'dropdown' type in your project and bootstrap.js not loaded
            'type' => 'dropdown', // defaults uses `inline`
    	));

----

## Main available methods

----
    /**
     * Return array of active languages with its all attributes if $listAttribute is false / null,
     *      or langaugeCode => $listAttribute array
     * 
     * @param string $listAttribute
     * @return array or string
     */
    TsTranslationComponent::getActiveLanguages($listAttribute = 'name')
    
    /**
     * Return array of available (created) languages with its all attributes if $listAttribute is false / null,
     *      or langaugeCode => $listAttribute array
     * 
     * @param type $listAttribute
     * @return array
     */
    TsTranslationComponent::getAvailableLanguages($listAttribute = 'name')
    
    /**
     * Returns array of default language attributes, or attribute $attribute of default language
     * 
     * @param string $attribute
     * @return array or string
     */
    TsTranslationComponent::getDefaultLanguage($attribute = false)
    
    /**
     * Return dynamic content in gives language, `dt` means dynamic translate
     *      if $language is null uses value returned by TsTranslation::model()->getDtLanguage().
     * TsTranslation::model()->getDtLanguage() returns `dynamicTranslate` language,
     *      which select user by widget:
     *      <code>     
     *          $this->widget('tstranslation.widgets.TsLanguageWidget', array(
     *              'dynamicTranslate' => true,
     *          ));
     *      </code>
     * Example of use:
     * <code>
     *      $model = Articles::findByPk($_GET['id']);
     *      $model->attributes = TsTranslation::dt($model, array('title', 'introText', 'fullText'));
     * </code>
     * 
     * @param CModel or string $categoryOrModel
     * @param string or array $messageOrAttribute
     * @param string $language
     * @return array or string
     * @throws CHttpException if user have not permission
     * @throws TsTranslationException if CModel $categoryOrModel have not attribute $messageOrAttribute
     */
    TsTranslation::dt($categoryOrModel, $messageOrAttribute, $language = null)
    
    /**
     * Save dynamic content in $language language,
     *      if $language is null uses value returned by TsTranslation::model()->getDtLanguage().
     * TsTranslation::model()->getDtLanguage() returns `dynamicTranslate` language,
     *      which select user by widget:
     *      <code>     
     *          $this->widget('tstranslation.widgets.TsLanguageWidget', array(
     *              'dynamicTranslate' => true,
     *          ));
     *      </code>
     * Example of use:
     * <code>
     *  if(isset($_POST['Articles'])) {
     *      $model->attributes = $_POST['Articles'];
     *      if($model->save()) {
     *           TsTranslation::save($model, array('title', 'introText', 'fullText'));
     *      }
     *  }
     * </code>
     *  
     * @param CModel or string $categoryOrModel
     * @param string or array $messageOrAttribute
     * @param string $language
     * @throws CHttpException if user have not permission
     * @throws TsTranslationException if CModel $categoryOrModel have not attribute $messageOrAttribute
     */
    TsTranslation::save($categoryOrModel, $messageOrAttribute, $language = null)
    
    /**
     * Return dynamic content save language, usage:
     *
     * @return string
     */
    TsTranslation::model()->getDtLanguage()
    
----

# Example of usage

----
> Suppose we have table `articles` in our application and his model `Articles`.  
> As default table `articles` containes these columns: `id, createDate, updateDate, author,  title, introText, fullText`.  
> If we use **TsTranslation** extensions, the columns which must be multilanguage can apcent from table, it means that table `articles` can contain only `id, createDate, updateDate, author` columns.  

Lets go to example of classes.  

* model **Articles**

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

----

* controller **ArticlesController**

        <?php
        
        class ArticlesController extends Controller
        {
        
        	/*************************************/	
        
            public function actionView($id) {
                $model = $this->loadModel($id);
                
                $this->render('view', array(
                    'model' => $model,
                ));
            }
        
            public function actionCreate() {
                $model = new Articles;
                
                if(isset($_POST['Articles'])) {
                    $model->attributes = $_POST['Articles'];
                    if($model->save()) {
                        /*
                         * Save attributes at once
                         * Also can save one at one. Example: TsTranslation::save($model, 'fullText');
                         * Third parameter of TsTranslation::save() is language 
                         *      - as default uses TsTranslation::model()->getDtLanguage() returned value:
                         *      - $this->widget('tstranslation.widgets.TsLanguageWidget', array(
                                    'dynamicTranslate' => true,
                                ));
                         */
                        TsTranslation::save($model, array('title', 'introText', 'fullText'));
                    }
                    $this->redirect(array('update','id' => $model->id));
                }
        
                $this->render('create', array(
                    'model' => $model,
                ));
            }
        
            public function actionUpdate($id) {
                
                $model = $this->loadModel($id);
                
                if(isset($_POST['Articles'])) {
                    $model->attributes = $_POST['Articles'];
                    if($model->save()) {
                        /*
                         * Save attributes at once
                         * Also can save one by one. Example: TsTranslation::save($model, 'fullText');
                         * Third parameter of TsTranslation::save() is language 
                         *      - as default uses TsTranslation::model()->getDtLanguage() returned value:
                         *      - $this->widget('tstranslation.widgets.TsLanguageWidget', array(
                                    'dynamicTranslate' => true,
                                ));
                         */
                        TsTranslation::save($model, array('title', 'introText', 'fullText'));
                    }
                }
                /**
                 * Get attributes translations at once
                 * Also can get translation one by one. Example: TsTranslation::dt($model, 'fullText');
                 * Third parameter of TsTranslation::dt() is language 
                 *      - as default uses TsTranslation::getDtLanguage() returned value:
                 *      - $this->widget('tstranslation.widgets.TsLanguageWidget', array(
                            'dynamicTranslate' => true,
                        ));
                 */
                $model->attributes = TsTranslation::dt($model, array('title', 'introText', 'fullText'));
        
                $this->render('update', array(
                    'model' => $model,
                ));
            }
        	
        	/*************************************/
        
        }
        
----

* view file for `actionUpdate` **update.php**

        <?php
        	/************************************/
        	
        	/**
             * Menu or breadcrumbs or other code
             */
        	 
        	/************************************/
        ?>
        <h1>Update Articles <?php echo $model->id; ?></h1>
        
        <?php echo $this->renderPartial('_form', array('model' => $model)); ?>

----

* view file for `actionCreate` **create.php**

        <?php
        	/************************************/
        	
        	/**
             * Menu or breadcrumbs or other code
             */
        	 
        	/************************************/
        ?>
        <h1>Create Articles</h1>
        
        <?php echo $this->renderPartial('_form', array('model'=>$model)); ?>

----

* view file **_form.php**

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
----

* view file for `actionView` **view.php**

        <h1>Article <?php echo Yii::t($model, 'title'); ?></h1>
        <div class="article-info" style="float:right;">
            <strong>Author: <?php echo Yii::t($model, 'author'); ?></strong>
            <i>Create date: <?php echo Yii::t($model, 'createDate'); ?></i>
            <i>Last update: <?php echo Yii::t($model, 'updateDate'); ?></i>
        </div>
        <div class="intro-text">
            <p><?php echo Yii::t($model, 'introText'); ?></p>
        </div>
        <div class="full-text">
            <p><?php echo Yii::t($model, 'fullText'); ?></p>
        </div>

----

* layout file **main.php**

        <?php
        	/************************************/
        
        	/**
        	 * Add `TsLanguageWidget` in layout file
        	 * This is language selector (changer) witget
        	 */
        	$this->widget('tstranslation.widgets.TsLanguageWidget');
        	
        	/************************************/
        	
        	/**
        	 * The rest code as always
        	 */
        
        	echo $content;
        	
        ?>
