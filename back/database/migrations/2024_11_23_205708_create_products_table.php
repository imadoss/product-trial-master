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
            $table->string("code", 9)->unique();
            $table->string("name");
            $table->text("description")->nullable();
            $table->string("image")->nullable();
            $table->string("category")->nullable();
            $table->double("price");
            $table->integer("quantity")->default(0);
            $table->string("internalReference", 11);
            $table->integer("shellId");
            $table->enum("inventoryStatus", ["INSTOCK", "LOWSTOCK", "OUTOFSTOCK"])->default("INSTOCK");
            $table->double("rating")->default(0);
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
