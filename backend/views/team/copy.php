<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
?>
<div class="body-content">
	<div class="row">
		<div class="col-lg-12">
			<h1>Copy a Team</h1>
			<p>This will copy a team and all of it's shifts (but not volunteers) to the selected new event.
				Each shift will be moved relative to the start of the new event's date.  So a shift on the first day of the old event at 10am will be set to the first day of the new event at 10am.</p> 
            <?php $form = ActiveForm::begin(['id' => 'team-copy-form']); ?>

                <?= $form->field($model, 'team_id')->dropdownList($model->teamList, ['prompt' => 'Select a team to copy']); ?>
                <?= $form->field($model, 'event_id')->dropdownList($model->eventList, ['prompt' => 'Select an event to copy it to']); ?>

                <div class="form-group">
                    <?= Html::submitButton('Copy', ['class' => 'btn btn-primary', 'name' => 'copy-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>
			
		</div>
	</div>
</div>

