<?php

/**
 * @author Taron Saribekyan <saribekyantaron@gmail.com>
 * @copyright Copyright &copy; 2014, Taron Saribekyan. All rights reserved.
 * @license http://opensource.org/licenses/BSD-3-Clause The BSD 3-Clause License
 * @version 1.0.0
 */

/**
 * ## TsTranslation application component.
 *
 * This is the main TsTranslation component which you should attach to your Yii CWebApplication instance.
 *
 * Almost all configuration options are meaningful only at the initialization time,
 * changing them after `TsTranslation` was attached to application will have no effect.
 *
 * @package tstranslation.components
 */
class TsTranslation extends CApplicationComponent {

    /**
     * access rule for `TsTranslationController` controller actions
     * and `TsTranslationWidget`
     * @var string
     */
    public $accessRules = '@';

    /**
     * 
     * @var bool or array
     */
    public $languageChangeFunction = true;
    private static $_instance = null;

    public function init() {
        self::setTsTranslation($this);
        $this->setRootAliasIfUndefined();

        Yii::app()->setImport(array(
            'tstranslation.models.*',
            'tstranslation.components.*',
        ));
        $this->setTsLanguage();

        parent::init();
    }

    /**
     * Set language of Yii application
     * @see Yii::app()->language
     */
    public function setTsLanguage() {
        if ($this->languageChangeFunction === true) {
            /**
             * Init Url Manager and parsing url to make GET params
             */
            $this->_initGetParams();

            $newLang = null;
            if (isset(Yii::app()->request->cookies['_lang']) && !empty(Yii::app()->request->cookies['_lang']->value)) {
                $sessionLang = Yii::app()->request->cookies['_lang']->value;
            } else {
                $sessionLang = null;
            }
            $_lang = $sessionLang;
            $requestLang = isset($_GET['_lang']) ? $_GET['_lang'] : null;

            if (isset($_GET['_newLang'])) {
                $newLang = $_GET['_newLang'];
            } elseif (isset($_POST['_newLang'])) {
                $newLang = $_POST['_newLang'];
            }

            if ($newLang !== null) {
                if ($newLang !== $sessionLang) {
                    $_lang = $this->_getExistedLanguage($newLang);
                }
            } elseif ($requestLang !== null) {
                if ($requestLang !== $sessionLang) {
                    $_lang = $this->_getExistedLanguage($requestLang);
                }
            } elseif ($_lang === null) {
                $_lang = TsTranslationComponent::getDefaultLanguage('code2');
            }
            Yii::app()->request->cookies['_lang'] = new CHttpCookie('_lang', $_lang, array(
                'expire' => time() + 60 * 60 * 24 * 30, // 1 month
                'path' => '/',
            ));
            $_GET['_lang'] = $_lang;
            Yii::app()->setLanguage($_lang);
            /**
             * Disable form submit confirmation on page refresh
             */
            if (isset($_POST['_newLang'])) {
                unset($_POST['_newLang']);
                Yii::app()->request->redirect(Yii::app()->request->url);
            }
        } elseif (is_array($this->languageChangeFunction)) {
            call_user_func_array($this->languageChangeFunction, array());
        }
    }

    /**
     * Return dynamic content save language, usage:
     * <code>
     *      TsTranslation::model()->getDtLanguage();
     * </code>
     * 
     * @param string $dtVar
     * @return string
     */
    public function getDtLanguage($dtVar = '_dtLang') {
        if (isset($_GET[$dtVar])) {
            return $this->_getExistedLanguage($_GET[$dtVar], false);
        } else {
            return TsTranslationComponent::getDefaultLanguage('code2');
        }
    }

    private function _initGetParams() {
        if (!isset($_GET['_lang'])) {
            Yii::app()->getUrlManager()->parseUrl(Yii::app()->request);
        }
    }

    private function _getExistedLanguage($langCode, $onlyActive = true) {
        if ($onlyActive === true) {
            $langList = TsTranslationComponent::getActiveLanguages('asDefault');
        } else {
            $langList = TsTranslationComponent::getAvailableLanguages('asDefault');
        }

        if (isset($langList[$langCode])) {
            return $langCode;
        } else {
            return array_search('1', $langList);
        }
    }

