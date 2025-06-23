<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('community_vendor', function (Blueprint $table) {
            $table->foreign('vendor_id')
                ->references('id')->on('vendors')
                ->onDelete('cascade');

            $table->foreign('community_id')
                ->references('id')->on('communities')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('community_vendor', function (Blueprint $table) {
            $table->dropForeign(['vendor_id']);
            $table->dropForeign(['community_id']);
        });
    }

};
