<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model simialbi\yii2\voting\models\Question */

$this->title = Yii::t('simialbi/voting/question', 'Update Question: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('simialbi/voting/question', 'Questions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('simialbi/voting', 'Update');
?>
<div class="question-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
