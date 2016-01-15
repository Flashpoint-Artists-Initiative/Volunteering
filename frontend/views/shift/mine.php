<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
?>

<h2>Your Shifts</h2>
<?php foreach($data as $event => $teams):?>
	<h3><?php echo Html::encode($event);?></h3>
	<?php foreach($teams as $team_name => $participants):?>
			<h4><?php echo Html::encode($team_name);?></h4>
			<?php echo GridView::widget([
				'dataProvider' => new ArrayDataProvider([
					'allModels' => $participants,
					'sort' => ['attributes' => ['shift.start_time']],
				]), 
				'layout' => '{items}',
				'columns' => [
					'shift.title',
					[
						'label' => 'Time & Date ',
						'attribute' => 'status.start_time',
						'format' => 'text',
						'value' => function($model){
							$start = date('l, M j g:i a', $model->shift->start_time);
							$end = date('g:i a', $model->shift->start_time + ($model->shift->length * 3600));
							return sprintf("%s - %s (%u hours)", $start, $end, $model->shift->length);
						},
					],
					[
						'label' => 'Signup',
						'format' => 'raw',
						'value' => function($model){
							return $model->shift->generateSignupLink(Yii::$app->user->id);
						},
					],
				],
				]);?>
	<?php endforeach;?>
<?php endforeach;?>
