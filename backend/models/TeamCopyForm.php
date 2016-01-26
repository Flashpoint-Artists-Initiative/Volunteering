<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\Team;
use common\models\Event;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * LoginForm is the model behind the login form.
 */
class TeamCopyForm extends Model
{
	public $team_id;
	public $event_id;

	protected $_team = false;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['team_id', 'event_id'], 'required'],
            [['team_id', 'event_id'], 'integer'],
        ];
    }

	public function attributeLabels()
	{
		return [
			'team_id' => 'Original Team',
			'event_id' => 'New Event',
		];
	}

	public function getTeamList()
	{
		$events = Event::find()->addOrderBy('start DESC')->joinWith('teams')->all();

		$dropdown = [];
		foreach($events as $event)
		{
			$dropdown[Html::encode($event->dropdownName)] = ArrayHelper::map($event->teams, 'id', 'name');
		}

		return $dropdown;
	}

	public function getEventList()
	{
		$events = Event::find()->joinWith('teams')->all();
		return ArrayHelper::map($events, 'id', 'dropdownName');
	}

	public function copy()
	{
		if(!$this->validate())
		{
			return false;
		}

		$team = $this->getTeam();

		return $team->copyToEvent($this->event_id);
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
}

