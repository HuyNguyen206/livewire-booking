<?php

namespace App\Http\Livewire;

use App\Models\Employee;
use App\Models\Schedule;
use App\Models\Service;
use Carbon\CarbonInterval;
use Livewire\Component;

class CreateBooking extends Component
{
    public $service;
    public $employee;
    public $schedule;
    public $slot;
    public $availableTimeSlots = [];
    public $currentStartDate;

    public $test;

    public function mount()
    {
        $this->currentStartDate = now();
    }

    public function render()
    {
        $this->generateDatetimeData();
        $services = $this->listServices;
        $employees = $this->listEmployees;
        $seriesOfOrderDay = $this->generateDatetimeData();

        return view('livewire.create-booking', compact('services', 'employees', 'seriesOfOrderDay'))
            ->layout('layouts.guest');
    }

    public function getListServicesProperty()
    {
        return Service::all();
    }

    public function getListEmployeesProperty()
    {
        return optional(Service::find($this->service))->employees ?? [];
    }

    public function getCanEnableDatePickerProperty()
    {
        return $this->service && $this->employee;
    }

    public function updatedEmployee()
    {
        if($this->employee) {
            $this->getSlots(now()->format('Y-m-d'));
        }
    }


    public function getSlots($scheduleValue = null)
    {
        $this->schedule = $scheduleValue ?? $this->schedule;
        if ($this->service && $this->employee && $this->schedule) {
            $schedule = Schedule::whereDate('date', $this->schedule)->latest()->first();

            if ($schedule) {
                $service = Service::find($this->service);
                $this->availableTimeSlots = Employee::find($this->employee)->availableTimeSlots($schedule, $service);
            } else {
                $this->availableTimeSlots = [];
            }

        } else {
            $this->availableTimeSlots = [];
        }
    }

    public function bookApp()
    {
//        Appointment::create([
//            'employee_id' => $this->employee,
//            'service_id' => $this->service,
//            ''
//        ]);
    }

    public function getPreviousWeek()
    {
      $this->currentStartDate->subWeek()->subDay();
    }

    public function getCanSelectPreviousWeekProperty()
    {
       return $this->currentStartDate->copy()->endOfDay()->gt(today()->endOfDay());
    }

    public function getNextWeek()
    {
        $this->currentStartDate->addWeek()->addDay();
    }

    private function generateDatetimeData()
    {
        $startDate = $this->currentStartDate;
        $currentMonthYear = $startDate->format('M Y');
        $seriesOfOrderDay['label'] = $currentMonthYear;

        $daysInterval = CarbonInterval::days()->toPeriod($startDate, $startDate->copy()->addWeek());
        $seriesOfOrderDay['days'] = [];
        collect($daysInterval)->map(function ($day) use(&$seriesOfOrderDay){
            $seriesOfOrderDay['days'][] = [
                'text' => $day->format('D'),
                'digit' => [
                    'day' => $day->format('d'),
                    'fullDate' => $day->format('Y-m-d')
                ]
            ];
        });

        return $seriesOfOrderDay;
    }
}
