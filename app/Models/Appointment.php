<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Appointment extends Model
{
    use HasFactory;
    protected static $unguarded = true;

    protected $casts = [
        'date' => 'datetime',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'canceled_at' => 'datetime'
    ];

    protected static function booted()
    {
       static::creating(function (Appointment $appointment){
           $appointment->uuid = Str::uuid();
           $appointment->token = Str::random();
       });

       static::addGlobalScope('non-cancel', function (Builder $builder) {
          $builder->whereNull('canceled_at');
       });
    }

    public function service()
    {
        return  $this->belongsTo(Service::class);
    }

    public function employee()
    {
        return  $this->belongsTo(Employee::class);
    }
}
