<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
?>
<div class="body-content">
	<div class="row">
		<div class="col-lg-12">
			<h1>Copy a Team</h1>
			<p>This will copy an event and all of it's team and shifts (but not volunteers) to a new event. 
				Each shift will be moved relative to the start of the new event's date.  So a shift on the first day of the old event at 10am will be set to the first day of the new event at 10am.</p> 
            <?php $form = ActiveForm::begin(['id' => 'team-copy-form']); ?>

                <?= $form->field($model, 'event_id')->dropdownList($model->eventList, ['prompt' => 'Select an event to copy']); ?>

				<?= $form->field($model, 'start_time')->widget(DatePicker::classname(), [
					'pluginOptions' => [
						'defaultTime' => false,
						'format' => 'M d  yyyy',
					]
				]) ?>

                <div class="form-group">
                    <?= Html::submitButton('Copy', ['class' => 'btn btn-primary', 'name' => 'copy-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>
			
		</div>
	</div>
</div>


