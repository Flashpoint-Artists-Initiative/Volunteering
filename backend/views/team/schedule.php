<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use kartik\grid\GridView;

$this->title = $event->name . " Schedule";
?>
<h1><?= Html::encode($event->name);?> Schedule</h1>
<?php echo GridView::widget([
	'dataProvider' => $dp,
	'columns' => [
		[
			'label' => 'Date',
			'attribute' => 'start_time',
			'format' => 'date',
		],
		[
			'label' => 'Time',
			'attribute' => 'timeAndLength',
		],
		'title',
		[
			'label' => 'Volunteers',
			'attribute' => 'volunteerList',
			'format' => 'raw',
		],
		[
			'label' => 'Volunteer Names',
			'attribute' => 'volunteerNameList',
			'format' => 'raw',
		],
		[
			'class' => 'yii\grid\ActionColumn',
			'controller' => 'shift',
			'urlCreator' => function($action, $model, $key, $index){
				return Url::toRoute(['/shift/' . $action, 'id' => $key]);
			},
		],
	],
	'toolbar' => '{export}',
	'export' => [
		'label' => 'Export',
		'showConfirmAlert' => false,
	],
	'panel'=>[
		'type'=>GridView::TYPE_DEFAULT,
	],
	'exportConfig' => [
		'csv' => [
			'colDelimiter' => ',',
			'rowDelimiter' => "\n",
		],
	],
]);?>
