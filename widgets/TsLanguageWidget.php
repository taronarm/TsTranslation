<?php

/**
 * @author Taron Saribekyan <saribekyantaron@gmail.com>
 * @copyright Copyright &copy; 2014, Taron Saribekyan. All rights reserved.
 * @license http://opensource.org/licenses/BSD-3-Clause The BSD 3-Clause License
 * @version 1.0.0
 */
class TsLanguageWidget extends CWidget {

    private $scriptPosition = CClientScript::POS_HEAD;
    public $id;

    /**
     * force copy assets on every load, set to true in develop mode
     * @var bool
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
    private $_minifySuffix = '';
    private $_id;

    public function init() {
        if ($this->id) {
            $this->_id = $this->id;
        } else {
            $this->_id = $this->getId();
        }
        if ($this->minifyScripts) {
            $this->_minifySuffix = '.min';
        }
        if ($this->type != 'dropdown' && $this->type != 'inline') {
            $this->type = 'dropdown';
        }
        parent::init();
    }

    protected function registerClientScript() {
        Yii::app()->getClientScript()->registerCoreScript('jquery.ui');
        $assetsUrl = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('tstranslation.assets'), false, -1, $this->assetsForceCopy);
        $cs = Yii::app()->getClientScript();

        if (!($cs->isScriptFileRegistered($assetsUrl . '/js/tstranslation.js', CClientScript::POS_END) || $cs->isScriptFileRegistered($assetsUrl . '/js/tstranslation.min.js', CClientScript::POS_END))) {
            if ($this->includeBootstrap) {
                $cs->registerCssFile($assetsUrl . '/css/bootstrap' . $this->_minifySuffix . '.css');
                $cs->registerCssFile($assetsUrl . '/css/bootstrap-yii' . $this->_minifySuffix . '.css');
                $cs->registerScriptFile($assetsUrl . '/js/bootstrap' . $this->_minifySuffix . '.js', $this->scriptPosition);
            }
            $cs->registerCssFile($assetsUrl . '/css/tstranslation' . $this->_minifySuffix . '.css');
            $cs->registerScriptFile($assetsUrl . '/js/tstranslation' . $this->_minifySuffix . '.js', CClientScript::POS_END);
        }
        return $assetsUrl;
    }

    public function run() {
        if ($this->dynamicTranslate) {
            if (!TsTranslation::model()->isAccessEnabled()) {
                throw new CHttpException(403, 'You have no permission to use dynamic content save method!');
            }
            $languageArray = TsTranslationComponent::getAvailableLanguages(false);
            if (!$this->showIsOne && count($languageArray) <= 1) {
                return;
            }
            $assetsUrl = $this->registerClientScript();
            $templateArray = explode('{items}', $this->template);
            $params = $_GET;
            $items = '';
            $currentLang = '';
            $dtLang = TsTranslation::model()->getDtLanguage();
            foreach ($languageArray as $lang) {
                if ($lang->code2 == $dtLang) {
                    $currentLang = '<span class="ts-current-dt-lang btn" data-toggle="' . $this->type . '" href="#">' .
                            strtr($this->itemTemplate, array(
                                '{flag}' => '<img src="' . $assetsUrl . '/images/flags/' . $lang->code2 . '.gif">',
                                '{name}' => $lang->name,
                                '{nativeName}' => $lang->nativeName,
                                '{code}' => $lang->code2,
                            )) . '</span>';
                } else {
                    $item = strtr($this->itemTemplate, array(
                        '{flag}' => '<img src="' . $assetsUrl . '/images/flags/' . $lang->code2 . '.gif">',
                        '{name}' => $lang->name,
                        '{nativeName}' => $lang->nativeName,
                        '{code}' => $lang->code2,
                    ));
                    $params['_dtLang'] = $lang->code2;
                    $items .= '<li><a data-language="' . $lang->code2 . '" href="' . Yii::app()->createUrl(Yii::app()->controller->route, $params) . '">' . $item . '</a></li>';
                }
            }
            echo $templateArray[0] . '<div id="' . $this->_id . '" class="' . $this->type . ' ts-dt-language-widget">' . $currentLang . '<ul class="' . $this->type . '-menu ts-lang-changer-list">' . $items . '</ul></div>' . $templateArray[1];
        } else {
            $languageArray = TsTranslationComponent::getActiveLanguages(false);
            if (!$this->showIsOne && count($languageArray) <= 1) {
                return;
            }
            $assetsUrl = $this->registerClientScript();
            $urlManager = Yii::app()->getComponent('urlManager');
            $templateArray = explode('{items}', $this->template);
            $params = $_GET;
            $items = '';
            $currentLang = '<span class="ts-current-dt-lang btn" data-toggle="' . $this->type . '" href="#"><img src="' . $assetsUrl . '/images/flags/' . Yii::app()->language . '.gif"> ' . Yii::app()->language . ' (This language not active, please select other language)</span>';
            foreach ($languageArray as $lang) {
                $item = strtr($this->itemTemplate, array(
                    '{flag}' => '<img src="' . $assetsUrl . '/images/flags/' . $lang->code2 . '.gif">',
                    '{name}' => $lang->name,
                    '{nativeName}' => $lang->nativeName,
                    '{code}' => $lang->code2,
                ));
                if ($lang->code2 == Yii::app()->language) {
                    $currentLang = '<span class="ts-current-lang btn" data-toggle="' . $this->type . '" href="#">' .
                            strtr($this->itemTemplate, array(
                                '{flag}' => '<img src="' . $assetsUrl . '/images/flags/' . $lang->code2 . '.gif">',
                                '{name}' => $lang->name,
                                '{nativeName}' => $lang->nativeName,
                                '{code}' => $lang->code2,
                            )) . '</span>';
                    $items .= '<li><a data-language="' . $lang->code2 . '" href="#" class="ts-current-lang-link" onclick="javascript:return false;">' . $item . '</a></li>';
                } else {
                    $params['_lang'] = $lang->code2;
                    $items .= '<li><a data-language="' . $lang->code2 . '" href="' . Yii::app()->createUrl(Yii::app()->controller->route, $params) . '">' . $item . '</a></li>';
                }
            }
            if ($this->type === 'inline') {
                $currentLang = '';
            }
            if ($urlManager->showLangInUrl) {
                echo $templateArray[0] . '<div id="' . $this->_id . '" class="' . $this->type . ' ts-language-widget">' . $currentLang . '<ul class="' . $this->type . '-menu ts-lang-changer-list">' . $items . '</ul></div>' . $templateArray[1];
            } else {
                echo $templateArray[0] . '<div id="' . $this->_id . '" class="' . $this->type . ' ts-language-widget"><form id="tsLangChangerForm" method="POST"><input type="hidden" name="_newLang" id="tsNewLang" value="">' . $currentLang . '<ul class="' . $this->type . '-menu ts-lang-changer-list">' . $items . '</ul></form></div>' . $templateArray[1];
            }
        }
    }

}
