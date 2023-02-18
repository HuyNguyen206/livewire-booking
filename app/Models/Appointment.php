<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Appointment extends Model
{
    use HasFactory;
//    protected static $unguarded = true;
protected $fillable = ['client_name'];
    protected $casts = [
        'date' => 'datetime',
        'start_time' => 'datetime',
        'end_time' => 'datetime'
    ];

    protected static function booted()
    {
       static::creating(function (Appointment $appointment){
           $appointment->uuid = Str::uuid();
           $appointment->token = Str::random();
       });
    }

    public function service()
    {
        $this->belongsTo(Service::class);
    }

    public function employee()
    {
        $this->belongsTo(Employee::class);
    }
}
