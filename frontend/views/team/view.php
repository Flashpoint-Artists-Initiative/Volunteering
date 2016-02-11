<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;

/* @var $this yii\web\View */
$this->title = $event->name . " - " . $team->name;
?>
<h1>Signup for <?php echo sprintf("%s %s", Html::encode($event->name), Html::encode($team->name));?> shifts</h1>
<p><strong>Team Leads:</strong> <?= Html::encode($team->leads);?></p>
<p><strong>Contact:</strong> <?php echo Html::mailto($team->contact, $team->contact);?></p>
<p><?php echo Yii::$app->formatter->asHtml($team->description);?></p>
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
				return $model->timeAndLength;
			},
		],
		[
			'attribute' => 'status',
			'contentOptions' => function($model, $k, $i, $c)
			{
				return ['class' => $model->statusClass];
			},
		],
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
