<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_aircraft_states', function (Blueprint $table) {
            $table->id();
            $table->date('report_date')->index();
            $table->foreignId('aircraft_id')->nullable()->constrained('aircraft')->nullOnDelete();
            $table->foreignId('wing_id')->nullable()->constrained('wings')->nullOnDelete();
            $table->decimal('daily_flight_hrs', 7, 2)->default(0);
            $table->decimal('total_flight_hrs', 9, 2)->nullable();
            $table->unsignedSmallInteger('daily_landings')->default(0);
            $table->unsignedInteger('total_landings')->nullable();
            $table->enum('state', ['S', 'U/S', 'grounded'])->default('S');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['report_date', 'aircraft_id'], 'daily_state_unique');
        });

        Schema::create('daily_defects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_aircraft_state_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('defect_number')->unsigned();
            $table->text('description');
            $table->boolean('is_critical')->default(false);
            $table->timestamps();
        });

        Schema::create('daily_service_remarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_aircraft_state_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('remark_number')->unsigned();
            $table->text('description');
            $table->decimal('due_hours', 9, 2)->nullable();
            $table->string('service_location', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_service_remarks');
        Schema::dropIfExists('daily_defects');
        Schema::dropIfExists('daily_aircraft_states');
    }
};
