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
/* @var $height integer|null */

if (!isset($height)) {
    $height = '400px';
}
?>

    <h3><?= Html::encode($lastQuestion->subject); ?></h3>
<?php if (!empty($lastQuestion->description)): ?>
    <p><?= Yii::$app->formatter->asNtext($lastQuestion->description); ?></p>
<?php endif; ?>
<?php $series = new ColumnSeries([
    'dataFields' => [
        'categoryX' => 'answer',
        'valueY' => 'count'
    ],
    'name' => Yii::t('simialbi/voting/answer', 'Answers')
]); ?>
<?php $series->appendix = new JsExpression("
{$series->varName}.columns.template.tooltipText = '{categoryX}';
{$series->varName}.columns.template.adapter.add('fill', function(fill, target) {
    return chartResultChart{$lastQuestion->id}.colors.getIndex(target.dataItem.index);
});
var bullet = {$series->varName}.bullets.push(new am4charts.LabelBullet());
bullet.interactionsEnabled = false;
bullet.dy = 50;
bullet.label.text = '{valueY}';
bullet.label.fontSize = '40px';
bullet.label.fill = am4core.color('#ffffff');"); ?>
<?php $categoryAxis = new CategoryAxis(['dataFields' => ['category' => 'answer']]); ?>
<?= LineChart::widget([
    'series' => [$series],
    'options' => [
        'id' => 'resultChart' . $lastQuestion->id,
        'style' => [
            'width' => '100%',
            'height' => $height,
            'max-height' => '100vh'
        ]
    ],
    'axes' => [
        $categoryAxis,
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
<?php
$this->registerJs("{$categoryAxis->varName}.renderer.labels.template.fontSize = 14;");
$this->registerJs("{$categoryAxis->varName}.renderer.labels.template.wrap = true;");
$this->registerJs("{$categoryAxis->varName}.renderer.labels.template.maxWidth = 220;");
?>
