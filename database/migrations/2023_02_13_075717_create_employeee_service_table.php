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
        Schema::create('employee_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
        \Illuminate\Support\Facades\DB::table('employee_service')->insert([
            [
                'employee_id' => \App\Models\Employee::query()->inRandomOrder()->value('id'),
                'service_id' => \App\Models\Service::query()->inRandomOrder()->value('id')
            ],
            [
                'employee_id' => \App\Models\Employee::query()->inRandomOrder()->value('id'),
                'service_id' => \App\Models\Service::query()->inRandomOrder()->value('id')
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
        Schema::dropIfExists('employee_service');
    }
};
