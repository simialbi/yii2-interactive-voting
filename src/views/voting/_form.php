<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model simialbi\yii2\voting\models\Voting */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="voting-form">

    <?php $form = ActiveForm::begin(); ?>

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

    <div class="form-group">
        <?= Html::submitButton(Yii::t('simialbi/voting', 'Save'), ['class' => ['btn', 'btn-success']]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
