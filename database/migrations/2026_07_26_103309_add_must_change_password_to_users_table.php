<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rôle : ajoute la colonne must_change_password à la table users.
     *
     * Cette colonne est utilisée pour forcer un utilisateur à changer
     * son mot de passe à la prochaine connexion.
     *
     * Cas d'usage :
     * - Création d'un nouveau compte → must_change_password = true
     * - Réinitialisation par le Super Admin → must_change_password = true
     * - Après changement → must_change_password = false
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('must_change_password')->default(false)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('must_change_password');
        });
    }
};