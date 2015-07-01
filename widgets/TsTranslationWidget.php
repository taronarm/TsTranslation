<?php

/**
 * @author Taron Saribekyan <saribekyantaron@gmail.com>
 * @copyright Copyright &copy; 2014, Taron Saribekyan. All rights reserved.
 * @license http://opensource.org/licenses/BSD-3-Clause The BSD 3-Clause License
 * @version 1.0.0
 */
class TsTranslationWidget extends CWidget {

    private $scriptPosition = CClientScript::POS_HEAD;

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
    private $_minifySuffix = '';
    private $_id;

    public function init() {
        if (isset($this->htmlOptions['id'])) {
            $this->_id = $this->htmlOptions['id'];
            unset($this->htmlOptions['id']);
        } else {
            $this->_id = $this->getId();
        }
        if ($this->listOptions['ajaxUpdate'] === true) {
            $this->listOptions['beforeAjaxUpdate'] = 'function(id, options){
                        var activeLanguage = $(".ts-translate.active").attr("data-language");
                        $("#currentActiveLanguage").val(activeLanguage);
                    }';
            $this->listOptions['afterAjaxUpdate'] = 'function(id, data){
                        var activeLanguage = $("#currentActiveLanguage").val();
                        $(".ts-translate[data-language="+activeLanguage+"]").addClass("active");
                        $(".ts-translate[data-language="+activeLanguage+"] i").addClass("glyphicon-check");
                        $(".tstranslation-list table tbody").sortable();
                        $(".tstranslation-list table tbody").disableSelection();
                        orderCount = 0;
                    }';
        }
        if ($this->minifyScripts) {
            $this->_minifySuffix = '.min';
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
        if ($this->showTooltips && !$cs->isScriptRegistered('TsShowTooltips')) {
            $cs->registerScript('TsShowTooltips', '$(document).on("hover.bs.tab.data-api", \'[data-tstoggle="tooltip"]\', function (e) {
                e.preventDefault();
                $(this).tooltip("show");
            })', CClientScript::POS_END);
        }

        $cs->registerCssFile($assetsUrl . '/css/bootstrap-editable' . $this->_minifySuffix . '.css');
        $cs->registerScriptFile($assetsUrl . '/js/bootstrap-editable' . $this->_minifySuffix . '.js', $this->scriptPosition);

        return $assetsUrl;
    }

    public function run() {
        if (!TsTranslation::model()->isAccessEnabled()) {
            echo 'Error 403. You have no permission to use this widget!';
        } else {
            $this->registerClientScript();

            $languageModel = new ExtLanguages();
            $languageModel->unsetAttributes();
            $allLanguages = AllLanguages::model()->findAll();
            $allLanguagesList = CHtml::listData($allLanguages, 'id', 'name');

            $sourceTableName = SourceMessages::model()->tableName();
            $connectionID = Yii::app()->getComponent('messages')->connectionID;
            $sql = 'SELECT `category` FROM ' . $sourceTableName . ' GROUP BY `category`';
            $categoryArray = Yii::app()->$connectionID->createCommand($sql)->queryColumn();

            $this->render('_translation_list', array(
                'id' => $this->_id,
                'model' => $languageModel,
                'type' => $this->type,
                'allLanguagesList' => $allLanguagesList,
                'listOptions' => $this->listOptions,
                'htmlOptions' => $this->htmlOptions,
                'categoryArray' => $categoryArray,
                'showDynamicContent' => $this->showDynamicContent
            ));
        }
    }

}
