<?php

use rmrevin\yii\fontawesome\FAS;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $users simialbi\yii2\models\UserInterface[] */

Pjax::begin([
    'id' => 'crateInviteePjax',
    'formSelector' => '#inviteeModalForm',
    'enablePushState' => false,
    'clientOptions' => [
        'skipOuterContainers' => true
    ]
]);
?>
<div class="voting-invitee-modal">
    <?= Html::beginForm('', 'post', ['id' => 'inviteeModalForm']); ?>
    <div class="modal-header">
        <h4 class="modal-title"><?= Yii::t('simialbi/voting/invitee', 'Create Invitee'); ?></h4>
        <?= Html::button('<span aria-hidden="true">' . FAS::i('times') . '</span>', [
            'type' => 'button',
            'class' => ['close'],
            'data' => [
                'dismiss' => 'modal'
            ],
            'aria' => [
                'label' => Yii::t('simialbi/voting', 'Close')
            ]
        ]); ?>
    </div>
    <div class="modal-body">
        <?= $this->render('_form', [
            'users' => $users
        ]); ?>
    </div>
    <div class="modal-footer">
        <?= Html::button(Yii::t('simialbi/voting', 'Close'), [
            'type' => 'button',
            'class' => ['btn', 'btn-dark'],
            'data' => [
                'dismiss' => 'modal'
            ],
            'aria' => [
                'label' => Yii::t('simialbi/voting', 'Close')
            ]
        ]); ?>
        <?= Html::submitButton(Yii::t('simialbi/voting', 'Save'), [
            'type' => 'button',
            'class' => ['btn', 'btn-success'],
            'aria' => [
                'label' => Yii::t('simialbi/voting', 'Save')
            ]
        ]); ?>
    </div>
    <?= Html::endForm(); ?>
</div>
<?php Pjax::end(); ?>

