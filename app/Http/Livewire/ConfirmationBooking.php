<?php

namespace App\Http\Livewire;

use App\Models\Appointment;
use Carbon\Carbon;
use Livewire\Component;

class ConfirmationBooking extends Component
{
    public $employee;
    public $service;
    public $name;
    public $date;
    public $appointment;
    public $message;
    public $isCancel = false;

    public function mount($uuid, $token)
    {
        $appointment = Appointment::whereUuid($uuid)->whereToken($token)->first();
        if(!$appointment) {
            $this->isCancel = true;
            return;
        }
        $this->appointment = clone $appointment;
        $this->employee = $appointment->employee;
        $this->service = $appointment->service;
        $this->name = $appointment->client_name;
        $this->date = $appointment->date->format('D dS M Y').' at '.$appointment->start_time->format('h:i A');
    }

    public function render()
    {
        return view('livewire.confirmation-booking')->layout('layouts.guest');
    }

    public function cancel()
    {
        $this->appointment->update([
            'canceled_at' => Carbon::tomorrow()
        ]);
        $this->isCancel = true;
    }

    public function book()
    {
        return $this->redirect(route('bookings.create'));
    }
}
