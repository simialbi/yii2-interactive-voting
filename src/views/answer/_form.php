<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model simialbi\yii2\voting\models\Answer */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="answer-form">

    <?php $form = ActiveForm::begin([
        'id' => 'answerForm',
        'action' => ['answer/create', 'questionId' => $model->question_id]
    ]); ?>

    <?= $form->field($model, 'text')->textInput(['maxlength' => true]); ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('simialbi/voting', 'Save'), ['class' => ['btn', 'btn-success']]); ?>

        <?= Html::a(
            Yii::t('simialbi/voting/answer', 'Create another Question'),
            ['question/create', 'votingId' => $model->voting->id],
            ['class' => ['btn', 'btn-primary']]
        ); ?>

        <?= Html::a(Yii::t('simialbi/voting', 'Finish'), ['voting/view', 'id' => $model->voting->id], [
            'class' => ['btn', 'btn-success']
        ]); ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
