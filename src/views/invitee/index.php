<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel simialbi\yii2\voting\models\SearchInvitee */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('simialbi/voting/invitee', 'Invitees');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invitee-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('simialbi/voting/invitee', 'Create Invitee'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'voting_id',
            'user_id',
            'created_by',
            'updated_by',
            'created_at',
            //'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
