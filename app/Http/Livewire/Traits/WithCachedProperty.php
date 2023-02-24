<?php

namespace App\Http\Livewire\Traits;

use App\Models\Employee;
use App\Models\Service;
use Carbon\Carbon;

trait WithCachedProperty
{
    public function getListEmployeesProperty()
    {
        return optional($this->serviceModel)->employees ?? [];
    }

    public function getServiceModelProperty()
    {
        return Service::find($this->service);
    }

    public function getEmployeeModelProperty()
    {
        return Employee::find($this->employee);
    }

    public function getCanEnableDatePickerProperty()
    {
        return $this->service && $this->employee;
    }

    public function getListServicesProperty()
    {
        return Service::all();
    }

    public function getFormatSelectedDateSlotProperty()
    {
        $carbon = Carbon::createFromTimestamp($this->slot);
        return $carbon->format('D dS M Y') . ' at ' . $carbon->format('h:i A');
    }

    public function getCanSelectPreviousWeekProperty()
    {
        Carbon::now();
        return $this->currentStartDate->copy()->endOfDay()->gt(today()->endOfDay());
    }

}
