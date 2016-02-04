<?php

namespace common\models;

use Yii;
use common\components\MDateTime;

/**
 * This is the model class for table "participant".
 *
 * @property integer $shift_id
 * @property integer $user_id
 * @property string $name
 * @property integer $size
 * @property boolean $completed
 */
class Participant extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'participant';
    }

	public static function primaryKey()
	{
		return ['shift_id', 'user_id'];
	}

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['shift_id'], 'required'],
            [['shift_id', 'user_id', 'size'], 'integer'],
            [['name'], 'string', 'max' => 255],
			[['completed'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'shift_id' => 'Shift ID',
            'user_id' => 'User ID',
            'name' => 'Name',
            'size' => 'Size',
			'completed' => 'Shift Completed',
        ];
    }

	/**
	 * Relations
	 */
	public function getUser()
	{
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}

	public function getShift()
	{
		return $this->hasOne(Shift::className(), ['id' => 'shift_id']);
	}
	
	public function beforeSave()
	{
		if(!$this->shift->team->event->active)
		{
			Yii::$app->session->addFlash("error", "Cannot modify shifts that are part of an inactive Event");
			return false;
		}

		return true;
	}

	public function beforeDelete()
	{
		if(!$this->shift->team->event->active)
		{
			return false;
		}

		return true;
	}

	public static function findUserEventDataByDay($event_id, $user_id)
	{
		$participants = self::find()
			->where(['user_id' => $user_id])
			->andWhere(['team.event_id' => $event_id])
			->joinWith(['shift', 'shift.team']) 
			->all();

		$data = [];

		foreach($participants as $participant)
		{
			$shift = $participant->shift;
			$team = $shift->team;

			$start_day = new MDateTime($shift->start_time);
			$start_day->subToStart('D');

			if(!isset($data[$start_day->timestamp]))
			{
				$data[$start_day->timestamp] = [];
			}

			$data[$start_day->timestamp][] = $participant;
		}

		foreach($data as $timestamp => $day_data)
		{
			uasort($data[$timestamp], function($a, $b){
				return $a->shift->start_time > $b->shift->start_time;
			});
		}

		return $data;
	}
}
