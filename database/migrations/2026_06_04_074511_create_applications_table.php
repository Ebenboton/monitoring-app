<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->char('id', 36)->primary();
            $table->string('name', 150);
            $table->string('url', 500);
            $table->string('method', 10)->default('GET');
            $table->string('accepted_codes', 50)->default('200');
            $table->integer('check_interval_seconds')->default(60);
            $table->integer('timeout_ms')->default(5000);
            $table->integer('latency_warn_ms')->default(800);
            $table->integer('latency_down_ms')->default(3000);
            $table->text('keyword_expected')->nullable();
            $table->text('keyword_forbidden')->nullable();
            $table->boolean('ssl_check')->default(true);
            $table->integer('ssl_alert_days')->default(30);
            $table->integer('retry_count')->default(3);
            $table->boolean('auth_enabled')->default(false);
            $table->string('auth_type', 20)->nullable();
            $table->string('auth_url', 500)->nullable();
            $table->longText('auth_credential')->nullable();
            $table->longText('auth_password')->nullable();
            $table->text('auth_success_keyword')->nullable();
            $table->json('headers')->nullable();
            $table->string('group_name', 100)->nullable();
            $table->json('tags')->nullable();
            $table->string('current_status', 20)->default('UNKNOWN');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_checked_at')->nullable();
            $table->char('created_by', 36)->nullable();
            $table->timestamps();

            $table->foreign('created_by')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};