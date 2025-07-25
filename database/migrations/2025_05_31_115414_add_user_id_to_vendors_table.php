<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('vendors', function (Blueprint $table) {
            // Add user_id column (without foreign key constraint)
            $table->unsignedBigInteger('user_id')->after('id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
};
