<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="text-center alert alert-info">
	Alchemy 2017 Volunteer shifts are posted.  Log in to see them.
</div>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

	<p>Login or <?= Html::a('create an account', ['site/signup']);?> to sign up for volunteer shifts.</p>
<?php
if (isset(Yii::$app->params['motd'])) {
    echo '<p>' . Yii::$app->params['motd'] . '</p>';
}
?>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

                <?= $form->field($model, 'username') ?>

                <?= $form->field($model, 'password')->passwordInput() ?>

                <?= $form->field($model, 'rememberMe')->checkbox() ?>

                <div style="color:#999;margin:1em 0">
                    If you forgot your password you can <?= Html::a('reset it', ['/site/request-password-reset']);?>. 
                </div>

                <div class="form-group">
                    <?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
