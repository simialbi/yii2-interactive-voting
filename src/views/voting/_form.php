<?php

use bizley\quill\Quill;
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

    <?= $form->field($model, 'finished_message')->widget(Quill::class, [
        'id' => 'quill',
        'localAssets' => true,
        'options' => [
            'style' => [
                'height' => 'auto'
            ]
        ],
        'readOnly' => $model->show_results,
        'toolbarOptions' => [
            [
                ['header' => 1],
                ['header' => 2],
            ],
            ['bold', 'italic', 'underline', 'strike'],
            [
                ['script' => 'sub'],
                ['script' => 'super']
            ],
            [
                ['list' => 'ordered'],
                ['list' => 'bullet'],
            ]
        ]
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('simialbi/voting', 'Save'), ['class' => ['btn', 'btn-success']]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$quillId = 'q_quill';
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
    q_quill.enable(!jQuery(this).is(':checked'));
    if (jQuery(this).is(':checked')) {
        jQuery('#{$form->id}').yiiActiveForm('updateAttribute', '$finishedMessageId', null);
    } else {
        q_quill.focus();
    }
});
JS;

$this->registerJs($js);

