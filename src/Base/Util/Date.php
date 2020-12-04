<?php


namespace Base\Util;


use Base\BaseApp;
use Base\Phrase;
use DateTimeZone;

/**
 * Class Date
 * @package Base\Util
 */
class Date extends \DateTime
{
    /**
     * @var string[]
     */
    protected $dowTranslation = [
        0 => 'sunday',
        1 => 'monday',
        2 => 'tuesday',
        3 => 'wednesday',
        4 => 'thursday',
        5 => 'friday',
        6 => 'saturday'
    ];
    /**
     * @var array
     */
    protected $options = [
        'date_format' => 'M j, Y',
        'time_format' => 'g:i A',
    ];
    /**
     * @var null
     */
    protected $dayStartTimestamps = null;

    /**
     * Date constructor.
     * @param $timeZone
     * @param string $time
     * @param DateTimeZone|null $timezone
     * @throws \Exception
     */
    public function __construct($timeZone, $time = 'now', DateTimeZone $timezone = null)
    {
        parent::__construct($time, $timezone);
        if(!($timeZone instanceof \DateTimeZone))
        {
            $this->setTimezone(new \DateTimeZone($timeZone)) ;
        }
        else
        {
            $this->setTimezone($timeZone) ;
        }

    }

    /**
     * @param $timestamp
     * @param $date
     * @param $time
     * @param bool $getFullDate
     * @return mixed|string
     */
    public function getRelativeDateTimeOutput($timestamp, $date, $time, $getFullDate = false)
    {
        $timeRef = $this->getDayStartTimestamps();
        $interval = $timeRef['now'] - $timestamp;

        if ($interval < -2)
        {
            $futureInterval = $timestamp - $timeRef['now'];

            if ($futureInterval < 60)
            {

                return $this->getPhraseCacheRaw('in_a_moment');
            }
            else if ($futureInterval < 120)
            {
                return $this->getPhraseCacheRaw('in_a_moment');
            }
            else if ($futureInterval < 3600)
            {
                return strtr($this->getPhraseCacheRaw('in_x_minutes'), [
                    '{minutes}' => floor($futureInterval / 60)
                ]);
            }
            else if ($timestamp < $timeRef['tomorrow'])
            {
                return strtr($this->getPhraseCacheRaw('later_today_at_x'), [
                    '{time}' => $time
                ]);
            }
            else if ($timestamp < $this->setTimestamp($timeRef['tomorrow'])->modify('+1 day')->format('U')) // day after tomorrow
            {
                return strtr($this->getPhraseCacheRaw('tomorrow_at_x'), [
                    '{time}' => $time
                ]);
            }
            else if ($futureInterval < ($this->setTimestamp($this->getTimeByTimeZoneFormatUnix())->modify('+1 week')->format('U') - $this->getTimeByTimeZoneFormatUnix()))
            {
                return $this->getDateTimeOutput($date, $time);
            }
            else if ($getFullDate)
            {
                return $this->getDateTimeOutput($date, $time);
            }
            else
            {
                return $date;
            }
        }
        else if ($interval <= 60)
        {
            return $this->getPhraseCacheRaw('a_moment_ago');
        }
        else if ($interval <= 120)
        {
            return $this->getPhraseCacheRaw('one_minute_ago');
        }
        else if ($interval < 3600)
        {
            return $this->getPhraseCacheRaw('x_minutes_ago', ['minutes' => floor($interval / 60)]);
        }
        else if ($timestamp >= $timeRef['today'])
        {
            return strtr($this->getPhraseCacheRaw('today_at_x'), [
                '{time}' => $time
            ]);
        }
        else if ($timestamp >= $timeRef['yesterday'])
        {

            return strtr($this->getPhraseCacheRaw('yesterday_at_x'), [
                '{time}' => $time
            ]);
        }
        else if ($timestamp >= $timeRef['week'])
        {
            $dow = $this->setTimestamp($timestamp)->format('w');
            $day = $this->getPhraseCacheRaw('day_' . $this->dowTranslation[$dow]);

            return strtr($this->getPhraseCacheRaw('day_x_at_time_y'), [
                '{day}' => $day,
                '{time}' => $time
            ]);
        }
        else if ($getFullDate)
        {
            return $this->getDateTimeOutput($date, $time);
        }
        else
        {
            return $date;
        }
    }

    /**
     * @param $timestamp
     * @param null $format
     * @return string
     */
    public function date($timestamp, $format = null)
    {
        if ($timestamp instanceof \DateTime)
        {
            $date = $timestamp;
        }
        else
        {
            $date = $this->setTimestamp($timestamp);
        }

        switch ($format)
        {
            case 'year':
                $dateFormat = 'Y';
                break;

            case 'monthDay':
                $dateFormat = 'F j';
                break;

            case 'picker':
                $dateFormat = 'Y-m-d';
                break;

            case 'absolute':
            case '':
                $dateFormat = $this->options['date_format'];
                break;

            default:
                $dateFormat = $format;
        }

        return $this->formatDateTime($date, $dateFormat);
    }

