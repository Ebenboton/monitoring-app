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
        Schema::create('incidents', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->foreignUuid('application_id')
                ->constrained('applications')
                ->cascadeOnDelete();

            $table->foreignUuid('acknowledged_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('started_at');

            $table->timestamp('resolved_at')
                ->nullable();

            $table->integer('duration_seconds')
                ->nullable();

            $table->string('root_cause', 50)
                ->nullable();

            $table->text('notes')
                ->nullable();

            $table->timestamp('acknowledged_at')
                ->nullable();

            $table->boolean('is_resolved')
                ->default(false);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
