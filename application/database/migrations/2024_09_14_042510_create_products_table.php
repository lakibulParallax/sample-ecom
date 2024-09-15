<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->unsignedBigInteger('sub_category_id')->nullable();
            $table->foreign('sub_category_id')->references('id')->on('sub_categories')->onDelete('cascade');
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');

            $table->string('name')->nullable();  // Product name
            $table->string('slug')->unique()->nullable();  // SEO-friendly URL slug
            $table->text('description')->nullable();  // Full product description
            $table->decimal('price', 10, 2)->nullable();  // Product price
            $table->decimal('discount', 10, 2)->nullable();  // Discounted price
            $table->decimal('shipping_cost', 10, 2)->nullable();  // Discounted price
            $table->decimal('other', 10, 2)->nullable();  // Discounted price
            $table->integer('quantity')->default(0);  // Stock quantity
            $table->string('sku')->unique()->nullable();  // Stock Keeping Unit (unique identifier)
            $table->boolean('status')->default(true);  // Product availability status

            // Product attributes
            $table->string('color')->nullable();  // Color (if applicable)
            $table->string('size')->nullable();  // Size (if applicable)

            // Image paths
            $table->string('main_image')->nullable();
            $table->bigInteger('creator_id')->nullable();
            $table->string('creator_type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
