<?php

/**
 * @author Taron Saribekyan <saribekyantaron@gmail.com>
 * @copyright Copyright &copy; 2014, Taron Saribekyan. All rights reserved.
 * @license http://opensource.org/licenses/BSD-3-Clause The BSD 3-Clause License
 * @version 1.0.0
 */
class TsTranslationController extends CExtController {

    protected function beforeAction($action) {
        if (!TsTranslation::model()->isAccessEnabled()) {
            if (Yii::app()->request->isAjaxRequest) {
                echo '{"ok":0, "message":"You have no permission to do this action!"}';
                Yii::app()->end();
            } else {
                throw new CHttpException(403, 'You have no permission to do this action!');
            }
        }
        return parent::beforeAction($action);
    }

    public function actionSaveOrdering() {
        $ok = 0;
        $message = 'Your request is invalid';
        $connectionID = Yii::app()->getComponent('messages')->connectionID;
        if (Yii::app()->request->isAjaxRequest && isset($_POST['languageList'])) {
            $languageList = $_POST['languageList'];
            $tableName = ExtLanguages::model()->tableName();
            $tmpQuery = '';
            foreach ($languageList as $ordering => $id) {
                $tmpQuery .= ' WHEN "' . intval($id) . '" THEN "' . (intval($ordering) + 1) . '" ';
            }
            $query = 'UPDATE `' . $tableName . '` SET `ordering` = CASE `id` ' . $tmpQuery . ' ELSE `ordering` END';
            if (Yii::app()->$connectionID->createCommand($query)->execute()) {
                $ok = 1;
                $message = 'New ordering saved.';
            } else {
                $message = 'Ordering not changed!';
            }
        }
        echo json_encode(array('ok' => $ok, 'message' => $message));
        Yii::app()->end();
    }

    public function actionAddLanguage() {
        $ok = 0;
        $message = 'Your request is invalid';
        $redirectUrl = null;
        if (Yii::app()->request->isAjaxRequest && isset($_POST['id'])) {
            $id = intval($_POST['id']);
            $model = AllLanguages::model()->findByPk($id);
            if ($model) {
                $existedLanguage = ExtLanguages::model()->findByAttributes(array('code2' => $model->code2));
                if ($existedLanguage) {
                    $message = 'Language "' . $existedLanguage->name . '" already exists!';
                } else {
                    $newLanguage = new ExtLanguages();
                    $newLanguage->unsetAttributes();
                    $newLanguage->attributes = $model->attributes;
                    $newLanguage->status = 1;
                    $newLanguage->asDefault = 0;
                    if ($newLanguage->save()) {
                        $ok = 1;
                        $message = 'New languge added successfully.';
                        $redirectUrl = Yii::app()->request->urlReferrer;
                    } else {
                        $message = 'Error! languge not added';
                    }
                }
            }
        }
        echo json_encode(array('ok' => $ok, 'message' => $message, 'redirectUrl' => $redirectUrl));
        Yii::app()->end();
    }

    public function actionRemoveLanguage() {
        $ok = 0;
        $message = 'Your request is invalid';
        if (Yii::app()->request->isAjaxRequest && isset($_POST['id'])) {
            $model = ExtLanguages::model()->findByPk(intval($_POST['id']));
            if ($model) {
                if ($model->asDefault) {
                    $message = 'You can not remove default language.';
                } elseif ($model->delete()) {
                    if ($_POST['deleteTranslations'] == 'yes') {
                        $connectionID = Yii::app()->getComponent('messages')->connectionID;
                        $query = 'DELETE FROM `' . TranslatedMessages::model()->tableName() . '` WHERE `language`="' . $model->code2 . '"';
                        Yii::app()->$connectionID->createCommand($query)->execute();
                    }
                    $ok = 1;
                    $message = 'Language removed';
                } else {
                    $message = 'Error! Language not deleted';
                }
            }
        }
        echo json_encode(array('ok' => $ok, 'message' => $message));
        Yii::app()->end();
    }

    public function actionChangeStatus() {
        $ok = 0;
        $status = null;
        $message = 'Your request is invalid';
        if (Yii::app()->request->isAjaxRequest && isset($_POST['id'])) {
            $model = ExtLanguages::model()->findByPk(intval($_POST['id']));
            if ($model) {
                $newStatus = abs((int) ($model->status) - 1);
                $model->status = $newStatus;
                if ($model->asDefault && !$model->status) {
                    $message = 'You can not change default language status.';
                } elseif ($model->save()) {
                    $ok = 1;
                    $status = $model->status;
                    $message = 'Status changed successfully.';
                } else {
                    $message = 'Error! Language not changed.';
                }
            }
        }
        echo json_encode(array('ok' => $ok, 'message' => $message, 'status' => $status));
        Yii::app()->end();
    }

