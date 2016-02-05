<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\Event */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Events', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
		<?= Html::a('Copy', ['copy', 'id' => $model->id], ['class' => 'btn btn-info']); ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
		<?= Html::a('View Volunteers', ['volunteers', 'id' => $model->id], ['class' => 'btn btn-success']); ?>
		<?= Html::a('View Schedule', ['schedule', 'id' => $model->id], ['class' => 'btn btn-success']); ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'start:datetime',
            'end:datetime',
            'active:boolean',
			'shiftSummary',
        ],
    ]) ?>
	
	<?= GridView::widget([
		'dataProvider' => $dataProvider,
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
	]);?>

			<?= Html::a("Add new Team", ['/team/create', 'event_id' => $model->id], ['class' => 'btn btn-success']);?>
			<?= Html::a("Copy Team from Previous Event", ['/team/copy', 'event_id' => $model->id], ['class' => 'btn btn-primary']);?>
</div>
