<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "event".
 *
 * @property integer $id
 * @property string $name
 * @property integer $start
 * @property integer $end
 * @property integer $active
 */
class Event extends \yii\db\ActiveRecord
{
	const DATE_FORMAT = "M j Y, h:i A";

	//Formatted date strings that tie into start/end timestamps
	public $formStart;
	public $formEnd;

	private $dateValidationFlag = false;

	protected $_teams;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'formStart', 'formEnd'], 'required'],
            [['start', 'end'], 'integer'],
            ['name', 'string', 'max' => 255],
			['formStart', 'date', 'timestampAttribute' => 'start', 'format' => 'php:' . self::DATE_FORMAT],
			['formEnd', 'date', 'timestampAttribute' => 'end', 'format' => 'php:' . self::DATE_FORMAT],
			['active', 'boolean'],
			[['start', 'end'], 'validateDates']];
    }

	public function validateDates($attr, $params)
	{
		$labels = $this->attributeLabels();

		if($attr === 'start')
		{
			if (!$this->dateValidationFlag && $this->$attr > $this->end)
			{
				$this->addError($attr, $labels[$attr] . " must come before " . $labels['end']);
				$this->dateValidationFlag = true;
			}
		}
		else
		{
			if ($this->dateValidationFlag && $this->$attr < $this->end)
			{
				$this->addError($attr, $labels[$attr] . " must come after " . $labels['start']);
				$this->dateValidationFlag = true;
			}
		}

	}

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'start' => 'Start',
            'end' => 'End',
			'formStart' => 'Start',
			'formEnd' => 'End',
            'active' => 'Active',
			'shiftSummary' => 'Summary',
        ];
    }

	public function afterFind()
	{
		if(isset($this->start))
		{
			$this->formStart = date(self::DATE_FORMAT, $this->start);
		}

		if(isset($this->end))
		{
			$this->formEnd = date(self::DATE_FORMAT, $this->end);
		}
	}

	public function init()
	{
		$this->active = false;

		parent::init();
	}

	public function getDuration()
	{
		$start = new \DateTime();
		$start->setTimestamp($this->start);
		$end = new \DateTime();
		$end->setTimestamp($this->end);

		$diff = $start->diff($end);
		return $diff->format("%d days, %h hours");
	}

	public function getTeams($reset = false)
	{
		if($reset || !isset($this->_teams))
		{
			$this->_teams = $this->hasMany(Team::className(), ['event_id' => 'id']);
		}
		return $this->_teams;
	}

	public function getDropdownName()
	{
		return sprintf("%s (%s)", $this->name, date("M Y", $this->start));
	}

	public function getMinTotalShifts()
	{
		$sum = 0;
		foreach($this->teams as $team)
		{
			$sum += $team->minTotalShifts;
		}

		return $sum;
	}

	public function getMaxTotalShifts()
	{
		$sum = 0;
		foreach($this->teams as $team)
		{
			$sum += $team->maxTotalShifts;
		}

		return $sum;
	}

	public function getFilledShifts()
	{
		$sum = 0;
		foreach($this->teams as $team)
		{
			$sum += $team->filledShifts;
		}

		return $sum;
	}

	public function getShiftSummary()
	{
		return sprintf("%d shifts filled out of %d minimum, %d maximum",
			$this->filledShifts, $this->minTotalShifts, $this->maxTotalShifts);
	}

	public function getUserShiftCount($user_id = null)
	{
		if(!isset($user_id))
		{
			$user_id = Yii::$app->user->id;
		}

		$sum = 0;
		foreach($this->teams as $team)
		{
			$sum += $team->getUserShiftCount($user_id);
		}

		return $sum;
	}

	public function beforeDelete()
	{
		if(!$this->active)
		{
			Yii::$app->session->addFlash('error', 'Cannot delete an inactive event');
			return false;
		}

		foreach($this->teams as $team)
		{
			if(!$team->delete())
			{
				return false;
			}
		}

		return true;
	}

	public function makeCopy($new_start)
	{
		$new_start += $this->start % (60*60*24);
		$new_end = $new_start + ($this->end - $this->start);
		$time_diff = ($new_start - $this->start) - floor(($new_start - $this->start) / (60*60*24));

		var_dump($time_diff);

		//Clone event
		$new_event = new Event();
		$new_event->attributes = $this->attributes;
		$new_event->formStart = date(self::DATE_FORMAT, $new_start);
		$new_event->formEnd = date(self::DATE_FORMAT, $new_end);
		$new_event->name = "Copy of " . $this->name;
		$new_event->save();

		//Clone teams
		$old_teams = $this->teams;
		foreach($old_teams as $old_team)
		{
			$new_team = new Team();
			$new_team->attributes = $old_team->attributes;
			$new_team->event_id = $new_event->id;
			$new_team->save();

			//Clone shifts
			$old_shifts = $old_team->shifts;
			foreach($old_shifts as $old_shift)
			{
				$new_shift = new Shift();
				$new_shift->attributes = $old_shift->attributes;
				$new_shift->team_id = $new_team->id;
				$new_shift->start_time += $time_diff;
				$new_shift->save();
			}
		}

		return $new_event->id;
	}
}