    public function actionMakeDefault() {
        $ok = 0;
        $message = 'Your request is invalid';
        if (Yii::app()->request->isAjaxRequest && isset($_POST['id'])) {
            $model = ExtLanguages::model()->findByPk(intval($_POST['id']));
            if ($model) {
                if ($model->asDefault) {
                    $message = 'Its default language already!';
                } elseif (!$model->status) {
                    $message = 'You must activate this language to make default!';
                } else {
                    $defaultModel = ExtLanguages::model()->findByAttributes(array('asDefault' => 1));
                    $defaultModel->asDefault = 0;
                    $model->asDefault = 1;
                    if ($defaultModel->save() && $model->save()) {
                        $ok = 1;
                        $message = 'Default language changed.';
                        Yii::app()->request->cookies['_defaultLang'] = new CHttpCookie('_defaultLang', $model->code2, array(
                            'path' => '/',
                        ));
                    } else {
                        $message = 'Error! Default language not changed.';
                    }
                }
            }
        }
        echo json_encode(array('ok' => $ok, 'message' => $message));
        Yii::app()->end();
    }

    public function actionGetTranslationsByCategory() {
        if (
                Yii::app()->request->isAjaxRequest &&
                isset($_POST['language']) &&
                isset($_POST['category']) &&
                isset($_POST['listId'])
        ) {
            $sourceModel = new SourceMessages();
            $sourceModel->unsetAttributes();
            $sourceModel->category = $_POST['category'];
            $sourceModel->language = $_POST['language'];

            $languageModel = ExtLanguages::model()->findByAttributes(array('code2' => $_POST['language']));

            $this->renderPartial('_translations_by_category', array(
                'sourceModel' => $sourceModel,
                'languageModel' => $languageModel,
                'listId' => $_POST['listId']
            ));
        }
    }

    public function actionGoogleTranslate() {
        $ok = 0;
        $message = 'Your request is invalid';
        $sourceLanguage = Yii::app()->sourceLanguage == 2 ? Yii::app()->sourceLanguage : substr(Yii::app()->sourceLanguage, 0, 2);
        if (is_callable('curl_init') && isset($_POST['language']) && isset($_POST['value']) && $curl = curl_init()) {
            if (trim($_POST['value']) == '') {
                $ok = 1;
                $message = '';
            } else {
                $cUrl = 'https://translate.google.ru/translate_a/single?client=t&sl=' . trim($sourceLanguage) . '&tl=' . trim($_POST['language']) . '&dt=t&ie=UTF-8&oe=UTF-8&q=' . urlencode(trim($_POST['value']));
                curl_setopt($curl, CURLOPT_URL, $cUrl);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

                $out = curl_exec($curl);
                $out = explode('","', $out);
                $out = mb_substr($out[0], 4);
                if (trim($out) == trim($_POST['value'])) {
                    $cUrl = 'https://translate.google.ru/translate_a/single?client=t&sl=auto&tl=' . trim($_POST['language']) . '&dt=t&ie=UTF-8&oe=UTF-8&q=' . urlencode(trim($_POST['value']));
                    curl_setopt($curl, CURLOPT_URL, $cUrl);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

                    $out = curl_exec($curl);
                    $out = explode('","', $out);
                    $out = mb_substr($out[0], 4);
                }
                $ok = 1;
                $message = $out;
                curl_close($curl);
            }
        }

        echo json_encode(array('ok' => $ok, 'message' => $message));
        Yii::app()->end();
    }

    public function actionSaveTranslate() {
        $ok = 0;
        $message = 'Your request is invalid';
        if (Yii::app()->request->isAjaxRequest) {
            if (isset($_POST['pk']) && isset($_POST['value']) && isset($_POST['name'])) {
                $model = TranslatedMessages::model()->findByPk(array('id' => intval($_POST['pk']), 'language' => $_POST['name']));
                if ($model) {
                    $model->translation = $_POST['value'];
                    if ($model->save()) {
                        $ok = 1;
                        $message = 'Translation saved';
                    } else {
                        $message = 'Translation NOT saved! Reload page and try again';
                    }
                } else {
                    $message = 'You can not translate source language messages';
                }
            }
        } else {
            $ok = 0;
        }
        echo json_encode(array('ok' => $ok, 'message' => $message));
        Yii::app()->end();
    }

}
