<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model simialbi\yii2\voting\models\LoginMobileForm */

$this->title = Yii::t('simialbi/voting/default', 'Login');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="default-login">
    <h1 class="mb-5"><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'fieldConfig' => [
            'options' => ['class' => ['form-group', 'col-12', 'col-lg-6', 'offset-lg-3']]
        ],
    ]); ?>
    <?= $form->field($model, 'scenario')->hiddenInput()->label(false); ?>

    <div class="form-row">
        <?= $form->field($model, 'username')->widget(MaskedInput::class, [
            'mask' => '9999[999999]',
            'clientOptions' => [
                'greedy' => false
            ]
        ]); ?>
    </div>
    <div class="form-row">
        <div class="form-group col-12 col-lg-6 offset-lg-3">
            <?= Html::submitButton(
                Yii::t('simialbi/voting', 'Submit'),
                ['class' => ['btn', 'btn-primary', 'btn-block'], 'name' => 'login-button']
            ) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
