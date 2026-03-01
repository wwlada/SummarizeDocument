<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->string('password')->nullable()->change();

            $table->uuid('visitor_token')->nullable()->unique()->after('id');
            $table->unsignedBigInteger('visits_count')->default(1)->after('visitor_token');
            $table->string('ip_address', 45)->nullable()->index()->after('visits_count');
            $table->string('user_agent', 500)->nullable()->index()->after('ip_address');
            $table->timestamp('last_visited_at')->nullable()->after('user_agent');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['visitor_token', 'visits_count', 'ip_address', 'user_agent', 'last_visited_at']);

            $table->string('name')->nullable(false)->change();
            $table->string('email')->nullable(false)->change();
            $table->string('password')->nullable(false)->change();
        });
    }
};
