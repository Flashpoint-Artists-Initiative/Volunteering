<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "team".
 *
 * @property integer $id
 * @property string $description
 * @property string $contact
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
            [['description'], 'string'],
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
            'contact' => 'Team Contact',
            'event_id' => 'Event',
			'name' => 'Team Name',
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

	public function getStatus()
	{
		$min_remaining = $extra_remaining = 0;
		$unlimited = false;
		foreach($this->shifts as $shift)
		{
			$filled = min($shift->filled, $shift->minSpots); 

			$min_remaining += $shift->minSpots - $filled;

			$extra_filled = $shift->filled - $filled;
			$extra_remaining += $shift->maxSpots - $shift->minSpots - $extra_filled;
		}

		$min_plural = $min_remaining == 1 ? "shift" : "shifts";
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
}
