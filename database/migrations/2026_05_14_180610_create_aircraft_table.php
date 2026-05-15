<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aircraft', function (Blueprint $table) {
            $table->id();
            $table->string('tail_number', 20)->unique();
            $table->string('model', 100);
            $table->string('manufacturer', 100);
            $table->unsignedSmallInteger('year_manufactured')->nullable();
            $table->unsignedBigInteger('wing_id')->nullable();
            $table->string('status', 20)->default('active'); // active|maintenance|grounded|retired
            $table->date('last_maintenance_date')->nullable();
            $table->decimal('total_flight_hours', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('wing_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aircraft');
    }
};
