<?php

namespace backend\controllers;

use Yii;
use common\models\Shift;
use common\models\Team;
use common\models\Event;
use common\models\ShiftSearch;
use common\models\Requirement;
use common\models\Participant;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii2mod\rbac\components\AccessControl;
use yii\data\ActiveDataProvider;

/**
 * ShiftController implements the CRUD actions for Shift model.
 */
class ParticipantController extends Controller
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
	
	public function actionDelete($shift_id, $user_id)
	{
		if(!$this->isEventActive($shift_id))
		{
			Yii::$app->session->addFlash("error", "Closed events cannot be modified.");
			return $this->goBack();
		}

		$model = Participant::findOne(['shift_id' => $shift_id, 'user_id' => $user_id])->delete();

		return $this->redirect(['/shift/view', 'id' => $shift_id]);
	}

    protected function isEventActive($shift_id)
    {
        if (($model = Shift::findOne($shift_id)) !== null) {
			return $model->team->event->active;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
