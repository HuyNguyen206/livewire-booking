<?php

namespace App\Filter;

use App\Booking\TimeSlotGenerator;

interface FilterInterface
{
    public function apply(TimeSlotGenerator $generator, array $params = []);
}
