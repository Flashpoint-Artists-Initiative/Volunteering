<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EventSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Events';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Event', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
		'filterPosition' => false,
        'columns' => [
			[
				'format' => 'raw',
				'attribute' => 'name',
				'value' => function($data){
					return Html::a(Html::encode($data->name), ['/event/view', 'id' => $data->id]);
				},
			],
			[
            	'attribute' => 'start',
				'value' => 'formStart',
			],
			[
            	'attribute' => 'end',
				'value' => 'formEnd',
			],
			'duration',
        ],
    ]); ?>

</div>
