<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
?>


<h2>Import Shifts</h2>
<?php $form = ActiveForm::begin(); ?>
<p>Paste CSV data here, one line per shift, in the following format:</p>
<p><pre>title, start_timestamp, length, min_participants, max_participants (optional), requirement_name (optional)

Example:
Ranger,4/29/2016 12:00:00,2,3,5
Khaki,4/29/2016 12:00:00,1,1,,Ranger Training 2016
</pre></p>
<div class="form-group">
	<?= $form->field($model, 'data')->textarea();?>
</div>

<div class="form-group">
	<?= Html::submitButton('Import Shifts', ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>
