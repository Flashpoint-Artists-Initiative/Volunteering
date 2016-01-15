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

	public function getTeams()
	{
		return $this->hasMany(Team::className(), ['event_id' => 'id']);
	}
}
