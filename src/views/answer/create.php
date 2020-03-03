<?php

use rmrevin\yii\fontawesome\FAS;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model simialbi\yii2\voting\models\Answer */

$this->title = Yii::t('simialbi/voting/answer', 'Create Answer');
$this->params['breadcrumbs'][] = ['label' => Yii::t('simialbi/voting/answer', 'Answers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php Pjax::begin([
    'id' => 'answerPjax',
    'formSelector' => '#answerForm',
    'enablePushState' => false,
    'clientOptions' => [
        'skipOuterContainers' => true
    ]
]); ?>

<div class="row">
    <div class="col-12">
        <div class="list-group">
            <?php foreach ($model->question->answers as $answer): ?>
                <div class="list-group-item list-group-item-actions" id="answer-<?= $answer->id; ?>">
                    <span><?= Html::encode($answer->text); ?></span>
                    <a href="<?= Url::to(['answer/delete', 'id' => $answer->id]); ?>"
                       class="list-group-item-action delete-answer">
                        <?= FAS::i('trash-alt'); ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<div class="row mt-4">
    <div class="col-12">
        <?= $this->render('_form', [
            'model' => $model,
        ]); ?>
    </div>
</div>

<?php
$msg = Yii::t('yii', 'Are you sure you want to delete this item?');
$js = <<<JS
    jQuery('.delete-answer').on('click', function (e) {        
        if (!confirm('$msg')) {
            e.preventDefault();
            e.stopPropagation();
        }
    });
JS;
$this->registerJs($js);
?>
<?php Pjax::end(); ?>
