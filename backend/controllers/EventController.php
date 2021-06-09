<?php

namespace backend\controllers;

use Yii;
use common\models\Event;
use common\models\Team;
use common\models\EventSearch;
use common\models\Participant;
use common\models\User;
use common\models\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii2mod\rbac\components\AccessControl;
use backend\models\EventCopyForm;

/**
 * EventController implements the CRUD actions for Event model.
 */
class EventController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
			],
        ];
    }

    /**
     * Lists all active Event models.
     * @return mixed
     */
    public function actionIndex()
    {
		$active = new ActiveDataProvider([
			'query' => Event::find()->addOrderBy('start DESC')->where(['active' => true]),
			'pagination' => false,
		]);

		$inactive = new ActiveDataProvider([
			'query' => Event::find()->addOrderBy('start DESC')->where(['active' => false]),
		]);

        return $this->render('index', [
			'activeEvents' => $active,
			'inactiveEvents' => $inactive,
        ]);
    }

    public function actionAdmin()
    {
		$dataProvider = new ActiveDataProvider([
			'query' => Event::find(),
			'pagination' => false,
		]);

        return $this->render('admin', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Event model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
		$event = $this->findModel($id);
		$dp = new ActiveDataProvider([
			'query' => Team::find()->where(['event_id' => $id])->addOrderBy('name asc'), 
			'pagination' => false,
		]);

        return $this->render('view', [
            'model' => $event,
			'dataProvider' => $dp,
        ]);
    }

    /**
     * Creates a new Event model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Event();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Event model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Event model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
		$model = $this->findModel($id);
		if($model->delete() !== false)
		{
			Yii::$app->session->addFlash('success', 'Event deleted.');
		}
			
        return $this->redirect(['index']);
    }

	public function actionCopy($id)
	{
		$model = new EventCopyForm();
		$model->event_id = $id;

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

	public function actionSchedule($id)
	{
		$model = $this->findModel($id);
		$dp = $model->scheduleDataProvider;

		return $this->render('schedule', [
			'event' => $model,
			'dp' => $dp,
		]);
	}

	public function actionVolunteers($id)
	{
		$model = $this->findModel($id);
		$searchModel = new UserSearch();

        $query = $searchModel->searchQuery(Yii::$app->request->queryParams);
		
		$dp = $model->volunteerDataProvider;
		$dp->pagination = false;


		return $this->render('volunteers', [
			'event' => $model,
			'dp' => $dp,
			'searchModel' => $searchModel,
		]);
	}

	public function actionExport($id)
	{
		$model = $this->findModel($id);
		$report = $model->generateParticipantReport();

		Yii::$app->response->acceptMimeType = "text/csv";

		$out = fopen('php://output', 'w');
		foreach($report as $line)
		{
			fputcsv($out, $line);
		}

		Yii::$app->response->setDownloadHeaders($model->name . ' Participant Export.csv', 'text/csv');
		fclose($out);
	}

	public function actionReport($id)
	{
		$model = $this->findModel($id);
		$report = $model->generateReport();

		Yii::$app->response->acceptMimeType = "text/csv";

		$out = fopen('php://output', 'w');
		foreach($report as $line)
		{
			fputcsv($out, $line);
		}
		
		Yii::$app->response->setDownloadHeaders($model->name . ' Report.csv', 'text/csv');
		fclose($out);
	}

    /**
     * Finds the Event model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Event the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Event::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
