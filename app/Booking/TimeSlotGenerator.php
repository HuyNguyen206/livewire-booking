<?php

namespace App\Booking;

use App\Filter\FilterInterface;
use App\Models\Schedule;
use App\Models\Service;
use Carbon\CarbonInterval;

class TimeSlotGenerator
{
    protected array $filters = [];
    public iterable $slots = [];

    public iterable $rawSlots = [];

    public function __construct(protected Schedule $schedule, public Service $service, public int $interVal = 15)
    {
    }

    public function when(bool $condition, FilterInterface $filter)
    {
        if (!$condition) {
            return $this;
        }
        $this->addFilter($filter);

        return $this;
    }

    public function addFilter(FilterInterface $filter)
    {
        $this->filters[] = $filter;
        return $this;
    }

    public function addFilters(array $filters)
    {
        $this->filters = $filters;
        return $this;
    }

    public function generate()
    {
        $startDate = $this->schedule->date->setTimeFrom($this->schedule->start_time);
        $bookableEndtime = $this->schedule->end_time->subMinute($this->service->duration);
        $endDate = $this->schedule->date->setTimeFrom($bookableEndtime);
        $this->slots = CarbonInterval::minute($this->interVal)->toPeriod($startDate, $endDate);
        $this->rawSlots = $this->slots;
        if (count($this->filters)) {
            $this->applyFilters();
        }

        return $this->slots;
    }

    protected function applyFilters()
    {
        collect($this->filters)->each(function (FilterInterface $filter) {
            $filter->apply($this);
        });

        if (collect($this->slots)->isEmpty()) {
            return [];
        }

        $unavailableSlots = collect($this->rawSlots)->diff($this->slots)->map(function ($slot){
            $dataSlot['slot'] =  $slot;
            $dataSlot['isAvailable'] = false;
            return $dataSlot;
        });

        $availableSlots = collect($this->slots)->map(function ($slot){
            $dataSlot['slot'] =  $slot;
            $dataSlot['isAvailable'] = true;
            return $dataSlot;
        });

        $this->slots = collect([...$unavailableSlots, ...$availableSlots])->sortBy(function ($slot){
            return $slot['slot']->timestamp;
        })->values()->map(function ($slot){
            $slot['slot'] = [
                'label' => $slot['slot']->format('h:i A'),
                'timestamp' => $slot['slot']->timestamp,
            ];
            return $slot;
        })->all();
    }
}
