<div class="tstranslation-list">
    <?php
    /**
     * @author Taron Saribekyan <saribekyantaron@gmail.com>
     * @copyright Copyright &copy; 2014, Taron Saribekyan. All rights reserved.
     * @license http://opensource.org/licenses/BSD-3-Clause The BSD 3-Clause License
     * @version 1.0.0
     */
    $this->widget('tstranslation.widgets.TsGridView', array_merge($listOptions, array(
        'id' => $id,
        'type' => $type,
        'dataProvider' => $model->search(),
        'columns' => array(
            array(
                'name' => 'name',
                'htmlOptions' => array(
                    'title' => 'Click and move rows up or down to order languages, then click `Save ordering` button to save ordering',
                )
            ),
            array(
                'name' => 'nativeName',
                'htmlOptions' => array(
                    'title' => 'Click and move rows up or down to order languages, then click `Save ordering` button to save ordering',
                )
            ),
            array(
                'name' => 'code2',
                'htmlOptions' => array(
                    'title' => 'Click and move rows up or down to order languages, then click `Save ordering` button to save ordering',
                )
            ),
            array(
                'name' => 'flagLink',
                'value' => 'TsTranslationComponent::getListColumns($data, "flagLink")',
                'filter' => CHtml::activeTextField($model, 'name'),
            ),
            array(
                'name' => 'status',
                'value' => 'TsTranslationComponent::getListColumns($data, "status")',
            ),
            array(
                'header' => 'Actions <span class="dropdown pull-right"><button data-tstoggle="tooltip" title="add new language from list" type="button" class="btn btn-info pull-right" data-toggle="dropdown"><i class="glyphicon glyphicon-pencil">&nbsp;&nbsp</i>Add language</button>' . CHtml::dropDownList('newLanguage', '', $allLanguagesList, array('class' => 'dropdown-menu ts-add-language', 'empty' => 'Select language', 'data-stopPropagation' => "true")) . '</span>',
                'value' => 'TsTranslationComponent::getListColumns($data)'
            )
        ),
        'htmlOptions' => $htmlOptions,
    )));
    echo '<ul class="tstranslation-category-tabs nav nav-tabs" role="tablist" id="' . $id . '_tabs">';
    if ($showDynamicContent === true) {
        foreach ($categoryArray as $category) {
            $url = $category;
            if (strpos($category, 'root.') === 0) {
                $url = substr($category, 5);
            } elseif (strpos($category, '#.') === 0) {
                $url = substr($category, 2);
            }
            $tmp = str_replace('.', ' / ', $url);
            $forHref = str_replace('.', '-', $category);
            $tabHeader = ucwords($tmp);
            echo '<li class=""><a href="#tab_' . $forHref . '_' . $id . '" data-toggle="tab" role="tab" data-category="' . $category . '">' . $tabHeader . '</a></li>';
        }
    } else {
        foreach ($categoryArray as $category) {
            $url = $category;
            if (strpos($category, '#.') === 0) {
                continue;
            } elseif (strpos($category, 'root.') === 0) {
                $url = substr($category, 5);
            }
            $tmp = str_replace('.', ' / ', $url);
            $forHref = str_replace('.', '-', $category);
            $tabHeader = ucwords($tmp);
            echo '<li class=""><a href="#tab_' . $forHref . '_' . $id . '" data-toggle="tab" role="tab" data-category="' . $category . '">' . $tabHeader . '</a></li>';
        }
    }

    echo '</ul>';
    echo '<div class="tab-content"><span class="for-loader"></span></div>';
    ?>

    <input type="hidden" id="tstranslationControllerUrl" value="<?php echo Yii::app()->createUrl('tstranslation'); ?>" >
    <input type="hidden" id="currentActiveLanguage" value="" >
    <input type="hidden" id="tstranslationAssetsUrl" value="<?php echo Yii::app()->getAssetManager()->getPublishedUrl(Yii::getPathOfAlias('tstranslation.assets')); ?>" >

</div>