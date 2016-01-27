<?php
/**
 * User controller, aliased as /me/<action>
 */

namespace backend\controllers;

use Yii;
use yii\web\Response;
use yii\web\Controller;
use yii\filters\VerbFilter;
use common\models\LoginForm;
use common\models\ContactForm;
use common\models\User;
use yii2mod\rbac\components\AccessControl;

class UserController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

	public function actionEvents()
	{
	}

	public function actionTeams()
	{
	}

	public function actionShifts()
	{
	}

	public function actionAjaxSearch($term = '')
	{
		Yii::$app->response->format = Response::FORMAT_JSON;
		$users = User::find()
			->where(['like', 'username', $term])
			->orWhere(['like', 'real_name', $term])
			->orWhere(['like', 'burn_name', $term])
			->orWhere(['like', 'email', $term])
			->all();
		$output = [];

		foreach($users as $user)
		{
			$output[] = [
				'id' => $user->id,
				'value' => sprintf("%s (%s) - %s", $user->username, $user->real_name, $user->email),
			];
		}
		
		return $output;
	}
}
