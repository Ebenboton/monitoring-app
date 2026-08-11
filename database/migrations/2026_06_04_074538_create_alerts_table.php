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
        Schema::create('alerts', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->foreignUuid('incident_id')
                ->constrained('incidents')
                ->cascadeOnDelete();

            $table->foreignUuid('application_id')
                ->constrained('applications')
                ->cascadeOnDelete();

            $table->string('recipient_email', 255);

            $table->string('subject', 500);

            $table->longText('body');

            $table->string('alert_type', 30);

            $table->integer('escalation_level')
                ->default(1);

            $table->string('status', 20)
                ->default('pending');

            $table->timestamp('sent_at')
                ->nullable();

            $table->text('error_message')
                ->nullable();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
