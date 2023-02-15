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
        Schema::create('schedule_unavailabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained()->cascadeOnDelete();
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
        });

        \Illuminate\Support\Facades\DB::table('schedule_unavailabilities')->insert([
            [
                'schedule_id' => \App\Models\Schedule::first()->id,
                'start_time' => '12:00',
                'end_time' =>  '13:00'
            ],
            [
                'schedule_id' => \App\Models\Schedule::first()->id,
                'start_time' => '14:00',
                'end_time' =>  '14:30'
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedule_unavailabilities');
    }
};
