<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\Team;
use common\models\Event;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;

/**
 * LoginForm is the model behind the login form.
 */
class EventCopyForm extends Model
{
	const DATE_FORMAT = "M j Y";

	public $event_id;
	public $start_time;
	public $timestamp;

	protected $_event = false;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['event_id', 'start_time'], 'required'],
            ['event_id', 'integer'],
			['start_time', 'date', 'timestampAttribute' => 'timestamp', 'format' => 'php:' . self::DATE_FORMAT],
        ];
    }

	public function attributeLabels()
	{
		return [
			'event_id' => 'Original Event',
			'start_time' => 'Start Date',
		];
	}

	public function getEventList()
	{
		$events = Event::find()->all();
		return ArrayHelper::map($events, 'id', 'dropdownName');
	}

	public function copy()
	{
		if(!$this->validate())
		{
			return false;
		}

		$event = $this->getEvent();

		return $event->makeCopy($this->timestamp);
	}

	public function getEvent()
	{
        if ($this->_event === false) {
            $this->_event = Event::findOne($this->event_id);
        }

        if ($this->_event === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        return $this->_event;
	}
}

