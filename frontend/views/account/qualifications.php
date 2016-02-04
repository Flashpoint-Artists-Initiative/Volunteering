<?php
use yii\grid\GridView;
use yii\helpers\Html;

$this->title = "My Qualifications";
?>
<h2>My Shift Qualifications</h2>
<p>If you have gone through training or are elligible to sign up for restricted shifts (such as Ranger or Khaki training), the record for those will show here.</p>
<?php if(empty($output)):?>
	<p>You don't have any shift qualifications on record.</p>
<?php else:?>
	<?php foreach($output as $team => $data):?>
		<h4><?= Html::encode($team);?></h4>
		<ul>
		<?php foreach($data as $item):?>
			<li><?= Html::encode($item);?></li>
		<?php endforeach;?>
		</ul>
	<?php endforeach;?>
<?php endif;?>
<p>If you feel like there are qualifications missing from your account that are preventing you from signing up for certain shifts, please contact the team leads for that team.</p>
