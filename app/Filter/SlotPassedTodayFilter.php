<?php

namespace App\Filter;

use App\Booking\TimeSlotGenerator;
use Carbon\Carbon;
use Carbon\CarbonInterval;

class SlotPassedTodayFilter implements FilterInterface
{
    public function apply(TimeSlotGenerator $generator, array $params = [])
    {
        $result = collect($generator->slots)
            ->filter(function (Carbon $slot){
//            for testing
//           return $slot->gte(now()->subHour(2));
            return $slot->gte(now());
        })->all();

        $generator->slots = $result;
    }
}
