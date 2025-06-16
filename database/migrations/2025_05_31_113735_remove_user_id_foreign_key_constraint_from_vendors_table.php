<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        Schema::table('vendors', function (Blueprint $table) {
            // Drop FK only if it exists
            if (Schema::hasColumn('vendors', 'user_id')) {
                // You may need to use DB raw query if Laravel can't detect FK
                DB::statement('ALTER TABLE vendors DROP FOREIGN KEY vendors_user_id_foreign');
                $table->dropColumn('user_id');
            }
        });
    }

    public function down()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->after('id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
