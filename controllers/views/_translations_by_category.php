<?php
/**
 * @author Taron Saribekyan <saribekyantaron@gmail.com>
 * @copyright Copyright &copy; 2014, Taron Saribekyan. All rights reserved.
 * @license http://opensource.org/licenses/BSD-3-Clause The BSD 3-Clause License
 * @version 1.0.0
 */
$this->widget('tstranslation.widgets.TsGridView', array(
    'id' => $listId,
    'ajaxUpdate' => false,
    'summaryText' => '<h3 class="list-header">Translates for <strong>' . $languageModel->name . ' (' . $languageModel->nativeName . ')</strong><span class="for-loader">&nbsp;&nbsp;</span></h3>'
    . '<div class="progress" style="display:none"><div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div></div>',
    'enableSorting' => false,
    'template' => '{summary}{items}',
    'dataProvider' => $sourceModel->search(),
    'columns' => array(
        array(
            'value' => 'TsTranslationComponent::getSourceColumn($data, "' . $languageModel->code2 . '")',
            'header' => 'Source Message'
        ),
        array(
            'header' => 'Translate <span class="pull-right tab-action-buttons">'
            . '<button type="button" class="btn btn-primary pull-right google-translate-all"><i class="glyphicon glyphicon-globe">&nbsp;&nbsp;</i>Translate all</button>'
            . '<button type="button" class="btn btn-success pull-right save-all"><i class="glyphicon glyphicon-floppy-save">&nbsp;&nbsp;</i>Save all</button>'
            . '</span>',
            'value' => 'TsTranslationComponent::getTranslateColumn($data, "' . $languageModel->code2 . '")',
        ),
    ),
    'htmlOptions' => array(
        'class' => 'table table-striped table-bordered tab-pane fade in active',
        'data-language' => $languageModel->code2,
    ),
));
?>
<script type="text/javascript">
    $.fn.editable.defaults.type = "textarea";

    $.fn.editable.defaults.url = "<?php echo Yii::app()->createUrl('tstranslation/saveTranslate'); ?>";
    $.fn.editableform.buttons = '<button data-tstoggle="tooltip" title="Ctrl + S or Ctrl + Enter (save)" type="submit" class="btn btn-success editable-submit"><i class="glyphicon glyphicon-ok"></i></button>' +
            '<button data-tstoggle="tooltip" title="Ctrl + G (google translate)" type="button" class="btn btn-primary editable-google-translate"><i class="glyphicon glyphicon-globe"></i></button>' +
            '<span class="for-loader">&nbsp;&nbsp;</span>' +
            '<button data-tstoggle="tooltip" title="close" type="button" style="float:right;" class="btn editable-cancel"><i class="glyphicon glyphicon-remove"></i></button>';

    $(function () {
        $(".translate-value").editable({
            textarea: {
                rows: 8,
                send: "always",
            }

        });
    });
</script>