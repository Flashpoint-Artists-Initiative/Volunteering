<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
?>
<div>
	<h1>User: <?= Html::encode($model->username);?></h1>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
			'username',
			'real_name',
			'burn_name',
			'email',
        ],
    ]) ?>

	<h3>Assigned Requirements</h3>
	<?php echo GridView::widget([
		'dataProvider' => $dp,
		'columns' => [
			'requirement.name',
            [
				'class' => 'yii\grid\ActionColumn',
				'controller' => 'user',
				'template' => '{delete}', 
				'urlCreator' => function($action, $model, $key, $index){
					return Url::toRoute(['user/delete-requirement',
						'user_id' => $model->user_id, 'requirement_id' => $model->requirement_id]);
				},
			],
		],
	]);?>

	<h3>Add Requirement to User</h3>
	<?php $activeForm = ActiveForm::begin(['id' => 'add-user_requirement-form']);?>
	<?= $activeForm->field($form, 'requirement_id')->dropdownList($form->requirementList);?>
	<?= Html::submitButton('Assign', ['class' => 'btn btn-success']);?>
	<?php ActiveForm::end(); ?>
</div>
