<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui;

/* @var $this yii\web\View */
/* @var $model app\models\Event */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="event-form">

    <?php $form = ActiveForm::begin(); ?>
	
	<?= $form->errorSummary($model);?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'formStart')->widget(\kartik\datetime\DateTimePicker::className(), [
		'type' => 1,
		'pluginOptions' => [
			'format' => 'M d yyyy, H:ii P',
		]
	]);?>

    <?= $form->field($model, 'formEnd')->widget(\kartik\datetime\DateTimePicker::className(), [
		'type' => 1,
		'pluginOptions' => [
			'format' => 'M d yyyy, H:ii P',
		]
	]);?>

	<?= $form->field($model, 'active')->checkbox();?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
