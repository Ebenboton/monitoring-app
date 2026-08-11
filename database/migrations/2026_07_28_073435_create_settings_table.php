<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Valeurs par défaut
        $defaults = [
            // SMTP
            ['key' => 'mail_host',       'value' => 'smtp.gmail.com'],
            ['key' => 'mail_port',       'value' => '587'],
            ['key' => 'mail_encryption', 'value' => 'tls'],
            ['key' => 'mail_username',   'value' => ''],
            ['key' => 'mail_password',   'value' => ''],
            ['key' => 'mail_from',       'value' => 'M-Monitoring <monitoring@exemple.com>'],

            // Destinataires escalade
            ['key' => 'alert_email_level1',       'value' => ''],
            ['key' => 'alert_email_level2',       'value' => ''],
            ['key' => 'alert_delay_level2',       'value' => '15'],
            ['key' => 'alert_email_level3',       'value' => ''],
            ['key' => 'alert_delay_level3',       'value' => '30'],

            // Surveillance globale
            ['key' => 'default_check_interval',  'value' => '60'],
            ['key' => 'default_retry_count',     'value' => '3'],
            ['key' => 'check_retention_days',    'value' => '90'],
            ['key' => 'send_recovery_alert',     'value' => '1'],
        ];

        foreach ($defaults as $setting) {
            DB::table('settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
