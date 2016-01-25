<?php 
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<?php $form = ActiveForm::begin();?>
    <div class="form-group">
		<?= $form->field($model, 'username')->textInput(); ?>
		<p class="help-block">This will be displayed publically to other people on the volunteer site.</p>
    </div>
    <div class="form-group">
    	<?= $form->field($model, 'real_name')->textInput(); ?>
		<p class="help-block">Please use the name you go by at the burn, including your last name if applicable.  This won't be shared with anyone except the team leads, and will only be used internally and for contacting you.</p> 
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
