<?php

namespace common\models;

use Yii;
use common\models\Shift;
use common\models\Event;
use common\models\Requirement;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

/**
 * This is the model class for table "team".
 *
 * @property integer $id
 * @property string $description
 * @property string $contact
 * @property string $leads
 * @property integer $event_id
 * @property string $name
 */
class Team extends \yii\db\ActiveRecord
{
	protected $_event;
	protected $_shifts;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'team';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description', 'leads'], 'string'],
            [['event_id'], 'required'],
            [['event_id'], 'integer'],
            [['contact', 'name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'description' => 'Description',
            'contact' => 'Contact Address',
            'event_id' => 'Event',
			'name' => 'Team Name',
			'statusSummary' => 'Status',
			'leads' => 'Team Leads',
        ];
    }

	//Relations
	public function getEvent($reset = false)
	{
		if($reset || !isset($this->_event))
		{
			$this->_event = $this->hasOne(Event::className(), ['id' => 'event_id']);
		}

		return $this->_event;
	}

	public function getShifts($reset = false)
	{
		if($reset || !isset($this->_shifts))
		{
			$this->_shifts = $this->hasMany(Shift::className(), ['team_id' => 'id']);
		}
		return $this->_shifts;
	}

	public function getMinTotalShifts()
	{
		$sum = 0;
		foreach($this->shifts as $shift)
		{
			$sum += $shift->minSpots;
		}

		return $sum;
	}

	public function getMaxTotalShifts()
	{
		$sum = 0;
		foreach($this->shifts as $shift)
		{
			$sum += $shift->maxSpots;
		}

		return $sum;
	}

	public function getFilledShifts()
	{
		$sum = 0;
		foreach($this->shifts as $shift)
		{
			$sum += $shift->filled;
		}

		return $sum;
	}

	public function getUserShiftCount($user_id)
	{
		$sum = 0;
		foreach($this->shifts as $shift)
		{
			if($shift->hasParticipant($user_id))
			{
				$sum++;
			}
		}

		return $sum;
	}

	public function getStatusSummary()
	{
		$filled = $min_needed = $max_needed = 0;
		foreach($this->shifts as $shift)
		{
			$filled += $shift->filled;
			$min_needed += $shift->minSpots;
			$max_needed += $shift->maxSpots;
		}

		return sprintf("%d shifts filled out of %d minimum, %d maximum",
			$filled, $min_needed, $max_needed);
	}

	public function getStatus()
	{
		$min_remaining = $extra_remaining = 0;
		foreach($this->shifts as $shift)
		{
			$filled = min($shift->filled, $shift->minSpots); 

			$min_remaining += $shift->minSpots - $filled;

			$extra_filled = $shift->filled - $filled;
			$extra_remaining += $shift->maxSpots - $shift->minSpots - $extra_filled;
		}

		$min_plural = $min_remaining == 1 ? "volunteer" : "volunteers";
		$extra_plural = $extra_remaining == 1 ? "shift" : "shifts";

		if($min_remaining <= 0 && $extra_remaining <= 0)
		{
			return "Completely Filled!";
		}

		if($min_remaining <= 0 && $extra_remaining > 0)
		{
			return sprintf("All needed shifts filled, but %u extra %s available.", 
				$extra_remaining,
				$extra_plural
			);
		}

		if($min_remaining > 0 && $extra_remaining <= 0)
		{
			return sprintf("%u %s needed.",
				$min_remaining,
				$min_plural
			);
		}

		//Min and extra both remaining
		return sprintf("%u %s needed, and %u extra %s available.", 
			$min_remaining,
			$min_plural,
			$extra_remaining,
			$extra_plural
		);
	}

	public function getStatusClass()
	{
		$min_remaining = $extra_remaining = $filled = 0;
		foreach($this->shifts as $shift)
		{
			$filled += $shift->filled;
			$shift_filled = min($shift->filled, $shift->minSpots); 

			$min_remaining += $shift->minSpots - $shift_filled;

			$extra_filled = $shift->filled - $filled;
			$extra_remaining += $shift->maxSpots - $shift->minSpots - $extra_filled;
		}


		if($min_remaining <= 0 && $extra_remaining <= 0)
		{
			return "success"; 
		}

		if($min_remaining <= 0 && $extra_remaining > 0)
		{
			return "info";
		}

		if($filled === 0)
		{
			return "danger";
		}

		return "warning";
	}

