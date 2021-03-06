<?php

/* @var $this yii\web\View */

use yii\helpers\Url;

/* @var $voting simialbi\yii2\voting\models\Voting */
/* @var $lastQuestion simialbi\yii2\voting\models\Question|null */

$this->title = $voting->subject;
$this->params['breadcrumbs'][] = $this->title;
?>
<?php if ($voting->is_active && !$voting->is_finished && $lastQuestion): ?>
    <?= $this->render('_chart', [
        'lastQuestion' => $lastQuestion,
        'height' => 'calc(100vh - 400px)'
    ]); ?>
<?php elseif ($voting->is_finished): ?>
    <div class="jumbotron">
        <h1 class="display-4"><?= Yii::t('simialbi/voting', 'Voting finished'); ?></h1>
        <p class="lead">
            <?= Yii::t(
                'simialbi/voting',
                'The Voting <b>{voting}</b> is closed. The results can\'t be seen any more.',
                ['voting' => $voting->subject]
            ); ?>
        </p>
    </div>
<?php else: ?>
    <div class="jumbotron">
        <h1 class="display-4"><?= Yii::t('simialbi/voting', 'Voting did not start yet'); ?></h1>
        <p class="lead">
            <?= Yii::t(
                'simialbi/voting',
                'The Voting <b>{voting}</b> did not start yet. This page will automatically update as soon as the first question started.',
                ['voting' => $voting->subject]
            ); ?>
        </p>
    </div>
<?php endif; ?>
<?php
$url = Url::to(['status', 'votingId' => $voting->id, 'referrer' => 'live'], true);
$id = $lastQuestion ? $lastQuestion->id : 'null';
$js = <<<JS
window.setInterval(function () {
    jQuery.ajax({
        url: '$url',
        method: 'post'
    }).done(function (returnData) {
        switch (returnData.action) {
            case 'redirect':
                if (returnData.newQuestion && returnData.newQuestion.id === {$id}) {
                    return;
                }
                window.location.replace(returnData.target);
                break;
            default:
                break;
        }
    });
}, 6000);
JS;

$this->registerJs($js);
