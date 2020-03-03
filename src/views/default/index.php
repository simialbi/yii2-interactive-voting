<?php

use yii\bootstrap4\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $votings simialbi\yii2\voting\models\Voting[] */

$this->title = Yii::t('simialbi/voting/default', 'Your votings');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="default-index">
    <h1 class="mb-3"><?= Html::encode($this->title); ?></h1>

    <?php if (empty($votings)): ?>
        <p><?= Yii::t('simialbi/voting/default', 'There are no active votings.'); ?></p>
    <?php else: ?>
        <div class="row">
            <div class="col-12 col-lg-4">
                <div class="list-group">
                    <?php foreach ($votings as $voting): ?>
                        <a href="<?= Url::to(['default/view', 'votingId' => $voting->id]); ?>"
                           class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1"><?= $voting->subject; ?></h5>
                                <time class="small"
                                      datetime="<?= Yii::$app->formatter->asDatetime($voting->created_at); ?>">
                                    <?= Yii::$app->formatter->asRelativeTime($voting->created_at); ?>
                                </time>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
