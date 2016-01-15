<?php
namespace common\components;

use \DateTime;
use \DateTimeZone;
use \DateInterval;

class MDateTime extends DateTime
{
    public function __construct($time = 'now', DateTimeZone $timezone = NULL)
    {
        if(is_numeric($time))
        {
            if(isset($timezone))
            {
                parent::__construct('now', $timezone);
            }
            else
            {
                parent::__construct('now');
            }

            $this->setTimestamp($time);
        }
        elseif(isset($timezone))
        {
            parent::__construct($time, $timezone);
        }
        else
        {
            parent::__construct($time);
        }
    }

    public function __get($var)
    {
        if($var == 'timestamp')
        {
            return $this->getTimestamp();
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $var.
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);

        return null;
    }

    public function subToStart($interval)
    {
        $months = $days = $hours = $minutes = $seconds = false;

        switch($interval)
        {
            case 'Y':
                $months = $this->format('m');
                $months--;  //Subtract one so we stay on the same year.  Ex: 2014-05 minus 5 months = 2013-12.

            case 'M':
            case 'W':
                if($interval == 'W')
                    $days = $this->format('w');
                else
                {
                    $days = $this->format('j');
                    $days--;    //Subtract one for same reason as above, for months
                }

            case 'D':
                $hours   = $this->format('H');

            case 'H':
                $minutes = $this->format('i');

            case 'I':
                //M is taken, use I since that's what date() uses.
                $seconds = $this->format('s');
                break;

            default:
                throw new BadMethodCallException("subToStart must be passed either Y,M,W,D,H, or I.");
        }

        $interval_string = "P";

        if($days)
            $interval_string .= "{$days}D";

        if($hours || $minutes || $seconds)
        {
            $interval_string .= "T";

            if($hours)
                $interval_string .= "{$hours}H";

            if($minutes)
                $interval_string .= "{$minutes}M";

            if($seconds)
                $interval_string .= "{$seconds}S";
        }

        //Get the top of the current hour
        $this->sub(new DateInterval($interval_string));

        //Do months after everything else to preserve the first of the month.
        if($months)
            $this->sub(new DateInterval("P{$months}M"));

        return $this;
    }

    public function addToStart($interval)
    {
        $this->subToStart($interval);

        if($interval == 'H' || $interval == 'I')
            $str = "PT1$interval";
        else
            $str = "P1$interval";

        $this->add(new DateInterval($str));

        return $this;
    }

    //No idea how this would be useful, but it's there for completion's sake.
    public function subToEnd($interval)
    {
        $this->subToStart($interval)->sub(new DateInterval('PT1S'));

        return $this;
    }

    public function addToEnd($interval)
    {
        $this->addToStart($interval)->sub(new DateInterval('PT1S'));

        return $this;
    }

    public function toDateTime()
    {
        $str = $this->format('Y-m-d H:i:s');
        return new DateTime($str);
    }
}