	public function getDayDataProvider($start)
	{
		$query = Shift::find();
		$query->groupBy = 'shift.id';

		return new ActiveDataProvider([
			'query' => $query
			->addSelect([new Expression("*, concat(
					greatest(least(coalesce(min_needed, 1), coalesce(max_needed, 1)) - count(participant.user_id), 0),
					'.',
					greatest(greatest(coalesce(min_needed, 1), coalesce(max_needed, 1)) 
						- least(coalesce(min_needed, 1), coalesce(max_needed, 1))
						- greatest((count(participant.user_id) - least(coalesce(min_needed, 1), coalesce(max_needed, 1))), 0),0)
				) as status")])
			->joinWith('participants')
			->where(
				"team_id = :id AND active = true AND start_time BETWEEN :start AND :end",
				[':id' => $this->id, ':start' => $start, ':end' => $start + 86399]),
			'pagination' => false,
			'sort' => [
				'defaultOrder' => [
					'start_time' => SORT_ASC,
				],
				'attributes' => [
					'status' => [
						'asc' => ['status' => SORT_ASC],
						'desc' => ['status' => SORT_DESC],
					],
					'title',
					'start_time',
				],
			],
		]);
	}

	public function copyToEvent($event_id)
	{
		$old_event = $this->event;
		$new_event = Event::findOne($event_id);

		$time_diff = ($new_event->start - $old_event->start) - floor(($new_event->start - $old_event->start) / (60*60*24));

		$new_team = new Team();
		$new_team->attributes = $this->attributes;
		$new_team->name = "Copy of " . $this->name;
		$new_team->event_id = $event_id;
		$new_team->save();

		//Copy shifts
		$old_shifts = $this->shifts;
		foreach($old_shifts as $old_shift)
		{
			$new_shift = new Shift();
			$new_shift->attributes = $old_shift->attributes;
			$new_shift->team_id = $new_team->id;
			$new_shift->start_time += $time_diff;
			$new_shift->save();
		}

		return $new_team->id;
	}
	
	public function beforeDelete()
	{
		if(!$this->event->active)
		{
			Yii::$app->session->addFlash('error', 'Teams cannot be deleted once an event is closed');
			return false;
		}

		foreach($this->shifts as $shift)
		{
			if(!$shift->delete())
			{
				return false;
			}
		}

		return true;
	}

	public function importShifts($data)
	{
		//Data should be a 2D array of the following
		//[title, start_timestamp, length, min_participants, max_participants, requirement_name]

		$count = 0;
		foreach($data as $row)
		{
			$timestamp = strtotime($row[1]);

			if(isset($row[5]))
			{
				$requirement = Requirement::findOne(['name' => $row[5]]);
			}

			$shift = new Shift();
			$shift->title = $row[0];
			$shift->start_time = $timestamp;
			$shift->team_id = $this->id;
			$shift->length = $row[2];
			$shift->min_needed = !empty($row[3]) ? $row[3] : null;
			$shift->max_needed = !empty($row[4]) ? $row[4] : null;
			$shift->requirement_id = isset($requirement) ? $requirement->id : null;
			$shift->active = true;

			if($shift->save())
			{
				$count++;
			}
		}

		Yii::$app->session->addFlash('success', sprintf("Imported %d of %d shifts successfully", $count, count($data)));
	}

	public function getVolunteerDataProvider()
	{
		return new ActiveDataProvider([
			'query' => User::find()
				->addSelect([new Expression("user.*, sum(case when participant.user_id = user.id then 1 else 0 end) as num_shifts")])
				->joinWith(['participation.shift.team'])
				->where(['team.event_id' => $this->id])
				->groupBy('user.id'),
			'sort' => [
				'defaultOrder' => [
					'username' => SORT_ASC,
				],
				'attributes' => [
					'num_shifts' => [
						'asc' => ['num_shifts' => SORT_ASC],
						'desc' => ['num_shifts' => SORT_DESC],
					],
					'username',
					'real_name',
					'burn_name',
					'email',
				],
			],
		]);
	}

	public function getScheduleDataProvider()
	{
		return new ActiveDataProvider([
			'query' => Shift::find()
				->joinWith(['team', 'participants.user'])
				->where(['shift.team_id' => $this->id])
				->orderBy([
					'start_time' => SORT_ASC, 
					'title' => SORT_ASC,
				]),
			'pagination' => false,
			'sort' => false,
		]);
	}
}
