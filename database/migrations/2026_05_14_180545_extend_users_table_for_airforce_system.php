<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Extend role to support all 5 military roles
            // SQLite doesn't support modifying enum columns, so we use string
            $table->string('rank', 50)->nullable()->after('role');
            $table->unsignedBigInteger('wing_id')->nullable()->after('rank');
            $table->integer('failed_attempts')->default(0)->after('wing_id');
            $table->timestamp('last_failed_login')->nullable()->after('failed_attempts');
            $table->softDeletes();

            $table->index('wing_id');
            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn(['rank', 'wing_id', 'failed_attempts', 'last_failed_login']);
        });
    }
};
