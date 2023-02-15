<?php

namespace App\Filter;

use App\Booking\TimeSlotGenerator;
use App\Models\ScheduleUnavailability;

class UnavailabilityFilter implements FilterInterface
{
    public function __construct(protected ScheduleUnavailability $unavailabilities)
    {
    }

    public function apply(TimeSlotGenerator $generator, array $params = [])
    {
        $generator->slots = collect($generator->slots)->filter(function ($slot) use($generator){
           return $slot->gte($this->unavailabilities->end_time) || $slot->lte($this->unavailabilities->start_time->subMinute($generator->service->duration));
        })->all();
    }
}
