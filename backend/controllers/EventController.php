<?php

namespace backend\controllers;

use Yii;
use common\models\Event;
use common\models\Team;
use common\models\EventSearch;
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
		$active = new \yii\data\ActiveDataProvider([
			'query' => Event::find()->addOrderBy('start DESC')->where(['active' => true]),
			'pagination' => false,
		]);

		$inactive = new \yii\data\ActiveDataProvider([
			'query' => Event::find()->addOrderBy('start DESC')->where(['active' => false]),
		]);

        return $this->render('index', [
			'activeEvents' => $active,
			'inactiveEvents' => $inactive,
        ]);
    }

    public function actionAdmin()
    {
		$dataProvider = new \yii\data\ActiveDataProvider([
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
