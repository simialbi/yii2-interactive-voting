<?php

/* @var $this yii\web\View */

use yii\helpers\Url;

/* @var $voting simialbi\yii2\voting\models\Voting */
/* @var $lastQuestion simialbi\yii2\voting\models\Question|null */

$this->title = $voting->subject;
$this->params['breadcrumbs'][] = $this->title;
?>
<?php if ($lastQuestion): ?>
    <?= $this->render('_chart', [
        'lastQuestion' => $lastQuestion
    ]); ?>
<?php else: ?>
    <div class="jumbotron">
        <h1 class="display-4"><?= Yii::t('simialbi/voting', 'Voting did not start yet'); ?></h1>
        <p class="lead">
            <?= Yii::t(
                'simialbi/voting',
                'The Voting <b>{voting}</b> did not start yet. This page will automatically update as soon as the first question started.'
            ); ?>
        </p>
    </div>
<?php endif; ?>
<?php
$url = Url::to(['status', 'votingId' => $voting->id, 'referrer' => 'live'], true);
$js = <<<JS
window.setInterval(function () {
    jQuery.ajax({
        url: '$url',
        method: 'post'
    }).done(function (returnData) {
        switch (returnData.action) {
            case 'redirect':
                if (returnData.newQuestion && returnData.newQuestion.id === {$lastQuestion->id}) {
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
