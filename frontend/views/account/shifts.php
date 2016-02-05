<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;

$this->title = "My Shifts - " . Html::encode($event->name);
?>

<h2>Your Shifts for <?= Html::encode($event->name);?></h2>
<?php if(count($data) > 0):?>
<?php foreach($data as $timestamp => $participants):?>
	<h3><?= date('l, F j, Y', $timestamp);?></h3>
	<?php echo GridView::widget([
		'dataProvider' => new ArrayDataProvider([
			'allModels' => $participants,
			'sort' => ['attributes' => ['shift.start_time']],
		]), 
		'layout' => '{items}',
		'columns' => [
			'shift.team.name',
			'shift.title',
			[
				'label' => 'Time',
				'attribute' => 'status.start_time',
				'format' => 'text',
				'value' => function($model){
					$start = date('g:i a', $model->shift->start_time);
					$end = date('g:i a', $model->shift->start_time + ($model->shift->length * 3600));
					return sprintf("%s - %s (%u hours)", $start, $end, $model->shift->length);
				},
			],
			[
				'label' => 'Cancel',
				'format' => 'raw',
				'value' => function($model){
					return $model->shift->generateSignupLink(Yii::$app->user->id);
				},
			],
		],
		]);?>
<?php endforeach;?>
<?php else:?>
<p>You haven't signed up for any shifts for this event.
<?php if($event->active):?>
	<?= Html::a("Sign up", ['/'], ['class' => 'btn btn-primary btn-xs']);?> for something!
<?php endif;?>
</p>
<?php endif;?>

<h3>View other events</h3>
<?php echo GridView::widget([
	'dataProvider' => new ArrayDataProvider([
		'allModels' => $events,
		'sort' => ['attributes' => ['start']],
	]),
	'layout' => '{items}',
	'columns' => [
		[
			'label' => 'Event',
			'format' => 'raw',
			'value' => function($model){
				return Html::a($model->name, ['/account/shifts', 'id' => $model->id]);
			},
		],
		[
			'label' => 'Shifts taken',
			'attribute' => 'userShiftCount',
		],
	],
]);?>
