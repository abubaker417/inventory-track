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
        Schema::create('purchases', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->unsignedBigInteger('product_id');
			$table->unsignedBigInteger('location_id');
			$table->datetime('purchased_at');
			$table->decimal('quantity', 18, 4);
			$table->decimal('unit_price', 18, 4)->default(0);
			$table->string('reference')->nullable();
			$table->timestamps();

			$table->index(['product_id', 'purchased_at']);
			$table->index(['location_id', 'purchased_at']);
			$table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
			$table->foreign('location_id')->references('id')->on('locations')->cascadeOnDelete();;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
