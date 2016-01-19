<?php
use yii\helpers\Html;
?>
<div class="container">
	<div class="row">
		<div class="col-md-6">
			<h3><?php echo Html::a("Assignment", ['/rbac/assignment']);?></h3>
		</div>
		<div class="col-md-6">
			<h3><?php echo Html::a("Rules", ['/rbac/rule']);?></h3>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<h3><?php echo Html::a("Roles", ['/rbac/role']);?></h3>
		</div>
		<div class="col-md-6">
			<h3><?php echo Html::a("Permission", ['/rbac/permission']);?></h3>
		</div>
	</div>
</div>
