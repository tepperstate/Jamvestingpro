<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'is_2fa_exempt')) {
                $table->boolean('is_2fa_exempt')->default(false)->after('password');
            }
        });

        Schema::table('admins', function (Blueprint $table) {
            if (! Schema::hasColumn('admins', 'is_super_admin')) {
                $table->boolean('is_super_admin')->default(false)->after('password');
            }
            if (! Schema::hasColumn('admins', 'is_2fa_exempt')) {
                $table->boolean('is_2fa_exempt')->default(false)->after('is_super_admin');
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_2fa_exempt');
        });

        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn(['is_super_admin', 'is_2fa_exempt']);
        });
    }
};
