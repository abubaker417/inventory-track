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
        Schema::create('sales', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->unsignedBigInteger('product_id');
			$table->unsignedBigInteger('location_id');
			$table->datetime('sold_at');
			$table->decimal('quantity', 18, 4);
			$table->decimal('unit_price', 18, 4)->default(0);
			$table->string('reference', 128)->nullable();
			$table->timestamps();

			$table->index(['product_id', 'sold_at']);
			$table->index(['location_id', 'sold_at']);
			$table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
			$table->foreign('location_id')->references('id')->on('locations')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