    public static function setTsTranslation($value) {
        if ($value instanceof TsTranslation) {
            self::$_instance = $value;
        }
    }

    public static function getTsTranslation() {
        if (self::$_instance === null) {
            if (Yii::app()->hasComponent('tstranslation')) {
                self::$_instance = Yii::app()->getComponent('tstranslation');
            }
        }
        return self::$_instance;
    }

    public static function model($className = __CLASS__) {
        return self::getTsTranslation() !== null ? self::getTsTranslation() : new $className;
    }

    protected function setRootAliasIfUndefined() {
        if (Yii::getPathOfAlias('tstranslation') === false) {
            Yii::setPathOfAlias('tstranslation', realpath(dirname(__FILE__) . '/..'));
        }
    }

    public function isAccessEnabled() {
        $ruleOption = $this->accessRules;
        if (is_string($ruleOption)) {
            switch ($ruleOption) {
                case '*':
                    return true;
                    break;
                case '@':
                    return !(Yii::app()->user->isGuest);
                    break;
                default:
                    return Yii::app()->user->name === $ruleOption;
                    break;
            }
        } elseif (is_array($ruleOption)) {
            if (isset($ruleOption['expression'])) {
                return eval('return ' . $ruleOption['expression'] . ';');
            } else {
                return in_array(Yii::app()->user->name, $ruleOption);
            }
        } elseif (is_bool($ruleOption)) {
            return $ruleOption;
        } else {
            return false;
        }
    }

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
     *      $model = Article::findByPk($_GET['id']);
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
    public static function dt($categoryOrModel, $messageOrAttribute, $language = null) {
        if (!self::model()->isAccessEnabled()) {
            throw new CHttpException(403, 'You have no permission to use dynamic content translation method!');
        }
        $category = $categoryOrModel;
        $message = $messageOrAttribute;

        if ($language === null) {
            $language = self::model()->getDtLanguage();
        } elseif ($language === 'default') {
            $language = TsTranslationComponent::getDefaultLanguage('code2');
        }
        if (is_object($categoryOrModel)) {
            if (is_array($messageOrAttribute)) {
                if ($categoryOrModel->isNewRecord || $categoryOrModel->getPrimaryKey() === null) {
                    return array_fill_keys($messageOrAttribute, null);
                }
                $attribute = array();
                foreach ($messageOrAttribute as $m) {
                    if ($categoryOrModel->hasAttribute($m) || property_exists($categoryOrModel, $m)) {
                        $attribute[$m] = Yii::t($categoryOrModel, $m, array(), null, $language);
                    } else {
                        throw new TsTranslationException('The model ' . get_class($categoryOrModel) . ' have not attribute ' . $m);
                    }
                }
                return $attribute;
            } else {
                if ($categoryOrModel->hasAttribute($messageOrAttribute) || property_exists($categoryOrModel, $messageOrAttribute)) {
                    return Yii::t($categoryOrModel, $messageOrAttribute, array(), null, $language);
                } else {
                    throw new TsTranslationException('The model ' . get_class($categoryOrModel) . ' have not attribute ' . $messageOrAttribute);
                }
            }
        } else {
            return Yii::t($categoryOrModel, $messageOrAttribute, array(), null, $language);
        }
    }

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
    public static function save($categoryOrModel, $messageOrAttribute, $language = null) {
        if (!self::model()->isAccessEnabled()) {
            throw new CHttpException(403, 'You have no permission to use dynamic content save method!');
        }
        $category = $categoryOrModel;
        $message = $messageOrAttribute;
        if ($language === null) {
            $language = self::model()->getDtLanguage();
        } elseif ($language === 'default') {
            $language = TsTranslationComponent::getDefaultLanguage('code2');
        }
        if (is_object($categoryOrModel)) {
            if ($categoryOrModel->isNewRecord || $categoryOrModel->getPrimaryKey() === null) {
                throw new TsTranslationException('The primary key of model ' . get_class($categoryOrModel) . ' is empty. Perhaps you call function TsTranslation::dt() before saving model');
            }
            if (is_array($messageOrAttribute)) {
                foreach ($messageOrAttribute as $m) {
                    if ($categoryOrModel->hasAttribute($m) || property_exists($categoryOrModel, $m)) {
                        $category = '#.' . get_class($categoryOrModel) . '-' . $m . '.' . $categoryOrModel->getPrimaryKey();
                        $message = $categoryOrModel->$m;
                        self::model()->_save($category, $message, $language);
                    } else {
                        throw new TsTranslationException('The model ' . get_class($categoryOrModel) . ' have not attribute ' . $m);
                    }
                }
            } else {
                if ($categoryOrModel->hasAttribute($messageOrAttribute) || property_exists($categoryOrModel, $messageOrAttribute)) {
                    $category = '#.' . get_class($categoryOrModel) . '-' . $messageOrAttribute . '.' . $categoryOrModel->getPrimaryKey();
                    $message = $categoryOrModel->$messageOrAttribute;
                    self::model()->_save($category, $message, $language);
                } else {
                    throw new TsTranslationException('The model ' . get_class($categoryOrModel) . ' have not attribute ' . $messageOrAttribute);
                }
            }
        } else {
            if (!is_string($category)) {
                throw new TsTranslationException('The first parameter of TsTranslation::save() method must be CActiveRecord model or string');
            }
            if (!is_string($message)) {
                throw new TsTranslationException('If the first parameter of TsTranslation::save() method is string, the second parameter also must be string');
            }
            $event = new CMissingTranslationEvent(self::getTsTranslation(), $category, $message, $language);
            self::addTranslation($event);
        }
    }

