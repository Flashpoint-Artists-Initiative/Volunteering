<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Signup';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Please fill out the following fields to signup:</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>

                <?= $form->field($model, 'username') ?>

                <?= $form->field($model, 'password')->passwordInput() ?>

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
                    <?= Html::submitButton('Signup', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
