<?php

namespace App\Utils\Helpers;

use Illuminate\Support\Carbon;

class DatetimeHelpers
{

    /**
     * @param $timestamp
     * @return string
     */
    public static function convertUnixTimestampToCarbonInstance($timestamp): string
    {
        $dateTime = Carbon::createFromTimestamp($timestamp);
        return $dateTime->format('Y-m-d H:i:s');
    }
}
