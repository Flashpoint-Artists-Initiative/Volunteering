<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\Requirement */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Requirements', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="requirement-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
			'team',
			'errorMessageString',
        ],
    ]) ?>

</div>

<h4>Volunteers with this Requirement</h4>
<?php echo GridView::widget([
	'dataProvider' => $dp,
	'columns' => [
		[
			'attribute' => 'username',
			'format' => 'raw',
			'value' => function($model, $k, $i, $c) {
				return Html::a($model->username, ['/user/view', 'id' => $model->id]);
			},
		],
		'real_name',
		'burn_name',
		'email',
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
