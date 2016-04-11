<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

$this->title = $event->name . " volunteers";
?>
<h1><?= Html::encode($event->name);?> Volunteers</h1>
<p>
	<?= Html::a('Download Emails', ['volunteer-emails', 'id' => $event->id], ['class' => 'btn btn-info']) ?>
</p>
<?php echo GridView::widget([
	'dataProvider' => $dp,
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
