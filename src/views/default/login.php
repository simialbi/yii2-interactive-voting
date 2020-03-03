<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model simialbi\yii2\voting\models\LoginForm */

$this->title = Yii::t('simialbi/voting/default', 'Login');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="default-login">
    <h1 class="mb-5"><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'layout' => ActiveForm::LAYOUT_HORIZONTAL,
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-3\">{input}\n{error}</div><div class=\"col-lg-8\">{hint}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]); ?>

    <?= $form->field($model, 'username')->textInput(['autofocus' => true]); ?>

    <?= $form->field($model, 'code')->passwordInput(); ?>

    <div class="form-group row">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::submitButton('Login', ['class' => ['btn', 'btn-primary'], 'name' => 'login-button']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
