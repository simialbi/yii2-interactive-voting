<?php

use yii\bootstrap4\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model simialbi\yii2\voting\models\Question */

$this->title = Yii::t('simialbi/voting/question', 'Create Question');
$this->params['breadcrumbs'][] = ['label' => Yii::t('simialbi/voting/question', 'Questions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="question-create">

    <?php Pjax::begin([
        'id' => 'formPjax',
        'formSelector' => '#questionForm',
        'enablePushState' => false,
        'clientOptions' => [
            'skipOuterContainers' => true
        ]
    ]); ?>
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]); ?>

    <?php Pjax::end(); ?>
</div>
