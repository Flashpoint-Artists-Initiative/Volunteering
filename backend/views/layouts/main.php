<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use backend\assets\AppAsset;
use common\widgets\Alert;

$url = parse_url(Url::base(true));
$base_url = $url['scheme'] . "://" . $url['host'];

$menu_items = [['label' => 'Volunteer Frontend', 'url' => $base_url]];

if(Yii::$app->params['websiteLinks'])
{
	$menu_items = array_merge($menu_items, Yii::$app->params['websiteLinks']);
}

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
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
                'brandLabel' => Yii::$app->name, 
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);

			$eventItems = [
				['label' => 'All Events','url' => ['/event/index']],
			];
			$adminItems = [
				'label' => 'Admin',
				'items' => [
					['label' => 'Auth', 'url' => ['/rbac']],
					['label' => 'Teams','url' => ['/team/index']],
					['label' => 'Settings', 'url' => ['/settings']],
				],
			];

			if(Yii::$app->user->can('administrator'))
			{
				$eventItems[] = '<li class="divider">';
				$eventItems[] = ['label' => 'Admin Events', 'url' => ['/event/admin']];

			}

            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-left'],
                'items' => [
                    ['label' => 'All Events', 'url' => ['/event/index']],
					['label' => 'User Requirements', 'items' => [
						['label' => 'Create Requirements', 'url' => ['/requirement/index']],
						['label' => 'Assign Requirements', 'url' => ['/requirement/assign']],
					]],
					Yii::$app->user->can('administrator') ? $adminItems : '',
                ],
            ]);
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => [
					[
						'label' => 'Return to Website', 
						'items' => $menu_items,
					],
					Yii::$app->user->isGuest ? '' : ['label' => 'Logged in as ' . Yii::$app->user->identity->username, 'url' => ['/#']],
                    Yii::$app->user->isGuest ?
                        ['label' => 'Login', 'url' => ['/site/login']] :
                        ['label' => 'Logout', 'url' => ['/site/logout'],
                            'linkOptions' => ['data-method' => 'post']],
                ],
            ]);
            NavBar::end();
        ?>

        <div class="container">
        	<?= Alert::widget() ?>
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['sidebar']) ? $this->params['sidebar'] : [],
            ]) ?>
            <?= $content ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
        	<p class="pull-left">&copy; <?= Yii::$app->params['copyright'] . " " . date('Y') ?></p>
			<p class="pull-right">This site is still a work in progress, please contact 
				<?= Html::mailto(Yii::$app->params['adminEmail'], Yii::$app->params['adminEmail']);?> with any issues or suggestions.</p>
        </div>
    </footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
