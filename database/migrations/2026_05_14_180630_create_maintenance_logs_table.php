<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('maintenance_task_id')->nullable();
            $table->unsignedBigInteger('aircraft_id');
            $table->unsignedBigInteger('engineer_id');
            $table->text('work_performed');
            $table->text('parts_used')->nullable();
            $table->decimal('hours_spent', 6, 2);
            $table->date('log_date');
            $table->string('status', 20)->default('draft'); // draft|submitted|approved
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('maintenance_task_id');
            $table->index('aircraft_id');
            $table->index('engineer_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_logs');
    }
};
