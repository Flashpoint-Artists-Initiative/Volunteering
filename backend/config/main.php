<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

$config = [
	'homeUrl' => '/admin',
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [
		'rbac' => [
			'class' => 'yii2mod\rbac\Module',
				'controllerMap' => [
					'assignment' => [
						'class' => 'yii2mod\rbac\controllers\AssignmentController',
						'userClassName' => 'common\models\User',
						'usernameField' => 'username',
						'searchClass' => 'common\models\UserSearch',
					]
				]
		],
		'gridview' =>  [
			'class' => 'kartik\grid\Module'
		]
	],
    'components' => [
        'user' => [
			'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
		'request' => [
			'baseUrl' => '/admin',
		],
		'authManager' => [
			'class' => 'yii\rbac\DbManager',
		],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => [
            '50.202.126.170', //Work
            '198.2.191.*', //Work
            '98.251.87.162', //Home
            '73.207.58.46', //Home
            '2601:c1:400:b32d:cc3e:4473:8db3:e246', //Home
            '24.126.245.73', //Home
        ],
    ];
}

return $config;
