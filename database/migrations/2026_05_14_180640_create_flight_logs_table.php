<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flight_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('aircraft_id');
            $table->unsignedBigInteger('pilot_id');
            $table->unsignedBigInteger('co_pilot_id')->nullable();
            $table->string('departure_location', 255);
            $table->string('arrival_location', 255);
            $table->timestamp('departure_time');
            $table->timestamp('arrival_time');
            $table->unsignedInteger('flight_duration_minutes')->default(0);
            $table->unsignedInteger('max_altitude_ft')->nullable();
            $table->unsignedInteger('max_speed_knots')->nullable();
            $table->json('gps_track')->nullable();
            $table->string('mission_type', 20); // training|operational|test|ferry
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('aircraft_id');
            $table->index('pilot_id');
            $table->index('mission_type');
            $table->index('departure_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flight_logs');
    }
};
