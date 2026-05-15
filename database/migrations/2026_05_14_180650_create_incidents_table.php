<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->text('description');
            $table->unsignedBigInteger('aircraft_id')->nullable();
            $table->unsignedBigInteger('reported_by');
            $table->unsignedBigInteger('assigned_investigator_id')->nullable();
            $table->string('severity', 20); // low|medium|high|critical
            $table->string('status', 30)->default('open'); // open|under-investigation|resolved|closed
            $table->timestamp('incident_date');
            $table->text('resolution_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('aircraft_id');
            $table->index('reported_by');
            $table->index('severity');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
