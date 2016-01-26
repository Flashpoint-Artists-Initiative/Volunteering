<?php

namespace backend\controllers;

use Yii;
use common\models\Team;
use common\models\Event;
use common\models\Shift;
use common\models\TeamSearch;
use common\models\Requirement;
use common\components\MDateTime;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii2mod\rbac\components\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use backend\models\TeamCopyForm;
use backend\models\ShiftImportForm;

/**
 * TeamController implements the CRUD actions for Team model.
 */
class TeamController extends Controller
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
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Team models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TeamSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Team model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
		$model = $this->findModel($id);

		$event = $model->event;

		$start = new MDateTime($event->start, new \DateTimeZone('EST5EDT'));
		$start->subToStart('D');

		$days = [];

		while($start->timestamp < $event->end)
		{
			$days[$start->timestamp] = $model->getDayDataProvider($start->timestamp);

			$start->add(new \DateInterval('P1D'));
		}

		$dp = new ActiveDataProvider([
			'query' => Shift::find()->where(['team_id' => $id]),
			'pagination' => false,
		]);

		$shift = new Shift();
		$shift->team_id = $model->id;
		$shift->active = true;

        if ($shift->load(Yii::$app->request->post()))
		{
			$shift->save();
		}

		$requirements = Requirement::find()->orderBy('name ASC')->all();
		$event = $model->event;

        return $this->render('view', [
            'model' => $model,
			'shift' => $shift,
			'event' => $event,
			'dataProvider' => $dp,
			'days' => $days,
			'requirements' => $requirements,
        ]);
    }

    /**
     * Creates a new Team model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($event_id = null)
    {
        $model = new Team();
		$model->event_id = $event_id;
		$events = Event::findAll(['active' => true]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
				'events' => $events,
            ]);
        }
    }

    /**
     * Updates an existing Team model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
		$events = Event::findAll(['active' => true]);

		if(!$model->event->active)
		{
			Yii::$app->session->addFlash('error', 'Teams can not be updated once an event is closed');
			Yii::$app->user->setReturnUrl(Yii::$app->request->referrer);
			return $this->goBack();
		}

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
				'events' => $events,
            ]);
        }
    }

    /**
     * Deletes an existing Team model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
		$model = $this->findModel($id);
		if($model->delete() !== false)
		{
			Yii::$app->session->addFlash('success', 'Team deleted.');
		}
			
		return $this->redirect(['/event/view', 'id' => $model->event_id]);
    }

	public function actionCopy($id = null, $event_id = null)
	{
		$model = new TeamCopyForm();
		$model->team_id = $id;
		$model->event_id = $event_id;

        if ($model->load(Yii::$app->request->post()))
		{
			$new_id = $model->copy();
        	return $this->redirect(['update', 'id' => $new_id]);
        } else {
            return $this->render('copy', [
                'model' => $model,
            ]);
        }
	}

	public function actionImport($id)
	{
		$team = $this->findModel($id);
		if(!$team->event->active)
		{
			Yii::$app->session->addFlash('error', 'Cannot add shifts when an event is inactive');
			return $this->redirect(['view', 'id' => $id]);
		}

		$model = new ShiftImportForm();
		$model->team_id = $id;
		if($model->load(Yii::$app->request->post()))
		{
			$model->import();
			return $this->redirect(['view', 'id' => $id]);
		}

		return $this->render('import', [
			'model' => $model,
		]);
	}

    /**
     * Finds the Team model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Team the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Team::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
