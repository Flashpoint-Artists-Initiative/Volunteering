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
use common\models\UserRequirement;
use yii2mod\rbac\components\AccessControl;
use yii\data\ActiveDataProvider;
use backend\models\AddUserRequirementForm;

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

	public function actionView($id)
	{
		$user = $this->findModel($id);
		$dp = new ActiveDataProvider([
			'query' => UserRequirement::find()->where(['user_id' => $id]),
		]);

		$form = new AddUserRequirementForm();
		$form->user_id = $id;

        if($form->load(Yii::$app->request->post())) 
		{
			$form->addUserRequirement();
        }

		return $this->render('view', [
			'model' => $user,
			'dp' => $dp,
			'form' => $form,
		]);
	}

	public function actionDeleteRequirement($user_id, $requirement_id)
	{
		$req = UserRequirement::findOne(['user_id' => $user_id, 'requirement_id' => $requirement_id]);
		if($req)
		{
			$req->delete();
		}

		return $this->redirect(['/user/view', 'id' => $user_id]);
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

    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
