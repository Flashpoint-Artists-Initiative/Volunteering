<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Shift */

$this->title = 'Create Shift';
$this->params['breadcrumbs'][] = ['label' => 'Shifts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shift-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
