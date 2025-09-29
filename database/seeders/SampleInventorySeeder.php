<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Location;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;

class SampleInventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
	{
		$location = Location::firstOrCreate(['name' => 'Muzaffar Garh Factory']);

		$flywheel = Product::firstOrCreate(['sku' => 'FW-NPR', 'name' => 'FLYWHEEL ISUZU NPR']);
		$testTube = Product::firstOrCreate(['sku' => 'TT-001', 'name' => 'TEST TUBE']);
		$diesel = Product::firstOrCreate(['sku' => 'DSL-001', 'name' => 'DIESEL FUEL']);
		$test = Product::firstOrCreate(['sku' => 'TST-001', 'name' => 'test']);

		Sale::firstOrCreate([
			'product_id' => $testTube->id,
			'location_id' => $location->id,
			'sold_at' => '2020-01-15',
			'quantity' => 2,
			'unit_price' => 220,
		]);

		Purchase::firstOrCreate([
			'product_id' => $flywheel->id,
			'location_id' => $location->id,
			'purchased_at' => '2020-03-01',
			'quantity' => 1,
			'unit_price' => 1500,
		]);

		Sale::firstOrCreate([
			'product_id' => $testTube->id,
			'location_id' => $location->id,
			'sold_at' => '2020-04-10',
			'quantity' => 3,
			'unit_price' => 220,
		]);

		Sale::firstOrCreate([
			'product_id' => $diesel->id,
			'location_id' => $location->id,
			'sold_at' => '2020-05-05',
			'quantity' => 5,
			'unit_price' => 292,
		]);

		Purchase::firstOrCreate([
			'product_id' => $test->id,
			'location_id' => $location->id,
			'purchased_at' => '2020-06-15',
			'quantity' => 12,
			'unit_price' => 500,
		]);
	}
}
