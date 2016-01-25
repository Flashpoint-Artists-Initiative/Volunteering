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
			$days[$start->timestamp] = new ActiveDataProvider([
				'query' => Shift::find()->where(
					"team_id = :id AND active = true AND start_time BETWEEN :start AND :end",
					[':id' => $model->id, ':start' => $start->timestamp, ':end' => $start->timestamp + 86400]),
				'pagination' => false,
				'sort' => [
					'defaultOrder' => [
						'start_time' => SORT_ASC,
					],
				],
			]);

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

        return $this->render('view', [
            'model' => $model,
			'shift' => $shift,
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
    public function actionCreate()
    {
        $model = new Team();
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
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
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
