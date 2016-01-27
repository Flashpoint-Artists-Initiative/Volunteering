<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
//use kartik\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model app\models\Team */

$this->title = $model->event->name . ' - ' . $model->name;
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
        <?= Html::a('Add Shift', '#add-shift', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Import Shifts', ['import', 'id' => $model->id], ['class' => 'btn btn-info']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'description:html',
			'leads',
            'contact',
			'statusSummary',
        ],
    ]) ?>

	<h3>Shifts</h3>
	<?php foreach($days as $timestamp => $dp):?>
	<h4><?php echo date('l, M j, Y', $timestamp);?></h4>
	<?php echo GridView::widget([
		'dataProvider' => $dp,
		'columns' => [
			'title',
			[
				'label' => 'Time',
				'attribute' => 'start_time',
				'format' => 'text',
				'value' => function($model){
					$start = date('g:i a', $model->start_time);
					$end = date('g:i a', $model->start_time + ($model->length * 3600));
					return sprintf("%s - %s (%u hours)", $start, $end, $model->length);
				},
			],
			'min_needed',
			'max_needed',
			'filled',
			[
				'attribute' => 'status',
				'contentOptions' => function($model, $k, $i, $c)
				{
					return ['class' => $model->statusClass];
				},
			],
            [
				'class' => 'yii\grid\ActionColumn',
				'controller' => 'shift',
				'template' => $event->active ? '{view} {update} {delete}' : '{view}',
			],
		],
	]);?>
	<?php endforeach;?>
	
	<a name="add-shift"></a>
	<h2>Add New Shift</h2>
	<?php if($event->active):?>
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($shift, 'title')->textInput(['maxlength' => 255]) ?>

    <div class="form-group">
		<?= $form->field($shift, 'length')->textInput() ?>
		<p class="help-block">In Hours</p>
	</div>

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

	<?= $form->field($shift, 'requirement_id')->dropDownList(ArrayHelper::map($requirements, 'id', 'name'), ['prompt' => 'N/A']);?>

    <div class="form-group">
        <?= Html::submitButton('Create Shift', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
	<?php else:?>
	<p>The event has been closed, new shifts can not be added.</p>
	<?php endif;?>

</div>
