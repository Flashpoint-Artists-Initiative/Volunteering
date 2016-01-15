<?php
use yii\grid\GridView;
use yii\helpers\Html;
/* @var $this yii\web\View */

$this->title = Yii::$app->params['siteTitle'];
?>
<div class="site-index">
    <div class="body-content">
		<div class="row">
			<div class="col-lg-12">
				<h1><?php echo Html::encode($event->name);?></h1>
				<?php echo GridView::widget([
					'dataProvider' => $teams,
					'layout' => '{items}',
					'columns' => [
						[
							'attribute' => 'name',
							'format' => 'raw',
							'value' => function($model){return Html::a($model->name, ['team/view', 'id' => $model->id]);},
						],
						'status',
					],
				]);
				?>
			</div>
		</div>
    </div>
</div>

