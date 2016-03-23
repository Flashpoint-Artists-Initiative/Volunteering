<?php

namespace common\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use common\components\MDateTime;
use \DateInterval;
use \DatePeriod;

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
				->where(['team.event_id' => $this->id])
				->orderBy([
					'shift.start_time' => SORT_ASC, 
					'team.name' => SORT_ASC,
					'shift.title' => SORT_ASC,
					'user.username' => SORT_ASC,
				]),
			'pagination' => false,
			'sort' => false,
		]);
	}

	public function generateReport()
	{
		$output = [];
		$participants = Participant::find()->joinWith(['shift', 'shift.team'])
			->where(['team.event_id' => $this->id])
			->orderBy(['participant.timestamp' => SORT_ASC])
			->all();

		$date_containers = [];
		$date_totals = [];
		$user_totals = [];
		$multi_shift_totals = [];
		$team_names = ["Total Event" => 0];
		$max_needed = [
			"Total Event" => $this->maxTotalShifts,
		];
		
		//Get list of teams, plus event total, and their max total shifts
		foreach($this->teams as $team)
		{
			$team_names[$team->name] = 0;
			$max_needed[$team->name] = $team->maxTotalShifts;
		}

		//Loop through every participant signup, group by date (week)
		foreach($participants as $participant)
		{
			$dt = new MDateTime($participant->timestamp);
			$dt->subToStart('W');

			$timestamp = $dt->timestamp;
			$team_name = $participant->shift->team->name;

			if(!isset($date_containers[$timestamp]))
			{
				$date_containers[$timestamp] = $team_names;
				$date_totals[$timestamp] = 0;
				$user_totals[$timestamp] = 0;
			}

			if(!isset($date_containers[$timestamp][$team_name]))
			{
				$date_containers[$timestamp][$team_name] = 0;
			}

			$date_containers[$timestamp][$team_name]++;
			$date_containers[$timestamp]["Total Event"]++;
			$date_totals[$timestamp]++;
		}

		//Add missing dates to $date_containers
		$first_date = new MDateTime(array_keys($date_containers)[0]);
		$last_date = new MDateTime(array_pop(array_keys($date_containers)));
		$last_date->add(new DateInterval('P1W'));

		$period = new DatePeriod($first_date, new DateInterval('P1W'), $last_date);

		foreach($period as $dt)
		{
			if(!isset($date_containers[$dt->getTimestamp()]))
			{
				$date_containers[$dt->getTimestamp()] = $team_names;
				$date_totals[$dt->getTimestamp()] = 0;
				$user_totals[$dt->getTimestamp()] = 0;
			}
		
			//Calculate unique user totals from the start to each different time period
			$unique_ids = [];
			foreach($participants as $participant)
			{
				$end_of_week = new MDateTime($dt->getTimestamp());
				$end_of_week->addToEnd('W');
				if($participant->timestamp <= $end_of_week->timestamp);
				{
					$unique_ids[$participant->user_id] = 1;
				}
			}

			$user_totals[$dt->getTimestamp()] = count($unique_ids);
		}

		ksort($date_containers);
		ksort($date_totals);
		ksort($user_totals);


		//Add previous dates totals to the next date past that one
		//to show progression over time
		$current_vals = [];
		foreach($date_containers as $timestamp => $data)
		{
			foreach($data as $team_name => $total)
			{
				if(isset($current_vals[$team_name]))
				{
					$date_containers[$timestamp][$team_name]+= $current_vals[$team_name];
				}
			}

			$current_vals = $date_containers[$timestamp];
		}

		$headers = ['Team'];
		foreach($period as $date)
		{
			$headers[] = $date->format('n/j');
		}

		$raw_output = [];
		$percent_output = [];

		foreach($team_names as $name => $v)
		{
			$team_raw_output = [$name];
			$team_percent_output = [$name];
			foreach($period as $date)
			{
				$team_raw_output[] = $date_containers[$date->getTimestamp()][$name];
				$team_percent_output[] = sprintf("%.1f%%", ($date_containers[$date->getTimestamp()][$name] / $max_needed[$name]) * 100);
			}

			$raw_output[] = $team_raw_output;
			$percent_output[] = $team_percent_output;
		}

		//Total shifts per participant
		foreach($participants as $participant)
		{
			if(!isset($multi_shift_totals[$participant->user_id]))
			{
				$multi_shift_totals[$participant->user_id] = 0;
			}

			$multi_shift_totals[$participant->user_id]++;
		}

		$multi_shift_breakdown = [];
		foreach($multi_shift_totals as $p_total)
		{
			$label = $p_total == 1 ? "1 Shift" : "$p_total Shifts";

			if(!isset($multi_shift_breakdown[$p_total]))
			{
				$multi_shift_breakdown[$p_total] = [$label, 0];
			}

			$multi_shift_breakdown[$p_total][1]++;
		}

		ksort($multi_shift_breakdown);

		//Sort by team name
		$sort_func = function($a, $b)
		{
			if($a[0] === "Total Event")
			{
				return -1;
			}

			if($b[0] === "Total Event")
			{
				return 1;
			}
			
			return $a[0] > $b[0];
		};

		usort($raw_output, $sort_func);
		usort($percent_output, $sort_func);

		$output[] = $headers;
		$output[] = ["Percent Totals"];
		$output = array_merge($output, $percent_output);
		$output[] = [""];
		$output[] = ["Raw Totals"];
		$output = array_merge($output, $raw_output);
		$output[] = [""];
		$output[] = ["Weekly Total"];
		$output[] = [""] + $date_totals; 
		$output[] = [""];
		$output[] = ["Total Unique Users"];
		$output[] = [""] + $user_totals; 
		$output[] = [""];
		$output[] = ["Number of Shifts per Volunteer"];
		$output = array_merge($output, $multi_shift_breakdown);
		

		return $output;
	}
}
