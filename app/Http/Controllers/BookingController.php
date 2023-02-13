<?php

namespace App\Http\Controllers;

use App\Booking\TimeSlotGenerator;
use App\Filter\SlotPassedTodayFilter;
use App\Models\Schedule;
use App\Models\Service;
use Carbon\CarbonInterval;
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
        $slotGenerator = (new TimeSlotGenerator($schedule, $service));
        $slotGenerator->addFilter(new SlotPassedTodayFilter());
        $slots = $slotGenerator->generate();

        return view('bookings.create', compact('slots'));
    }
}
