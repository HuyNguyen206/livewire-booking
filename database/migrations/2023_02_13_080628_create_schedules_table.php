<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
        });
        \Illuminate\Support\Facades\DB::table('schedules')->insert([
            [
                'employee_id' => \App\Models\Employee::query()->inRandomOrder()->value('id'),
                'date' => \Carbon\Carbon::now()->subDay(),
                'start_time' => '9:00',
                'end_time' =>  '17:00'
            ],
            [
                'employee_id' => \App\Models\Employee::query()->inRandomOrder()->value('id'),
                'date' => \Carbon\Carbon::now()->subDay(2),
                'start_time' => '9:00',
                'end_time' =>  '17:00'
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedules');
    }
};
