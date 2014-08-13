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
                 *      - as default uses TsTranslation::getDtLanguage() returned value:
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
                 *      - as default uses TsTranslation::getDtLanguage() returned value:
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