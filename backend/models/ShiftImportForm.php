<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\Team;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;

/**
 * LoginForm is the model behind the login form.
 */
class ShiftImportForm extends Model
{
	public $data;
	public $team_id;

	protected $_team = false;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
			['data', 'required'],
        ];
    }

	public function attributeLabels()
	{
		return [
			'data' => 'CSV Data',
		];
	}

	public function getTeam()
	{
        if ($this->_team === false) {
            $this->_team = Team::findOne($this->team_id);
        }

        if ($this->_team === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        return $this->_team;
	}

	public function import()
	{
		$team = $this->getTeam();

		$lines = array_map('str_getcsv', str_getcsv($this->data, "\n"));

		$team->importShifts($lines);
	}
}


