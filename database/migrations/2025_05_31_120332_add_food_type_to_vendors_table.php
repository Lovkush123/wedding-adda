<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('vendors', function (Blueprint $table) {
            // Add 'food_type' column (nullable string)
            $table->string('food_type')->nullable()->after('based_area');
            
            // Add 'community_id' column (nullable unsignedBigInteger)
            $table->unsignedBigInteger('community_id')->nullable()->after('food_type');
        });
    }

    public function down()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn(['food_type', 'community_id']);
        });
    }
};
