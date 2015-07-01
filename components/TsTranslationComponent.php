<?php

/**
 * @author Taron Saribekyan <saribekyantaron@gmail.com>
 * @copyright Copyright &copy; 2014, Taron Saribekyan. All rights reserved.
 * @license http://opensource.org/licenses/BSD-3-Clause The BSD 3-Clause License
 * @version 1.0.0
 */
class TsTranslationComponent extends TsTranslation {

    public static function getListColumns($data, $field = null) {
        switch ($field) {
            case 'flagLink':
                echo $data->flagLink ? '<img src="' . Yii::app()->baseUrl . '/' . $data->flagLink . '">' : '<img src="' . Yii::app()->getAssetManager()->getPublishedUrl(Yii::getPathOfAlias('tstranslation.assets')) . '/images/flags/' . $data->code2 . '.gif">';
                break;
            case 'status':
                if ($data->status == 1) {
                    echo '<span class="badge alert-success badge-success ts-status">Active</span>';
                    if ($data->asDefault == 1) {
                        echo '<span class="badge alert-info badge-info ts-default">Default</span>';
                    }
                } else {
                    echo '<span class="badge alert-danger badge-important ts-status">Not active</span>';
                }
                echo '<span class="for-loader"> </span>';
                break;

            default:
                echo '<button type="button" class="btn btn-warning ts-change-status" data-id="' . $data->getPrimaryKey() . '" data-tstoggle="tooltip" title="enable / disable language for frontend language widget"><i class="glyphicon glyphicon-edit">&nbsp;&nbsp;</i>Change status</button>';
                echo '<button type="button" class="btn btn-primary ts-make-default" data-id="' . $data->getPrimaryKey() . '" data-tstoggle="tooltip" title="make this language as default"><i class="glyphicon glyphicon-pushpin">&nbsp;&nbsp;</i>Make default</button>';
                echo '<button type="button" class="btn btn-danger ts-remove"  data-id="' . $data->getPrimaryKey() . '" data-tstoggle="tooltip" title="delete language"><i class="glyphicon glyphicon-trash">&nbsp;&nbsp;</i>Remove</button>';
                echo '<button type="button" class="btn btn-success ts-translate" data-language="' . $data->code2 . '" data-tstoggle="tooltip" title="translate available messages for this language"><i class="glyphicon glyphicon-font">&nbsp;&nbsp;</i>Translate</button>';
                break;
        }
    }

    public static function getTranslateColumn($data, $code2) {
        echo '<div class="translate-div"><span data-tstoggle="tooltip" title="click to edit message" class="translate-value" data-pk="' . $data->getPrimaryKey() . '" data-name="' . $code2 . '">' . Yii::t($data->category, $data->message, array(), null, $code2) . '</span></div>';
    }

    public static function getSourceColumn($data, $code2) {
        if (strpos($data->category, '#.') === 0) {
            echo '<span class="translates-list source-green">' . Yii::t($data->category, $data->message, array(), null, TsTranslationComponent::getDefaultLanguage('code2')) . '</span>';
        } elseif (empty($data->message)) {
            echo '<span class="translates-list source-red">This is empty message</span>';
        } else {
            echo $data->message;
        }
    }

    /**
     * Return array of active languages with its all attributes if $listAttribute is false / null,
     *      or langaugeCode => $listAttribute array
     * 
     * @param string $listAttribute
     * @return array or string
     */
    public static function getActiveLanguages($listAttribute = 'name') {
        $languages = ExtLanguages::model()->findAllByAttributes(array('status' => 1), array('order' => '`ordering`'));
        if ($listAttribute) {
            $languageList = CHtml::listData($languages, 'code2', $listAttribute);
            return $languageList;
        }
        return $languages;
    }

    /**
     * Return array of available (created) languages with its all attributes if $listAttribute is false / null,
     *      or langaugeCode => $listAttribute array
     * 
     * @param type $listAttribute
     * @return array
     */
    public static function getAvailableLanguages($listAttribute = 'name') {
        $languages = ExtLanguages::model()->findAll();
        if ($listAttribute) {
            $languageList = CHtml::listData($languages, 'code2', $listAttribute);
            return $languageList;
        }
        return $languages;
    }

    /**
     * Returns array of default language attributes, or attribute $attribute of default language
     * 
     * @param string $attribute
     * @return array or string
     */
    public static function getDefaultLanguage($attribute = false) {
        if ($attribute === 'code2') {
            if (isset(Yii::app()->request->cookies['_defaultLang']) && !empty(Yii::app()->request->cookies['_defaultLang']->value)) {
                return Yii::app()->request->cookies['_defaultLang']->value;
            } else {
                $defaultLanguage = ExtLanguages::model()->findByAttributes(array('asDefault' => 1));
                Yii::app()->request->cookies['_defaultLang'] = new CHttpCookie('_defaultLang', $defaultLanguage->code2, array(
                    'path' => '/',
                ));
                return $defaultLanguage->code2;
            }
        }

        $defaultLanguage = ExtLanguages::model()->findByAttributes(array('asDefault' => 1));
        if ($attribute) {
            return $defaultLanguage->$attribute;
        }
        return $defaultLanguage;
    }

}
