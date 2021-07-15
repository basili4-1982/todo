<?php

namespace App\Mix;

use DateInterval;
use DateTime;
use Exception;

trait TimeTrait
{
    /**
     * Неделя 5 дней
     */
    private int $week = 2400;

    /**
     * День 8 часов
     */
    private int $day = 480;

    /**
     * Час 60 минут
     */
    private int $hour = 60;

    /**
     * @throws Exception
     */
    public function durationToDateTime(DateTime $dateTime, string $duration): DateTime
    {
        return $dateTime->add(new DateInterval("PT" . $this->parseDuration($duration) . "M"));
    }

    /**
     * @param string $duration
     * Возвращается количество минут
     *
     * @return int
     */
    private function parseDuration(string $duration): int
    {
        if (strlen($duration) < 2) {
            return 0;
        }

        $period = $duration[strlen($duration) - 1];

        $k = (int) substr($duration, 0, -1);

        switch ($period) {
            // неделя
            case 'w':
            {
                return $k * $this->week;
            }
            // день
            case 'd':
            {
                return $k * $this->day;
            }

            // час
            case 'h':
            {
                return $k * $this->hour;
            }
            //минуты
            default :
            {
                return $k;
            }
        }
    }
}
