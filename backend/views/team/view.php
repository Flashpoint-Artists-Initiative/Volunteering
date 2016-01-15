<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
//use kartik\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model app\models\Team */

$this->title = $model->name . ' - ' . $model->event->name;
$this->params['breadcrumbs'][] = ['label' => 'Events', 'url' => ['/event/index']];
$this->params['breadcrumbs'][] = ['label' => $model->event->name, 'url' => ['/event/view', 'id' => $model->event->id]];
$this->params['breadcrumbs'][] = $model->name;
?>
<div class="team-view">

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
            'description:ntext',
            'contact',
			[
            	'label' => 'Event Name',
				'value' => $model->event->name,
			],
        ],
    ]) ?>
	
	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'columns' => [
			[
				'attribute' => 'title',
				'format' => 'raw',
				'value' => function($data){return Html::a(Html::encode($data->title), ['/shift/view', 'id' => $data->id]);},
			],
		],
	]);?>

	<h2>Add New Shift</h2>
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($shift, 'title')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($shift, 'length')->textInput() ?>
	<p>In Hours</p>

    <?= $form->field($shift, 'formStart')->widget(DateTimePicker::classname(), [
		'pluginOptions' => [
			'defaultTime' => false,
			'format' => 'M d  yyyy, H:ii P',
			'startDate' => $model->event->formStart,
			'endDate' => $model->event->formEnd,
		]
	]) ?>

    <?= $form->field($shift, 'min_needed')->textInput() ?>
    <?= $form->field($shift, 'max_needed')->textInput() ?>

    <?= $form->field($shift, 'active')->checkbox() ?>

    <?= $form->field($shift, 'requirement_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Create Shift', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
