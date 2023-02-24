<?php

namespace App\Http\Livewire;

use App\Http\Livewire\Traits\WithCachedProperty;
use App\Models\Appointment;
use App\Models\Schedule;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Livewire\Component;

class CreateBooking extends Component
{
    use WithCachedProperty;

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
            'employee' => ['required', Rule::exists('employees', 'id')]
        ];
    }

    public function bookApp()
    {
        $data = $this->validate();
        $startime = Carbon::createFromTimestamp($data['slot']);
        $createData = [
            'employee_id' => $data['employee'],
            'service_id' => $data['service'],
            'date' => $data['schedule'], //can use $startime->toDateString() to convert to date string
            'start_time' => $startime, //can use $startime->toTimeString() to convert to time string
            'end_time' => $startime->copy()->addMinute($this->serviceModel->duration),
            'client_email' => $data['email'],
            'client_name' => $data['name'],
        ];
        $appointment = Appointment::create($createData);

        return $this->redirect(URL::signedRoute('bookings.confirmation', ['token' => $appointment->token, 'uuid' => $appointment->uuid]));
    }

    public function updatedEmployee()
    {
        $this->reset('schedule', 'slot', 'availableTimeSlots', 'name', 'email');
        if ($this->employee) {
            $this->getSlots(now()->format('Y-m-d'));
        }
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
                $service = $this->serviceModel;
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
        collect($daysInterval)->map(function ($day) use (&$seriesOfOrderDay) {
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
