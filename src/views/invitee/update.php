<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model simialbi\yii2\voting\models\Invitee */

$this->title = Yii::t('simialbi/voting/invitee', 'Update Invitee: {name}', [
    'name' => $model->voting_id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('simialbi/voting/invitee', 'Invitees'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->voting_id, 'url' => ['view', 'voting_id' => $model->voting_id, 'user_id' => $model->user_id]];
$this->params['breadcrumbs'][] = Yii::t('simialbi/voting/invitee', 'Update');
?>
<div class="invitee-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
