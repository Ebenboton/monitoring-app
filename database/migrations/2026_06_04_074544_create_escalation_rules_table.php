<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('escalation_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('application_id')->nullable(); // nullable pour les règles globales
            $table->integer('level');
            $table->integer('delay_minutes');
            $table->string('recipient_email', 255);
            $table->timestamps(); // created_at + updated_at

            $table->foreign('application_id')
                  ->references('id')
                  ->on('applications')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('escalation_rules');
    }
};