<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Requirement */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="requirement-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="form-group">
    	<?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>
		<p class="help-block">Please include the relevant event and year for the requirement, if applicable.  That way we can tell the difference between each year's Ranger Trainings, etc.
	</div>

    <div class="form-group">
		<?= $form->field($model, 'team')->widget(\yii\jui\AutoComplete::classname(), [
			'clientOptions' => [
				'source' => new JsExpression("function(request, response) {
						$.getJSON('" . Url::to(['/requirement/team-ajax-search'])  . "', {
							term: request.term
						}, response);
					}"),
			],
			'options' => [
				'class' => 'form-control ui-autocomplete-input',
			],
		]);?>
		<p class="help-block">Optional. The team name associated with this requirement.</p>
	</div>

    <div class="form-group">
    	<?= $form->field($model, 'error_message')->textInput(['maxlength' => 255]) ?>
		<p class="help-block">The error someone will see when attempting to signup for a shift without this requirement.</p>
	</div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
