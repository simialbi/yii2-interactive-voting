<?php

use kartik\select2\Select2;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $users simialbi\yii2\models\UserInterface[] */
?>

<div class="form-group">
    <?= Html::label(Yii::t('simialbi/voting/model/voting-invitee', 'User'), 'input-users');?>
    <?= Select2::widget([
        'name' => 'users',
        'value' => [],
        'id' => 'input-users',
        'data' => $users,
        'theme' => Select2::THEME_BOOTSTRAP,
        'bsVersion' => 4,
        'options' => [
            'placeholder' => '',
            'multiple' => true
        ],
        'pluginOptions' => [
            'allowClear' => false
        ]
    ]); ?>
</div>
