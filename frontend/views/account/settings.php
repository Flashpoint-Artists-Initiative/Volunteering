<?php 
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = "Account Settings";
?>
<h1>Acconut Settings</h1>
<?php $form = ActiveForm::begin();?>
	<div class="form-group">
		<?= $form->field($model, 'real_name')->textInput(); ?>
		<p class="help-block">Please use your full legal name.  This will not be shared with anyone except team leads, and will only be used in emergencies.</p>
	</div>

	<div class="form-group">
		<?= $form->field($model, 'burn_name')->textInput(); ?>
		<p class="help-block">Optional. What you want to be called during the event.  Your Legal name will be used if blank.</p> 
	</div>
    <div class="form-group">
    	<?= $form->field($model, 'email')->textInput(); ?>
		<p class="help-block">Make sure your email address is correct so the team leads can contact you about your shifts.</p>
    </div>

    <div class="form-group">
    	<?= $form->field($model, 'new_password')->passwordInput(); ?>
    	<?= $form->field($model, 'new_password_repeat')->passwordInput(); ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
    </div>
<?php ActiveForm::end();?>
