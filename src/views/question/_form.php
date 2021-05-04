<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model simialbi\yii2\voting\models\Question */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="question-form">

    <?php $form = ActiveForm::begin(['id' => 'questionForm']); ?>

    <?= $form->field($model, 'subject')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'multiple_answers_allowed', [
        'labelOptions' => [
            'class' => 'custom-control-label'
        ]
    ])->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('simialbi/voting', 'Save'), ['class' => ['btn', 'btn-success']]) ?>

        <?= Html::a(Yii::t('simialbi/voting', 'Finish'), ['voting/view', 'id' => $model->voting_id], [
            'class' => ['btn', 'btn-success']
        ]); ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
