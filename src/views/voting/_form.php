<?php

use marqu3s\summernote\Summernote;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model simialbi\yii2\voting\models\Voting */
/* @var $form yii\bootstrap4\ActiveForm */
?>

    <div class="voting-form">
        <?php $form = ActiveForm::begin(['id' => 'votingForm']); ?>

        <?= $form->field($model, 'subject')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

        <?= $form->field($model, 'is_moderated', [
            'labelOptions' => [
                'class' => 'custom-control-label'
            ]
        ])->checkbox() ?>

        <?= $form->field($model, 'is_with_mobile_registration', [
            'labelOptions' => [
                'class' => 'custom-control-label'
            ]
        ])->checkbox() ?>

        <?= $form->field($model, 'show_results', [
            'labelOptions' => [
                'class' => 'custom-control-label'
            ]
        ])->checkbox(['disabled' => (bool)$model->is_moderated]); ?>

        <?= $form->field($model, 'finished_message')->widget(Summernote::class, [
            'options' => [
                'style' => [
                    'height' => 'auto'
                ]
            ],
            'defaultClientOptions' => [
                'styleTags' => [
                    'h1', 'h2', 'p'
                ],
                'toolbar' => [
                    ['actions', ['undo', 'redo']],
                    ['para', ['style']],
                    ['lists', ['ol', 'ul']],
                    ['ruler', ['hr']],
                    ['font', ['bold', 'italic', 'underline', 'strikethrough', 'subscript', 'superscript']],
                    ['clear', ['clear']],
                ]
            ]
        ]) ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('simialbi/voting', 'Save'), ['class' => ['btn', 'btn-success']]) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
<?php
$isModeratedId = Html::getInputId($model, 'is_moderated');
$showResultsId = Html::getInputId($model, 'show_results');
$finishedMessageId = Html::getInputId($model, 'finished_message');
$js = <<<JS
jQuery('#$isModeratedId').on('change.yii', function () {
    if (!jQuery(this).is(':checked')) {
        jQuery('#$showResultsId').prop('disabled', false).prop('checked', true).trigger('change.yii');
    } else {
        jQuery('#$showResultsId').prop('disabled', true).trigger('change.yii');
        jQuery('#{$form->id}').yiiActiveForm('updateAttribute', '$showResultsId', null);
    }
});
jQuery('#$showResultsId').on('change.yii', function () {
    var enable = !jQuery(this).is(':checked') ? 'enable' : 'disable';
    jQuery('#$finishedMessageId').summernote(enable);
    if (jQuery(this).is(':checked')) {
        jQuery('#{$form->id}').yiiActiveForm('updateAttribute', '$finishedMessageId', null);
    } else {
        jQuery('#$finishedMessageId').summernote('focus');
    }
});
JS;

$this->registerJs($js);

