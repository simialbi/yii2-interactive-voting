<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model simialbi\yii2\voting\models\Voting */

$this->title = Yii::t('simialbi/voting/voting', 'Create Voting');
$this->params['breadcrumbs'][] = ['label' => Yii::t('simialbi/voting/voting', 'Votings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="voting-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