    /**
     * Private function for saving dynamic content
     * 
     * @param string $category
     * @param string $message
     * @param string $language
     */
    private function _save($category, $message, $language) {
        $source = SourceMessages::model()->findByAttributes(array('category' => $category, 'message' => null));
        if ($source === NULL) {
            $source = new SourceMessages();
            $source->category = $category;
            $source->message = null;
            $source->save();
        }

        $translation = TranslatedMessages::model()->findByPk(array('id' => $source->id, 'language' => $language));
        if ($translation !== NULL && $translation->translation !== $message) {
            $translation->translation = $message;
            $translation->update();
        } elseif ($translation === NULL) {
            $translation = new TranslatedMessages();
            $translation->id = $source->id;
            $translation->language = $language;
            $translation->translation = $message;
            $translation->insert();
        }
    }

    /**
     * Delete Source and Translated messages by category or model
     * 
     * Example of use:
     *  With Model instanceof CModel
     *  <code>
     *      if(isset($_POST['id'])) {
     *          $model = Articles::model()->findByPk($_POST['id']);
     *          if($model->delete()) {
     *              // Delete $model all translated messages
     *              TsTranslation::delete($model);
     *              // OR only title
     *              TsTranslation::delete($model, 'title');
     *              // OR title and introText
     *              TsTranslation::delete($model, array('title', 'introText'));
     *          }
     *      }
     *  </code>
     * 
     *  With category and message in source code
     *  <code>
     *      // Delete all messages in "Default" category
     *      TsTranslation::delete("Default");
     *      // Delete concrete message in "Default" category
     *      TsTranslation::delete("Default", 'Life is good');
     *  </code>
     * 
     * @param CModel or string $categoryOrModel
     * @param string or array $messageOrAttribute
     * @throws CHttpException if user have not permission
     * @since 1.1.0
     */
    public static function delete($categoryOrModel, $messageOrAttribute = null) {
        if (!self::model()->isAccessEnabled()) {
            throw new CHttpException(403, 'You have no permission to use dynamic content delete method!');
        }
        $category = $categoryOrModel;
        $message = $messageOrAttribute;
        $getIdsQuery = '';
        $connectionID = Yii::app()->getComponent('messages')->connectionID;
        if (is_object($categoryOrModel)) {
            if ($messageOrAttribute === null) {
                $getIdsQuery = 'SELECT `id` FROM `' . SourceMessages::model()->tableName() . '` WHERE `category` REGEXP "^#[.]{1}' . get_class($categoryOrModel) . '-.*.\\.' . $categoryOrModel->getPrimaryKey() . '$"';
            } else {
                if (is_array($messageOrAttribute) && !empty($messageOrAttribute)) {
                    $tmpCondition = array();
                    foreach ($messageOrAttribute as $attr) {
                        $tmpCondition[] = '"#.' . get_class($categoryOrModel) . '-' . $attr . '.' . $categoryOrModel->getPrimaryKey() . '"';
                    }
                    $getIdsQuery = 'SELECT `id` FROM `' . SourceMessages::model()->tableName() . '` WHERE `category` IN (' . implode(',', $tmpCondition) . ')';
                } else {
                    $getIdsQuery = 'SELECT `id` FROM `' . SourceMessages::model()->tableName() . '` WHERE `category`="#.' . get_class($categoryOrModel) . '-' . $messageOrAttribute . '.' . $categoryOrModel->getPrimaryKey() . '"';
                }
            }
        } else {
            if ($messageOrAttribute === null) {
                $getIdsQuery = 'SELECT `id` FROM `' . SourceMessages::model()->tableName() . '` WHERE `category`="' . $categoryOrModel . '"';
            } else {
                if (is_array($messageOrAttribute) && !empty($messageOrAttribute)) {
                    $getIdsQuery = 'SELECT `id` FROM `' . SourceMessages::model()->tableName() . '` WHERE `category`="' . $categoryOrModel . '" AND `message` IN (' . implode(',', $messageOrAttribute) . ')';
                } else {
                    $getIdsQuery = 'SELECT `id` FROM `' . SourceMessages::model()->tableName() . '` WHERE `category`="' . $categoryOrModel . '" AND `message`="' . $messageOrAttribute . '"';
                }
            }
        }
        $deletingIds = !empty($getIdsQuery) ? Yii::app()->$connectionID->createCommand($getIdsQuery)->queryColumn() : null;

        if (!empty($deletingIds)) {
            $sourceDeleteQuery = 'DELETE FROM `' . SourceMessages::model()->tableName() . '` WHERE `id` IN (' . implode(',', $deletingIds) . ')';
            $translatedDeleteQuery = 'DELETE FROM `' . TranslatedMessages::model()->tableName() . '` WHERE `id` IN (' . implode(',', $deletingIds) . ')';
            /**
             * If Source Message deletion failed due to Translated Messages foreign key, at first deletes translated messages
             */
            try {
                Yii::app()->$connectionID->createCommand($sourceDeleteQuery)->execute();
                Yii::app()->$connectionID->createCommand($translatedDeleteQuery)->execute();
            } catch (CDbException $e) {
                Yii::app()->$connectionID->createCommand($translatedDeleteQuery)->execute();
                Yii::app()->$connectionID->createCommand($sourceDeleteQuery)->execute();
            }
        }
    }

    public static function addTranslation($event) {
        if ($event->category === null) {
            $event->category = '';
            if (Yii::app()->controller->module !== null) {
                $event->category .= Yii::app()->controller->module->id . '.';
            } else {
                $event->category .= 'root.';
            }
            $event->category .= Yii::app()->controller->id . '.' . Yii::app()->controller->action->id;
        }
        $source = SourceMessages::model()->findByAttributes(array('message' => $event->message, 'category' => $event->category));
        //var_dump($event->message,  $event->category);die;
        if ($source === NULL) {
            $source = new SourceMessages();
            $source->category = $event->category;
            $source->message = $event->message;
            $source->save();
        }

        $translation = TranslatedMessages::model()->findByPk(array('id' => $source->id, 'language' => $event->language));

        if ($translation === NULL) {
            $translation = new TranslatedMessages();
            $translation->id = $source->id;
            $translation->language = $event->language;
            $translation->translation = $event->message;
            $translation->save();
        }
    }

}
