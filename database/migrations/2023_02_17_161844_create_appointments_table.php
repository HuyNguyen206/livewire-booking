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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->string('token');
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('client_email');
            $table->string('client_name');
            $table->timestamps();
        });
        \App\Models\Appointment::create([
            'employee_id' => \App\Models\Employee::first()->id,
            'service_id' =>  \App\Models\Employee::first()->id,
            'date' => '2023-02-18',
            'start_time' => '10:00',
            'end_time' => '11:00',
            'client_name' => 'Sim',
            'client_email' => 'sim@gmail.com'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appointments');
    }
};
