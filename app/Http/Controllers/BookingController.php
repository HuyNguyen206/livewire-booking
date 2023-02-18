<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Schedule;
use App\Models\Service;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $schedule = Schedule::first();
        $service = Service::first();
        $employee = Employee::query()->first();
//        $slotGenerator = (new TimeSlotGenerator($schedule, $service));
//        $slotGenerator
//            ->addFilter(new SlotPassedTodayFilter())
//            ->addFilter(new UnavailabilityFilter($schedule->scheduleUnavailabilities))
//            ->addFilter(new AppointmentFilter($schedule->employee->appointments()->whereDate('date', $schedule->date)->get()))
//        ;
//
//        $slots = $slotGenerator->generate();
//        $employee = Employee::first();
        $slots = $employee->availableTimeSlots($schedule, $service);
        foreach ($slots as $slot){
            dump($slot);
        }
        die();
        return view('bookings.create', compact('slots'));

    }
}
