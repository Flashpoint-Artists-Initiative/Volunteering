<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use moonland\tinymce\TinyMCE;

/* @var $this yii\web\View */
/* @var $model app\models\Team */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="team-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

	<?= $form->field($model, 'description')->widget(TinyMCE::className(), [
		'invalid_elements' => 'script,iframe',
	]);?>

    <?= $form->field($model, 'leads')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'contact')->textInput(['maxlength' => 255]) ?>

	<?= $form->field($model, 'event_id')->dropDownList(ArrayHelper::map($events, 'id', 'dropdownName'), ['prompt' => 'Select an active Event']);?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
