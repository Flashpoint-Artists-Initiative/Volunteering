<?php
use yii\grid\GridView;
use yii\helpers\Html;
/* @var $this yii\web\View */
?>
<h1>Signup for <?php echo sprintf("%s %s", Html::encode($event->name), Html::encode($team->name));?> shifts</h1>
<?php foreach($days as $timestamp => $dp):?>
<h3><?php echo date('l, M j, Y', $timestamp);?></h3>
<?php echo GridView::widget([
	'dataProvider' => $dp,
	'columns' => [
		'title',
		[
			'label' => 'Time',
			'attribute' => 'start_time',
			'format' => 'text',
			'value' => function($model){
				$start = date('g:i a', $model->start_time);
				$end = date('g:i a', $model->start_time + ($model->length * 3600));
				return sprintf("%s - %s (%u hours)", $start, $end, $model->length);
			},
		],
		'status',
		[
			'label' => 'Signup',
			'format' => 'raw',
			'value' => function($model){
				return $model->generateSignupLink(Yii::$app->user->id);
			},
		],
	],
]);?>
<?php endforeach;?>
