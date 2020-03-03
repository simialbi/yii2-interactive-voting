<?php

use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model simialbi\yii2\voting\models\Question */
/* @var $answerModel simialbi\yii2\voting\models\Answer */

?>

<div class="question-form">
    <div class="row">
        <div class="col-12">
            <h2><?= $model->subject; ?></h2>
            <?= Yii::$app->formatter->asParagraphs($model->description); ?>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <?php Pjax::begin([
                'id' => 'answerPjax',
                'formSelector' => '#answerForm',
                'enablePushState' => false,
                'clientOptions' => [
                    'skipOuterContainers' => true
                ]
            ]); ?>
            <?= $this->render('/answer/_form', ['model' => $answerModel]); ?>
            <?php Pjax::end(); ?>
        </div>
    </div>
</div>