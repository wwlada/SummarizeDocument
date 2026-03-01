<?php

use App\Models\Analytics;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(Analytics::TABLE, function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained(User::TABLE)->nullOnDelete();
            $table->unsignedInteger('input_tokens')->default(0);
            $table->unsignedInteger('output_tokens')->default(0);
            $table->unsignedInteger('total_tokens')->default(0);
            $table->unsignedSmallInteger('response_time')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(Analytics::TABLE);
    }
};
