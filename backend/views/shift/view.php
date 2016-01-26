<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Shift */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Events', 'url' => ['/event/index']];
$this->params['breadcrumbs'][] = ['label' => $model->event->name, 'url' => ['/event/view', 'id' => $model->event->id]];
$this->params['breadcrumbs'][] = ['label' => $model->team->name, 'url' => ['/team/view', 'id' => $model->team->id]];
$this->params['breadcrumbs'][] = Yii::$app->formatter->asDatetime($model->start_time);
?>
<div class="shift-view">

    <h1><?= Html::encode($this->title) ?></h1>

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
            'id',
            'team_id',
            'title',
            'length',
            'start_time:datetime',
            'participant_num',
            'active',
            'requirement_id',
        ],
    ]) ?>

</div>
