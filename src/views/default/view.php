<?php

use rmrevin\yii\fontawesome\FAS;
use yii\bootstrap4\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $voting simialbi\yii2\voting\models\Voting */
/* @var $question simialbi\yii2\voting\models\Question|null */
/* @var $lastQuestion simialbi\yii2\voting\models\Question[]|null */

$this->title = $voting->subject;
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="default-view">
    <h1 class="mb-3"><?= Html::encode($this->title); ?></h1>

    <?php if ($question): ?>
        <?= Html::beginForm(['save-answer', 'questionId' => $question->id], 'post', [
            'id' => 'answerForm'
        ]); ?>
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="card-title mb-0"><?= $question->subject; ?></h4>
            </div>
            <div class="card-body">
                <?php if ($question->description): ?>
                    <?= Yii::$app->formatter->asNtext($question->description); ?>
                <?php endif; ?>
                <div class="form-group">
                    <div class="btn-group-toggle d-flex flex-wrap" data-toggle="buttons">
                        <?php foreach ($question->answers as $answer): ?>
                            <label class="mx-3 my-2 btn btn-lg btn-outline-secondary" style="width: calc(50% - 2rem);">
                                <input type="radio" name="answer" value="<?= $answer->id; ?>">
                                <?= Html::encode($answer->text); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="form-group mb-0">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="anonymous" value="1"
                               id="voteAnonymous">
                        <label class="custom-control-label" for="voteAnonymous">
                            <?= Yii::t('simialbi/voting', 'Vote anonymous'); ?>
                        </label>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end">
                <?= Html::submitButton(FAS::i('save') . '&nbsp;' . Yii::t('simialbi/voting', 'Save'), [
                    'class' => ['btn', 'btn-success']
                ]); ?>
            </div>
        </div>
        <?= Html::endForm(); ?>
    <?php elseif (!empty($lastQuestion)): ?>
        <?php foreach ($lastQuestion as $item): ?>
            <div class="result-chart my-4">
                <?= $this->render('_chart', [
                    'height' => '400px',
                    'lastQuestion' => $item
                ]); ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?php
if (!$question) {
    $url = Url::to(['status', 'votingId' => $voting->id], true);
    $js = <<<JS
window.setInterval(function () {
    jQuery.ajax({
        url: '$url',
        method: 'post'
    }).done(function (returnData) {
        switch (returnData.action) {
            case 'redirect':
                window.location.replace(returnData.target);
                break;
            default:
                break;
        }
    });
}, 6000);
JS;

    $this->registerJs($js);
}