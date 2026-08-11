<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('checks', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->foreignUuid('application_id')
                ->constrained('applications')
                ->cascadeOnDelete();

            $table->string('status', 20);

            $table->integer('http_code')->nullable();

            $table->integer('response_time_ms')->nullable();

            $table->boolean('keyword_ok')->nullable();

            $table->boolean('auth_ok')->nullable();

            $table->integer('ssl_days_remaining')->nullable();

            $table->boolean('ssl_valid')->nullable();

            $table->text('error_message')->nullable();

            $table->timestamp('checked_at');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checks');
    }
};
