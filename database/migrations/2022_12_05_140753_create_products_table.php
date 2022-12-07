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
            $table->integer('seller_id')->nullable();
            $table->string('name')->length(255)->nullable();
            $table->integer('category')->nullable();
            $table->integer('sub_category')->nullable();
            $table->string('color')->nullable();
            $table->integer('mrp')->nullable();
            $table->integer('stock')->nullable();
            $table->string('size')->nullable();
            $table->text('description')->nullable();
            $table->varchar('image')->length(255)->nullable();
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
