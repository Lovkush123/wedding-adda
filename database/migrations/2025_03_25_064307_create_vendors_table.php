<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique(); // Slug field added
            $table->unsignedBigInteger('sub_category_id'); // Foreign key removed
            $table->boolean('non_veg')->default(false);
            $table->boolean('veg')->default(true);
            $table->decimal('starting_price', 10, 2); // Decimal value for price
            $table->string('contact')->unique();
            $table->string('mail')->unique();
            $table->string('cover_image')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendors');
    }
};
