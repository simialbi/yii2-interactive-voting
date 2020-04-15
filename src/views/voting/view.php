<?php

use kartik\grid\GridView;
use kartik\select2\Select2;
use rmrevin\yii\fontawesome\FAS;
use yii\bootstrap4\Html;
use yii\bootstrap4\Modal;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model simialbi\yii2\voting\models\Voting */
/* @var $questionSearchModel simialbi\yii2\voting\models\SearchQuestion */
/* @var $questionDataProvider yii\data\ActiveDataProvider */
/* @var $inviteeSearchModel simialbi\yii2\voting\models\SearchInvitee */
/* @var $inviteeDataProvider yii\data\ActiveDataProvider */
/* @var $users simialbi\yii2\models\UserInterface[] */

$this->title = $model->subject;
$this->params['breadcrumbs'][] = ['label' => Yii::t('simialbi/voting/voting', 'Votings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

\simialbi\yii2\web\BaseAsset::register($this);
?>
    <div class="voting-view">

        <h1>
            <?= Html::encode($this->title); ?>
            <?php if ($model->is_active): ?>
                <?= Yii::t('simialbi/voting', '(active)'); ?>
            <?php endif; ?>
        </h1>

        <p>
            <?= Html::a(Yii::t('simialbi/voting', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>

        <div class="row">
            <div class="col-12 col-lg-6 col-xl-4">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'id',
                        'subject',
                        'description:ntext',
                        'is_active:boolean',
                        'is_finished:boolean',
                        'is_moderated:boolean',
                        'is_with_mobile_registration:boolean',
                        'created_by',
                        'updated_by',
                        'created_at:datetime',
                        'updated_at:datetime',
                    ],
                ]); ?>
            </div>
            <div class="col-12 col-lg-6 col-xl-8">
                <?php $toolbar = []; ?>
                <?php if (Yii::$app->user->can('administrateVoting')): ?>
                    <?php $toolbar[] = [
                        'content' => Html::a(FAS::i('plus'), ['question/create', 'votingId' => $model->id], [
                            'class' => ['btn', 'btn-primary'],
                            'data' => [
                                'pjax' => '0'
                            ]
                        ])
                    ]; ?>
                <?php endif; ?>
                <?= GridView::widget([
                    'bsVersion' => 4,
                    'dataProvider' => $questionDataProvider,
                    'filterModel' => $questionSearchModel,
                    'pjax' => true,
                    'export' => false,
                    'bordered' => false,
                    'panel' => [
                        'heading' => Yii::t('simialbi/voting/question', 'Questions'),
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
                    'toolbar' => $toolbar,
                    'columns' => [
                        [
                            'class' => 'kartik\grid\SerialColumn'
                        ],
                        [
                            'class' => 'kartik\grid\ExpandRowColumn',
                            'width' => '50px',
                            'value' => function () {
                                return GridView::ROW_COLLAPSED;
                            },
                            'detailUrl' => Url::to(['question/view']),
                            'expandOneOnly' => true,
                            'expandIcon' => (string)FAS::i('caret-square-right'),
                            'collapseIcon' => (string)FAS::i('caret-square-up')
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
                            'controller' => 'question',
                            'template' => '{activate} {deactivate} {delete}',
                            'buttons' => [
                                'activate' => function ($url) {
                                    return Html::a(FAS::i('play'), $url, [
                                        'title' => Yii::t('simialbi/voting/question', 'Open this question'),
                                        'aria-label' => Yii::t('simialbi/voting/question', 'Open this question'),
                                        'data-pjax' => '0'
                                    ]);
                                },
                                'deactivate' => function ($url) {
                                    return Html::a(FAS::i('stop'), $url, [
                                        'title' => Yii::t('simialbi/voting/question', 'Close this question'),
                                        'aria-label' => Yii::t('simialbi/voting/question', 'Close this question'),
                                        'data-pjax' => '0'
                                    ]);
                                }
                            ],
                            'visibleButtons' => [
                                'activate' => function ($model) {
                                    /** @var $model \simialbi\yii2\voting\models\Question */
                                    return
                                        Yii::$app->user->can('manageVoting') &&
                                        $model->voting->is_moderated &&
                                        !$model->is_active &&
                                        !$model->is_finished;
                                },
                                'deactivate' => function ($model) {
                                    /** @var $model \simialbi\yii2\voting\models\Question */
                                    return
                                        Yii::$app->user->can('manageVoting') &&
                                        $model->voting->is_moderated &&
                                        $model->is_active &&
                                        !$model->is_finished;
                                },
                                'delete' => Yii::$app->user->can('administrateVoting')
                            ]
                        ],

                    ],
                ]); ?>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <?php $toolbar = []; ?>
                <?php if (Yii::$app->user->can('administrateVotingInvitations')): ?>
                    <?php $toolbar[] = [
                        'content' => Html::a(FAS::i('plus'), ['invitee/create', 'votingId' => $model->id], [
                            'class' => ['btn', 'btn-primary'],
                            'data' => [
                                'toggle' => 'modal',
                                'target' => '#dynamicModal'
                            ]
                        ])
                    ]; ?>
                <?php endif; ?>
                <?= GridView::widget([
                    'bsVersion' => 4,
                    'dataProvider' => $inviteeDataProvider,
                    'filterModel' => $inviteeSearchModel,
                    'pjax' => true,
                    'export' => false,
                    'bordered' => false,
                    'panel' => [
                        'heading' => Yii::t('simialbi/voting/invitee', 'Invitees'),
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
                    'toolbar' => $toolbar,
                    'columns' => [
                        [
                            'class' => 'kartik\grid\SerialColumn'
                        ],
                        [
                            'class' => 'kartik\grid\DataColumn',
                            'attribute' => 'user_id',
                            'value' => 'user.name',
                            'vAlign' => GridView::ALIGN_MIDDLE
                        ],
                        [
                            'class' => 'kartik\grid\DataColumn',
                            'attribute' => 'code',
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
                            'controller' => 'invitee',
                            'template' => '{delete}',
                            'visibleButtons' => [
                                'delete' => Yii::$app->user->can('administrateVotingInvitations')
                            ]
                        ]
                    ],
                ]); ?>
            </div>
        </div>
    </div>
<?php
Modal::begin([
    'id' => 'dynamicModal',
    'options' => [
        'class' => ['modal', 'remote', 'fade']
    ],
    'clientOptions' => [
        'backdrop' => 'static',
        'keyboard' => false
    ],
    'size' => Modal::SIZE_LARGE,
    'title' => null,
    'closeButton' => false
]);
Modal::end();