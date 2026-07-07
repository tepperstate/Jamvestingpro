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
        Schema::table('admins', function (Blueprint $table) {
            if (! Schema::hasColumn('admins', 'role')) {
                $table->enum('role', ['super_admin', 'admin'])->default('admin')->after('email');
            }
            if (! Schema::hasColumn('admins', 'is_exempt_from_2fa')) {
                $table->boolean('is_exempt_from_2fa')->default(false)->after('role');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'is_exempt_from_2fa')) {
                $table->boolean('is_exempt_from_2fa')->default(false)->after('email');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admins_and_users', function (Blueprint $table) {
            //
        });
    }
};
