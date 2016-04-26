<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

$this->title = $event->name . " volunteers";
?>
<h1><?= Html::encode($event->name);?> Volunteers</h1>
<?php echo GridView::widget([
	'dataProvider' => $dp,
	//'filterModel' => $searchModel,
	'columns' => [
		'username',
		'real_name',
		'burn_name',
		'email',
		'num_shifts',
	],
	'toolbar' => '{export}',
	'export' => [
		'label' => 'Export',
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
