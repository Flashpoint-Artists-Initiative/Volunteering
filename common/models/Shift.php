<?php

namespace common\models;

use Yii;
use yii\helpers\Html;
use common\models\Participant;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "shift".
 *
 * @property integer $id
 * @property integer $team_id
 * @property string $title
 * @property integer $length
 * @property integer $start_time
 * @property integer $active
 * @property integer $requirement_id
 * @property integer $min_needed
 * @property integer $max_needed
 */
class Shift extends \yii\db\ActiveRecord
{
	const DATE_FORMAT = "M j Y, h:i A";

	protected $_participants;
	protected $_team;
	protected $_event;

	public $formStart;
	public $participant_num;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shift';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['team_id', 'title', 'length'], 'required'],
            [['team_id', 'length', 'start_time', 'active', 'requirement_id', 'min_needed', 'max_needed'], 'integer'],
            [['title'], 'string', 'max' => 255],
			['formStart', 'date', 'timestampAttribute' => 'start_time', 'format' => 'php:' . self::DATE_FORMAT],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'team_id' => 'Team ID',
            'title' => 'Title',
            'length' => 'Length',
            'start_time' => 'Start Time',
			'formStart' => 'Start Time',
			'min_needed' => 'Minimum Volunteers Needed',
			'max_needed' => 'Maximum Volunteers Needed',
            'participant_num' => 'Number of Participants',
            'active' => 'Active',
            'requirement_id' => 'User Requirement',
			'volunteerList' => 'Volunteers',
			'filled' => 'Spots Filled',
        ];
    }

	public function afterFind()
	{
		if(isset($this->start_time))
		{
			$this->formStart = date(self::DATE_FORMAT, $this->start_time);
		}
	}

	public function getTeam($reset = false)
	{
		if($reset || !isset($this->_team))
		{
			$this->_team = $this->hasOne(Team::className(), ['id' => 'team_id']);
		}

		return $this->_team;
	}

	public function getParticipants($reset = false)
	{
		if($reset || !isset($this->_participants))
		{
			$this->_participants = $this->hasMany(Participant::className(), ['shift_id' => 'id']);
		}

		return $this->_participants;
	}

	protected function getEvent($reset = false)
	{
		if($reset || !isset($this->_event))
		{
			$this->_event = $this->team->event;
		}

		return $this->_event;
	}

	public function getRequirement()
	{
		return $this->hasOne(Requirement::className(), ['id' => 'requirement_id']);
	}

	public function getFilled()
	{
		return count($this->participants);
	}

	public function getMinSpots()
	{
		if(isset($this->max_needed) && !isset($this->min_needed))
		{
			return $this->max_needed;
		}

		return $this->min_needed;
	}

	public function getMaxSpots()
	{
		if(isset($this->min_needed) && !isset($this->max_needed))
		{
			return $this->min_needed;
		}

		return $this->max_needed;
	}

	public function getRemainingSpots()
	{
		return $this->maxSpots - $this->filled;
	}

	public function canBeFilled($user_id = null)
	{
		if($this->requirement)
		{
			return $this->RemainingSpots !== 0 && $this->requirement->check($user_id);
		}

		return $this->RemainingSpots !== 0;
	}

	public function getStatus()
	{
		if($this->filled < $this->minSpots)
		{
			if($this->maxSpots === $this->minSpots)
			{
				return sprintf("Needs %u more volunteers", $this->remainingSpots);
			}

			$count = $this->minSpots - $this->filled;
			$word = $count == 1 ? "volunteer" : "volunteers";
			return sprintf("Needs at least %u more %s", $count, $word);
		}

		if($this->remainingSpots > 0)
		{
			return sprintf("Minimum reached! Room for %u more", $this->remainingSpots);
		}

		return "Filled";
	}

	public function getStatusClass()
	{
		if($this->filled === 0)
		{
			return 'danger';
		}
		if($this->filled < $this->minSpots)
		{
				return 'warning';
		}

		if($this->remainingSpots > 0)
		{
			return 'info';
		}

		return 'success';
	}

	protected function hasParticipant($user_id)
	{
		foreach($this->participants as $participant)
		{
			if($participant->user_id == $user_id)
			{
				return true;
			}
		}

		return false;
	}

	public function generateSignupLink($user_id = null)
	{
		if($user_id == null)
		{
			return "Login to sign up";
		}

		$classes = 'btn btn-xs';
		$title = '';
		$url = '#';

		if($this->hasParticipant($user_id) == true)
		{
			$classes .= ' btn-danger';
			$url = ["shift/cancel", "id" => $this->id];
			$title = "Cancel";
		}
		elseif($this->remainingSpots === 0)
		{
			$classes .= ' btn-default disabled';
			$title = "Shift Full";
		}
		elseif($this->canBeFilled($user_id))
		{
			$classes .= ' btn-primary';
			$url = ["shift/signup", "id" => $this->id];
			$title = "Sign Up";
		}
		else
		{
			$classes .= ' btn-default disabled';
			$url = '#';
			$title = 'Not Allowed';
		}
		
		if(!$this->event->active)
		{
			$classes = 'btn btn-xs btn-default disabled';
			$url = '#';
			$title = 'Event Closed';
		}

		return Html::a($title, $url, ['class' => $classes]);
	}

	public function addParticipant($user_id)
	{
		if($this->hasParticipant($user_id) == true)
		{
		
			Yii::$app->session->addFlash("error", "You are already signed up for this shift.");
			return false;
		}

		if(!$this->canBeFilled($user_id))
		{
			Yii::$app->session->addFlash("error", "This shift is full, or you do not meet it's requirements.");
			return false;
		}

		$participant = new Participant();
		$participant->shift_id = $this->id;
		$participant->user_id = $user_id;

		if($participant->save())
		{
			Yii::$app->session->addFlash("success", sprintf("You are now signed up for the %s '%s' shift on %s", 
				$this->team->name, $this->title, date("M j \a\t g:i a", $this->start_time)));
			return true;
		}

		Yii::$app->session->addFlash("error", "There was an error saving: " . print_r($participant->errors, true));
		return false;
	}

	public function removeParticipant($user_id)
	{
		if($this->hasParticipant($user_id) == true)
		{
			foreach($this->participants as $p)
			{
				if($p->user_id == $user_id)
				{
					$p->delete();
					Yii::$app->session->addFlash("success", sprintf("You have been removed from the %s '%s' shift on %s", 
						$this->team->name, $this->title, date("M j \a\t g:i a", $this->start_time)));
					return true;
				}
			}
		}

		Yii::$app->session->addFlash("error", "You must be signed up for a shift to be removed from it.");
		return false;
	}

	public function getVolunteerList()
	{
		$output = [];
		foreach($this->participants as $p)
		{
			$output[] = Html::encode($p->user->username);
		}
		return implode("<br>", $output);
	}

	public function beforeDelete()
	{
		if(!$this->team->event->active)
		{
			Yii::$app->session->addFlash('error', 'Shifts cannot be deleted once an event is closed');
			return false;
		}

		foreach($this->participants as $p)
		{
			if(!$p->delete())
			{
				return false;
			}
		}

		return true;
	}
}
