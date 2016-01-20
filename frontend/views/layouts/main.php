<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'Alchemy Volunteer System',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);

    $menuItems = [
        ['label' => 'Home', 'url' => ['/site/index']],
    ];
	$leftItems = [];

    if (Yii::$app->user->isGuest) {
        $menuItems[] = "<li>" . Html::a('Signup', "http://alchemyburn.com/user/register", ["target" => "_blank"]) . "</li>";
        $menuItems[] = ['label' => 'Login', 'url' => ['/site/login']];
    } else {
		$leftItems[] = [
			'label' => 'My Account',
			'items' => [
				['label' => 'My Shifts', 'url' => ['/shift/mine']],
				['label' => 'Account Settings', 'url' => ['/site/userinfo']],
			],
		];
        $menuItems[] = [
            'label' => 'Logout (' . Yii::$app->user->identity->display_name . ')',
            'url' => ['/site/logout'],
            'linkOptions' => ['data-method' => 'post']
        ];
    }

	echo Nav::widget([
		'options' => ['class' => 'navbar-nav navbar-left'],
		'items' => $leftItems,
	]);

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; Flashpoint Artists Initiative <?= date('Y') ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
