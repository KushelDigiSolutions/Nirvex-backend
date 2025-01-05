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
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('cat_id');
            $table->unsignedBigInteger('sub_cat_id')->nullable();
            $table->boolean('status')->default(1);
            $table->text('image')->nullable();
            $table->decimal('mrp', 10, 2);
            $table->boolean('availability')->default(1);
            $table->text('specification')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('cat_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('sub_cat_id')->references('id')->on('subcategories')->onDelete('set null');
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
