<?php
namespace frontend\controllers;

use Yii;
use common\models\LoginForm;
use common\components\MDateTime;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use common\models\Shift;
use common\models\Participant;
use common\models\Event;

/**
 * Account controller
 */
class AccountController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
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

	public function actionShifts($id = null)
	{
		if(!isset($id))
		{
			$id = Yii::$app->params['currentEvent'];
		}

		$data = Participant::findUserEventDataByDay($id, Yii::$app->user->id);
		$events = Event::find()->where(['not', ['id' => $id]])->all();

		return $this->render('shifts', [
			'data' => $data,
			'event' => Event::findOne($id),
			'events' => $events,
		]);
	}

	public function actionSettings()
	{
		$user = Yii::$app->user->identity;
		$user->setScenario('update');
		if($user->load(Yii::$app->request->post()) && $user->save())
		{
			Yii::$app->session->addFlash("success", "Thank you for filling out your user information");
			return $this->redirect('/site/index');
		}

		return $this->render('settings', [
			'model' => $user,
		]);
	}

	public function actionQualifications()
	{
		$requirements = Yii::$app->user->identity->requirements;

		$output = [];

		foreach($requirements as $req)
		{
			if(!isset($output[$req->team]))
			{
				$output[$req->team] = [];
			}

			$output[$req->team][] = $req->name;
		}
		
		return $this->render('qualifications', [
			'output' => $output,
		]);
	}
}
