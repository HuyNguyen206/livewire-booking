<?php

namespace App\Http\Livewire;

use App\Models\Employee;
use App\Models\Schedule;
use App\Models\Service;
use Livewire\Component;

class CreateBooking extends Component
{
    public $service;
    public $employee;
    public $schedule;
    public $slot;
    public $availableTimeSlots = [];

    public $test;

    protected $listeners = ['getSlots'];

    public function render()
    {
        $this->generateDatetimeData();
        $services = $this->listServices;
        $employees = $this->listEmployees;
        $seriesOfOrderDay = $this->generateDatetimeData();
//        dd($seriesOfOrderDay);

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

    public function getSlots()
    {
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

    private function generateDatetimeData()
    {
        $now = now();
        $currentMonthYear = $now->format('M Y');
        $seriesOfOrderDay['label'] = $currentMonthYear;
        $seriesOfOrderDay['text'] = [$now->format('D')];
        $seriesOfOrderDay['digit'][] = [
            'day' => $now->format('d'),
            'fullDateTime' => $now->format('Y-m-d')
        ];
        for ($i = 1; $i <= 7; $i++) {
            $day = $now->addDay();
            $seriesOfOrderDay['text'][] = $day->format('D');
            $seriesOfOrderDay['digit'][] = [
                'day' => $day->format('d'),
                'fullDateTime' => $day->format('Y-m-d')
            ];
        }
        return $seriesOfOrderDay;
    }
}
