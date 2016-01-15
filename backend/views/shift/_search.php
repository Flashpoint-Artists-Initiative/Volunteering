<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ShiftSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="shift-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'team_id') ?>

    <?= $form->field($model, 'title') ?>

    <?= $form->field($model, 'length') ?>

    <?= $form->field($model, 'start_time') ?>

    <?php // echo $form->field($model, 'participant_num') ?>

    <?php // echo $form->field($model, 'active') ?>

    <?php // echo $form->field($model, 'requirement_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
