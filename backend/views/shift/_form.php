<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model app\models\Shift */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="shift-form">
<?php var_dump($model->errors);?>

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'title')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'length')->textInput() ?>
	<p>In Hours</p>

    <?= $form->field($model, 'formStart')->widget(DateTimePicker::classname(), [
		'pluginOptions' => [
			'defaultTime' => false,
			'format' => 'M d  yyyy, H:ii P',
			'startDate' => $model->team->event->formStart,
			'endDate' => $model->team->event->formEnd,
		]
	]) ?>

    <?= $form->field($model, 'min_needed')->textInput() ?>

    <?= $form->field($model, 'max_needed')->textInput() ?>

    <?= $form->field($model, 'active')->checkbox() ?>

    <?= $form->field($model, 'requirement_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
