<?php
use yii\grid\GridView;
use yii\helpers\Html;
/* @var $this yii\web\View */

$this->title = Yii::$app->params['siteTitle'];
?>
<div class="site-index">
    <div class="body-content">
		<div class="row">
			<div class="col-lg-12">
				<h1>Current Event: <?php echo Html::a($event->name, ['/event/view', 'id' => $event->id]);?></h1>
				<h3><?= $event->shiftSummary;?></h3>
				<?php echo GridView::widget([
					'dataProvider' => $teams,
					'layout' => '{items}',
					'columns' => [
						[
							'attribute' => 'name',
							'format' => 'raw',
							'value' => function($model){return Html::a($model->name, ['team/view', 'id' => $model->id]);},
						],
						'minTotalShifts',
						'maxTotalShifts',
						'filledShifts',
						[
							'attribute' => 'status',
							'contentOptions' => function($model, $k, $i, $c)
							{
								return ['class' => $model->statusClass];
							},
						],
					],
				]);
				?>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12">
			<?= Html::a("Add new Team", ['/team/create', 'event_id' => $event->id], ['class' => 'btn btn-success']);?>
			<?= Html::a("Copy Team from Previous Event", ['/team/copy', 'event_id' => $event->id], ['class' => 'btn btn-primary']);?>
			</div>
		</div>
    </div>
</div>

