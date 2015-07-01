<?php

/**
 * @author Taron Saribekyan <saribekyantaron@gmail.com>
 * @copyright Copyright &copy; 2014, Taron Saribekyan. All rights reserved.
 * @license http://opensource.org/licenses/BSD-3-Clause The BSD 3-Clause License
 * @version 1.0.0
 */
class TsUrlManager extends CUrlManager {

    public $prependLangRules = true;
    public $showLangInUrl = true;

    public function init() {
        if ($this->showLangInUrl && $this->prependLangRules && is_array($this->rules)) {
            $newRules = array();
            $langInUrl = '';
            foreach ($this->rules as $key => $value) {
                $langInUrl = strpos($key, '/') === 0 ? '<_lang:\w{2}>' : '<_lang:\w{2}>/';
                $newRules[$langInUrl . $key] = $value;
            }
            $this->rules = $newRules;
        }
        parent::init();
    }

    public function createUrl($route, $params = array(), $ampersand = '&') {
        if ($route !== 'tstranslation') {
            if ($this->showLangInUrl) {
                if (empty($params['_lang'])) {
                    $params['_lang'] = Yii::app()->language;
                }
            } else {
                if (isset($params['_lang'])) {
                    unset($params['_lang']);
                }
            }
        } else {
            $params = array();
        }

        return parent::createUrl($route, $params, $ampersand);
    }

}
