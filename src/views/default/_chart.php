<?php

use simialbi\yii2\chart\models\axis\CategoryAxis;
use simialbi\yii2\chart\models\axis\ValueAxis;
use simialbi\yii2\chart\models\data\JSONParser;
use simialbi\yii2\chart\models\series\ColumnSeries;
use simialbi\yii2\chart\widgets\LineChart;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $lastQuestion simialbi\yii2\voting\models\Question|null */
?>

    <h3><?= Html::encode($lastQuestion->subject); ?></h3>
<?php if (!empty($lastQuestion->description)): ?>
    <?= Yii::$app->formatter->asNtext($lastQuestion->description); ?>
<?php endif; ?>
<?php $series = new ColumnSeries([
    'dataFields' => [
        'categoryX' => 'answer',
        'valueY' => 'count'
    ],
    'name' => Yii::t('simialbi/voting/answer', 'Answers')
]); ?>
<?php $series->appendix = new JsExpression("
{$series->varName}.columns.template.tooltipText = '{valueY.value}';
{$series->varName}.columns.template.adapter.add('fill', function(fill, target) {
    return chartResultChart.colors.getIndex(target.dataItem.index);
});
var bullet = {$series->varName}.bullets.push(new am4charts.LabelBullet());
bullet.interactionsEnabled = false;
bullet.dy = 50;
bullet.label.text = '{valueY}';
bullet.label.fontSize = '40px';
bullet.label.fill = am4core.color('#ffffff');"); ?>
<?= LineChart::widget([
    'series' => [$series],
    'options' => [
        'id' => 'resultChart',
        'style' => [
            'width' => '100%',
            'height' => '400px',
            'max-height' => '100vh'
        ]
    ],
    'axes' => [
        new CategoryAxis([
            'dataFields' => [
                'category' => 'answer'
            ]
        ]),
        new ValueAxis()
    ],
    'dataSource' => [
        'url' => Url::to(['default/chart-data', 'questionId' => $lastQuestion->id]),
        'parser' => new JSONParser([
            'options' => [
                'emptyAs' => 0,
                'numberFields' => ['count']
            ]
        ]),
        'reloadFrequency' => 5000
    ]
]); ?>