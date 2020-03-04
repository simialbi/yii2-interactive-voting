<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model simialbi\yii2\voting\models\Voting */

$this->title = Yii::t('simialbi/voting/voting', 'Update Voting: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('simialbi/voting/voting', 'Votings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('simialbi/voting', 'Update');
?>
<div class="voting-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
