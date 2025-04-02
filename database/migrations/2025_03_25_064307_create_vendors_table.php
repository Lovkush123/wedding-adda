<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Name field with unique constraint
            $table->string('slug')->unique(); // Slug field
            $table->string('address1'); // Address line 1
            $table->string('address2')->nullable(); // Address line 2 (optional)
            $table->string('map_url')->nullable(); // Map URL field
            $table->string('state'); // State field
            $table->string('city'); // City field
            $table->string('country'); // Country field
            $table->unsignedBigInteger('category_id'); // Category ID
            $table->unsignedBigInteger('subcategory_id'); // Subcategory ID
            $table->enum('price_type', ['fixed', 'variable']); // Price type (fixed or variable)
            $table->decimal('starting_price', 10, 2)->nullable(); // Starting price
            $table->decimal('ending_price', 10, 2)->nullable(); // Ending price
            $table->string('about_title'); // About section title
            $table->text('text_editor'); // Text editor content
            $table->string('call_number')->unique(); // Call number
            $table->string('whatsapp_number')->unique(); // WhatsApp number
            $table->string('mail_id')->unique(); // Email ID
            $table->string('cover_image')->nullable(); // Cover image
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendors');
    }
};
