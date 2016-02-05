<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\bootstrap\ActiveForm;
use yii\web\JsExpression;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Shift */

$this->title = $model->title . " - " . $model->team->name . " - " . $model->event->name;
$this->params['breadcrumbs'][] = ['label' => 'Events', 'url' => ['/event/index']];
$this->params['breadcrumbs'][] = ['label' => $model->event->name, 'url' => ['/event/view', 'id' => $model->event->id]];
$this->params['breadcrumbs'][] = ['label' => $model->team->name, 'url' => ['/team/view', 'id' => $model->team->id]];
$this->params['breadcrumbs'][] = Yii::$app->formatter->asDatetime($model->start_time) . " - " . $model->title;
?>
<div class="shift-view">

    <h1>Shift: <?= Html::encode($model->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'team.name',
            'title',
            'length',
            'start_time:datetime',
			'min_needed',
			'max_needed',
            'active:boolean',
            'requirement.name',
        ],
    ]) ?>

	<h3>Volunteers</h3>
	<?php echo GridView::widget([
		'dataProvider' => $dp,
		'columns' => [
			'user.username',
			'user.real_name',
			'user.burn_name',
			'user.email',
            [
				'class' => 'yii\grid\ActionColumn',
				'controller' => 'participant',
				'template' => '{delete}', 
			],
		],
	]);?>
	
	<h4>Add Volunteer</h4>
	<p class="help-block">Warning, this will add a user to the shift, regardless of any caps or requirements</p>
	<?php $activeForm = ActiveForm::begin(['id' => 'add-participant-form']); ?>

		<?= $activeForm->field($form, 'user_search')->widget(\yii\jui\AutoComplete::classname(), [
			'clientOptions' => [
				'source' => new JsExpression("function(request, response) {
						$.getJSON('" . Url::to(['/user/ajax-search'])  . "', {
							term: request.term
						}, response);
					}"),
				'select' => new JsExpression("function(event, ui) {
					$('#addparticipantform-user_id').val(ui.item.id);
				}"),
			],
			'options' => [
				'class' => 'form-control ui-autocomplete-input',
				'placeholder' => 'Search by username, email, burn name, or real name',
			],
		]);?>
		<?= Html::activeHiddenInput($form, 'user_id');?>

		<div class="form-group">
			<?= Html::submitButton('Copy', ['class' => 'btn btn-primary', 'name' => 'copy-button']) ?>
		</div>

	<?php ActiveForm::end(); ?>

</div>