    /**
     * @return array|null
     */
    public function getDayStartTimestamps()
    {
        if (!$this->dayStartTimestamps)
        {

            $this->setTimestamp($this->getTimeByTimeZoneFormatUnix());
            $this->setTime(0, 0, 0);

            list($todayStamp, $todayDow) = explode('|', $this->format('U|w'));

            $this->modify('+1 day');
            $tomorrowStamp = $this->format('U');

            $this->modify('-2 days');
            $yesterdayStamp = $this->format('U');

            $this->modify('-5 days');
            $weekStamp = $this->format('U');

            $this->dayStartTimestamps = [
                'tomorrow'  => $tomorrowStamp,
                'now'       => $this->getTimeByTimeZoneFormatUnix(),
                'today'     => $todayStamp,
                'todayDow'  => $todayDow,
                'yesterday' => $yesterdayStamp,
                'week'      => $weekStamp
            ];
        }

        return $this->dayStartTimestamps;
    }

    /**
     * @param $date
     * @param $time
     * @return string
     */
    public function getDateTimeOutput($date, $time)
    {
        return strtr($this->getPhraseCacheRaw('date_x_at_time_y'), [
            '{date}' => $date,
            '{time}' => $time
        ]);
    }

    /**
     * @param $timestamp
     * @return false|string[]
     */
    public function getDateTimeParts($timestamp)
    {
        $date = $this->setTimestamp($timestamp);

        $dateTimeFormat = $this->options['date_format'] . '|' . $this->options['time_format'];
        return explode('|', $this->formatDateTime($date, $dateTimeFormat));
    }

    /**
     * @param \DateTime $date
     * @param $format
     * @return string
     */
    protected function formatDateTime(\DateTime $date, $format)
    {

        $dateParts = explode('|', $date->format('j|w|W|n|Y|G|i|s|S'));
        list($dayOfMonth, $dayOfWeek, $weekOfYear, $month, $year, $hour, $minute, $second, $ordinalSuffix) = $dateParts;

        $output = '';

        $formatters = str_split($format);
        $formatterCount = count($formatters);
        for ($i = 0; $i < $formatterCount; $i++)
        {
            $identifier = $formatters[$i];

            switch ($identifier)
            {
                case 'd': $output .= sprintf('%02d', $dayOfMonth); break;
                case 'j': $output .= $dayOfMonth; break;

                case 'D': $output .= $this->getPhraseCacheRaw('day_' . $this->dowTranslation[$dayOfWeek] . '_short'); break;
                case 'l': $output .= $this->getPhraseCacheRaw('day_' . $this->dowTranslation[$dayOfWeek]); break;

                case 'w': $output .= $dayOfWeek; break;
                case 'W': $output .= $weekOfYear; break;

                case 'm': $output .= sprintf('%02d', $month); break;
                case 'n': $output .= $month; break;
                case 'F': $output .= $this->getPhraseCacheRaw('month_' . $month); break;
                case 'M': $output .= $this->getPhraseCacheRaw('month_' . $month . '_short'); break;

                case 'Y': $output .= $year; break;
                case 'y': $output .= substr($year, 2); break;

                case 'a': $output .= $this->getPhraseCacheRaw(($hour >= 12 ? 'time_pm_lower' : 'time_am_lower')); break;
                case 'A': $output .= $this->getPhraseCacheRaw(($hour >= 12 ? 'time_pm_upper' : 'time_am_upper')); break;

                case 'H': $output .= sprintf('%02d', $hour); break;
                case 'h': $output .= sprintf('%02d', $hour % 12 ? $hour % 12 : 12); break;
                case 'G': $output .= $hour; break;
                case 'g': $output .= ($hour % 12 ? $hour % 12 : 12); break;

                case 'i': $output .= $minute; break;

                case 's': $output .= $second; break;

                case 'S': $output .= $ordinalSuffix; break;

                case '\\':
                    $i++;
                    if ($i < $formatterCount)
                    {
                        $output .= $formatters[$i];
                    }
                    break;
                case 'N':
                case 'z':
                case 't':
                case 'L':
                case 'o':
                case 'B':
                case 'u':
                case 'v':
                case 'e':
                case 'I':
                case 'O':
                case 'P':
                case 'T':
                case 'Z':
                case 'c':
                case 'r':
                case 'U':
                    $output .= $date->format($identifier);
                    break;
                default: $output .= $identifier;
            }
        }
        return $output;
    }

    /**
     * @param $name
     * @param array $values
     * @return mixed
     */
    protected function getPhraseCacheRaw($name, array $values = [])
    {
        return BaseApp::phrase($name, $values);
    }

    /**
     * @param $dateTime
     * @return string
     */
    public function renderDateBrut($dateTime)
    {
        $times = intval($dateTime);
        $this->setTimestamp($times);

        list($date, $time) = $this->getDateTimeParts($times);

        $relative = $this->getRelativeDateTimeOutput($times, $date, $time);
        return htmlspecialchars($relative);
    }
    /**
     * @param $dateTime
     * @return string
     */
    public function renderHtmlDate($dateTime)
    {
        $times = intval($dateTime);
        $this->setTimestamp($times);

        list($date, $time) = $this->getDateTimeParts($times);

        $relative = $this->getRelativeDateTimeOutput($times, $date, $time);


        return "<time dir=\"auto\" datetime=\"" . $this->format(\DateTime::ISO8601)
                ."\" data-time=\"$times\">"
                . htmlspecialchars($relative) .
                "</time>";
    }

    /**
     * @return string
     */
    public function getTimeByTimeZoneFormatUnix()
    {
        $date = new $this($this->getTimeZone());
        return $date->format('U');
    }
}