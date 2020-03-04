<?php

use kartik\grid\GridView;
use kartik\select2\Select2;
use rmrevin\yii\fontawesome\FAS;
use yii\bootstrap4\Html;

//use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel simialbi\yii2\voting\models\SearchVoting */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $users simialbi\yii2\models\UserInterface[] */

$this->title = Yii::t('simialbi/voting/voting', 'Votings');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="voting-index">
    <p>
        <?= Html::a(Yii::t('simialbi/voting/voting', 'Create Voting'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>


    <?= GridView::widget([
        'bsVersion' => 4,
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pjax' => true,
        'export' => false,
        'bordered' => false,
        'panel' => [
            'heading' => $this->title,
            'headingOptions' => [
                'class' => [
                    'card-header',
                    'd-flex',
                    'align-items-center',
                    'justify-content-between',
                    'bg-white'
                ]
            ],
            'titleOptions' => [
                'class' => ['card-title', 'm-0']
            ],
            'summaryOptions' => [
                'class' => []
            ],
            'beforeOptions' => [
                'class' => [
                    'card-body',
                    'py-2',
                    'border-bottom',
                    'd-flex',
                    'justify-content-between',
                    'align-items-center'
                ]
            ],
            'footerOptions' => [
                'class' => ['card-footer', 'bg-white']
            ],
            'options' => [
                'class' => ['card']
            ]
        ],
        'panelTemplate' => '
            {panelHeading}
            {panelBefore}
            {items}
            {panelFooter}
        ',
        'panelHeadingTemplate' => '
            {title}
            {toolbar}
        ',
        'panelFooterTemplate' => '{pager}{footer}',
        'panelBeforeTemplate' => '{pager}{summary}',
        'panelAfterTemplate' => '',
        'containerOptions' => [],
        'toolbar' => [
            [
                'content' => Html::a(FAS::i('plus'), ['voting/create'], [
                    'class' => ['btn', 'btn-primary'],
                    'data' => [
                        'pjax' => '0'
                    ]
                ])
            ]
        ],
        'columns' => [
            [
                'class' => 'kartik\grid\SerialColumn'
            ],
            [
                'class' => 'kartik\grid\DataColumn',
                'attribute' => 'subject',
                'vAlign' => GridView::ALIGN_MIDDLE
            ],
            [
                'class' => 'kartik\grid\DataColumn',
                'attribute' => 'description',
                'format' => 'ntext',
                'vAlign' => GridView::ALIGN_MIDDLE
            ],
            [
                'class' => 'kartik\grid\DataColumn',
                'attribute' => 'created_by',
                'value' => 'creator.name',
                'filter' => $users,
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'theme' => Select2::THEME_KRAJEE_BS4,
                    'bsVersion' => 4,
                    'options' => [
                        'placeholder' => ''
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ]
                ],
                'vAlign' => GridView::ALIGN_MIDDLE
            ],
            [
                'class' => 'kartik\grid\DataColumn',
                'attribute' => 'updated_by',
                'value' => 'updater.name',
                'filter' => $users,
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'theme' => Select2::THEME_KRAJEE_BS4,
                    'bsVersion' => 4,
                    'options' => [
                        'placeholder' => ''
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ]
                ],
                'vAlign' => GridView::ALIGN_MIDDLE
            ],
            [
                'class' => 'kartik\grid\DataColumn',
                'attribute' => 'created_at',
                'format' => 'datetime',
                'vAlign' => GridView::ALIGN_MIDDLE
            ],
            [
                'class' => 'kartik\grid\DataColumn',
                'attribute' => 'updated_at',
                'format' => 'datetime',
                'vAlign' => GridView::ALIGN_MIDDLE
            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{view} {activate} {delete}',
                'buttons' => [
                    'activate' => function ($url) {
                        return Html::a(FAS::i('play'), $url, [
                            'title' => Yii::t('simialbi/voting/voting', 'Start this voting'),
                            'aria-label' => Yii::t('simialbi/voting/voting', 'Start this voting'),
                            'data-pjax' => '0'
                        ]);
                    }
                ],
                'visibleButtons' => [
                    'activate' => function ($model) {
                        /** @var $model \simialbi\yii2\voting\models\Voting */
                        return !$model->is_active && !$model->is_finished;
                    }
                ]
            ],

        ],
    ]); ?>

</div>
