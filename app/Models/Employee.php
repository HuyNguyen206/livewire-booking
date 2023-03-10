<?php

namespace App\Models;

use App\Booking\TimeSlotGenerator;
use App\Filter\AppointmentFilter;
use App\Filter\SlotPassedTodayFilter;
use App\Filter\UnavailabilityFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    public function services()
    {
        return $this->belongsToMany(Service::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function availableTimeSlots(Schedule $schedule, Service $service)
    {
        $slotGenerator = (new TimeSlotGenerator($schedule, $service));
        $appointments = $this->appointments()->whereDate('date', $schedule->date)->get();
        $slotGenerator
            ->addFilter(new SlotPassedTodayFilter())
            ->when($schedule->scheduleUnavailabilities->isNotEmpty(), new UnavailabilityFilter($schedule->scheduleUnavailabilities))
            ->when($appointments->isNotEmpty(), new AppointmentFilter($appointments))
        ;

        return $slotGenerator->generate();
    }
}
