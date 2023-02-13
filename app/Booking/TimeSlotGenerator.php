<?php

namespace App\Booking;

use App\Filter\FilterInterface;
use App\Models\Schedule;
use App\Models\Service;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use function GuzzleHttp\Promise\all;

class TimeSlotGenerator
{
    protected array $filters = [];
    public array $slots = [];

    public function __construct(protected Schedule $schedule, protected Service $service, public int $interVal = 15)
    {
    }

    public function addFilter(FilterInterface $filter)
    {
        $this->filters[] = $filter;
    }

    public function generate()
    {
        $startDate = $this->schedule->date->setTimeFrom($this->schedule->start_time);
        $bookableEndtime = $this->schedule->end_time->subMinute($this->service->duration);
        $endDate = $this->schedule->date->setTimeFrom($bookableEndtime);
        $this->slots = CarbonInterval::minute($this->interVal)->toPeriod($startDate, $endDate);

        if (count($this->filters)) {
            $this->applyFilters();
        }

        return $this->slots;
//        return collect(CarbonInterval::minute($this->interVal)->toPeriod($startDate, $endDate))->filter(function (Carbon $slot) {
////            for testing
////           return $slot->gte(now()->subHour(2));
//            return $slot->gte(now());
//        })->all();
    }

    protected function applyFilters()
    {
        collect($this->filters)->each(function (FilterInterface $filter) {
            $filter->apply($this);
        });
    }
}
