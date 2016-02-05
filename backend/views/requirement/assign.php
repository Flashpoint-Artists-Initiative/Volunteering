<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = "Assign Requirements";
?>
<div>
	<h1>Assign User Requirements</h1>
	<p>Select a user to assign to</p>
	<?php echo GridView::widget([
		'dataProvider' => $dp,
        'filterModel' => $searchModel,
		'columns' => [
			'id',
			'username',
			'real_name',
			'burn_name',
			'email',
			[
				'label' => 'Assign',
				'format' => 'raw',
				'value' => function($model){
					return Html::a('Assign', ['/user/view', 'id' => $model->id], ['class' => 'btn btn-xs btn-primary']);
				},
			],
		],
	]);?>
</div>
