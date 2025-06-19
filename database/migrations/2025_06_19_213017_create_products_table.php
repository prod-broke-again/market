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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_name')->nullable();
            $table->string('brand')->nullable();
            $table->json('product_images')->nullable();
            $table->json('product_colors')->nullable();
            $table->foreignId('seller_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('product_description')->nullable();
            $table->foreignId('location_id')->nullable()->constrained('addresses')->onDelete('set null');
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->decimal('price', 13, 2)->nullable();
            $table->decimal('price_discount', 13, 2)->nullable();
            $table->tinyInteger('status_placement')->default(1);
            $table->tinyInteger('availability')->default(1);
            $table->tinyInteger('is_draft')->default(0);
            $table->string('articul')->nullable();
            $table->bigInteger('count')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
