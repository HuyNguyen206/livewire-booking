<?php

namespace App\Filter;

use App\Booking\TimeSlotGenerator;
use Illuminate\Support\Collection;

class UnavailabilityFilter implements FilterInterface
{
    public function __construct(protected Collection $unavailabilities)
    {
    }

    public function apply(TimeSlotGenerator $generator, array $params = [])
    {
        $firstOne = $this->unavailabilities->first();
        $lastOne = $this->unavailabilities->last();
        $firstStartTime = $firstOne->start_time;
        $lastEndTime = $lastOne->end_time;

        $finalResults = collect($generator->slots)->filter(function ($slot) use ($lastEndTime, $firstStartTime, $generator) {
            return $slot->gte($lastEndTime) || $slot->lte($firstStartTime->copy()->subMinute($generator->service->duration));
        })->all();
        $length = count($this->unavailabilities);
        for ($i = 0; $i < $length; $i++) {
            if ($i !== $length - 1){
                $val = $this->unavailabilities->get($i);
                $nextVal = $this->unavailabilities->get($i + 1);
                $intervalsBetweenTwoUnvalidPoints[] = [$val->end_time, $nextVal->start_time];
            }
        }
        collect($intervalsBetweenTwoUnvalidPoints)->each(function ($intervalPair) use (&$finalResults, $generator) {
            [...$finalResults, ...collect($generator->slots)->filter(function ($slot) use ($intervalPair, $generator) {
                return $slot->between($intervalPair[0], $intervalPair[1]->subMinute($generator->service->duration));
            })->all()];
        });

        $generator->slots = collect($finalResults)->sortBy(function ($slot){
            return $slot->timestamp;
        });
    }
}
