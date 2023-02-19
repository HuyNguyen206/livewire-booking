<?php

namespace App\Filter;

use App\Booking\TimeSlotGenerator;
use Illuminate\Support\Collection;

class BaseExcludeSlotFilter implements FilterInterface
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

        $availableSlots = collect($generator->slots)->filter(function ($slot) use ($lastEndTime, $firstStartTime, $generator) {
            return $slot->gte($lastEndTime) || $slot->lte($firstStartTime->copy()->subMinute($generator->service->duration));
        })->all();
        $intervalsBetweenTwoUnvalidPoints = [];
        $length = count($this->unavailabilities);
        for ($i = 0; $i < $length; $i++) {
            if ($i !== $length - 1){
                $val = $this->unavailabilities->get($i);
                $nextVal = $this->unavailabilities->get($i + 1);
                $intervalsBetweenTwoUnvalidPoints[] = [$val->end_time, $nextVal->start_time];
            }
        }
        collect($intervalsBetweenTwoUnvalidPoints)->each(function ($intervalPair) use (&$availableSlots, $generator) {
            $availableSlots = [...$availableSlots, ...collect($generator->slots)->filter(function ($slot) use ($intervalPair, $generator) {
                return $slot->betweenIncluded($intervalPair[0], $intervalPair[1]->copy()->subMinute($generator->service->duration));
            })->all()];
        });
        $unavailableSlots = collect($generator->rawSlots)->diff($availableSlots)->map(function ($slot){
            $dataSlot['slot'] =  $slot;
            $dataSlot['isAvailable'] = false;
            return $dataSlot;
        });

        $availableSlots = collect($availableSlots)->map(function ($slot){
            $dataSlot['slot'] =  $slot;
            $dataSlot['isAvailable'] = true;
            return $dataSlot;
        });

        $generator->slots = [...$unavailableSlots, ...$availableSlots];
    }
}
