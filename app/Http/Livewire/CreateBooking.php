<?php

namespace App\Http\Livewire;

use App\Models\Employee;
use App\Models\Schedule;
use App\Models\Service;
use Carbon\carbon;
use Carbon\CarbonInterval;
use Illuminate\Validation\Rule;
use Livewire\Component;

class CreateBooking extends Component
{
    public $service;
    public $employee;
    public $schedule;
    public $slot;
    public $availableTimeSlots = [];
    public $currentStartDate;

    public $email;
    public $name;


    public function mount()
    {
        $this->currentStartDate = now();
    }

    public function render()
    {
        $services = $this->listServices;
        $employees = $this->listEmployees;
        $seriesOfOrderDay = $this->generateDatetimeData();

        return view('livewire.create-booking', compact('services', 'employees', 'seriesOfOrderDay'))
            ->layout('layouts.guest');
    }

    protected function rules()
    {
        return [
            'name' => ['required', 'min:3'],
            'email' => ['required', 'email', 'min:6'],
            'schedule' => ['required'],
            'slot' => 'required',
            'service' => ['required', Rule::exists('services', 'id')],
            'employee'=> ['required', Rule::exists('employees', 'id')]
        ];
    }

    public function bookApp()
    {
        $data = $this->validate();
        dd($data);
        Appointment::create([
            'employee_id' => $this->employee,
            'service_id' => $this->service,
            ''
        ]);
    }

    public function getListServicesProperty()
    {
        return Service::all();
    }

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

    public function updatedEmployee()
    {
        $this->reset('schedule', 'slot', 'availableTimeSlots', 'name', 'email');
        if($this->employee) {
            $this->getSlots(now()->format('Y-m-d'));
        }
    }

    public function getFormatSelectedDateSlotProperty()
    {
        $carbon = Carbon::createFromTimestamp($this->slot);
        return $carbon->format('D dS M Y').' at '. $carbon->format('h:i A');
    }

    public function updatedService()
    {
        $this->reset('schedule', 'slot', 'availableTimeSlots', 'employee', 'name', 'email');
    }


    public function getSlots($scheduleValue = null)
    {
        $this->schedule = $scheduleValue ?? $this->schedule;
        if ($this->service && $this->employee && $this->schedule) {
            $schedule = Schedule::whereDate('date', $this->schedule)->latest()->first();
            if ($schedule) {
                $service = Service::find($this->service);
                $this->availableTimeSlots = $this->employeeModel->availableTimeSlots($schedule, $service);
            } else {
                $this->availableTimeSlots = [];
            }

        } else {
            $this->availableTimeSlots = [];
        }
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
