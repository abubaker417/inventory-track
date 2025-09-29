<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InventoryReportService
{
	public function buildRegister(int $locationId, string $fromDate, string $toDate): Collection
	{
		$from = Carbon::parse($fromDate)->startOfDay();
		$to = Carbon::parse($toDate)->endOfDay();

		$inwardInRange = Purchase::query()
			->select(['product_id', DB::raw('SUM(quantity) as qty'), DB::raw('NULLIF(SUM(quantity * unit_price) / NULLIF(SUM(quantity),0), 0) as avg_rate')])
			->where('location_id', $locationId)
			->whereBetween('purchased_at', [$from->toDateString(), $to->toDateString()])
			->groupBy('product_id')
			->get()
			->keyBy('product_id');

		$outwardInRange = Sale::query()
			->select(['product_id', DB::raw('SUM(quantity) as qty'), DB::raw('NULLIF(SUM(quantity * unit_price) / NULLIF(SUM(quantity),0), 0) as avg_rate')])
			->where('location_id', $locationId)
			->whereBetween('sold_at', [$from->toDateString(), $to->toDateString()])
			->groupBy('product_id')
			->get()
			->keyBy('product_id');

		$inwardBefore = Purchase::query()
			->select(['product_id', DB::raw('SUM(quantity) as qty'), DB::raw('NULLIF(SUM(quantity * unit_price) / NULLIF(SUM(quantity),0), 0) as avg_rate')])
			->where('location_id', $locationId)
			->where('purchased_at', '<', $from->toDateString())
			->groupBy('product_id')
			->get()
			->keyBy('product_id');

		$outwardBefore = Sale::query()
			->select(['product_id', DB::raw('SUM(quantity) as qty'), DB::raw('NULLIF(SUM(quantity * unit_price) / NULLIF(SUM(quantity),0), 0) as avg_rate')])
			->where('location_id', $locationId)
			->where('sold_at', '<', $from->toDateString())
			->groupBy('product_id')
			->get()
			->keyBy('product_id');

		$productIds = collect([
			$inwardInRange->keys(),
			$outwardInRange->keys(),
			$inwardBefore->keys(),
			$outwardBefore->keys(),
		])->flatten()->unique()->values();

		$products = Product::query()
			->whereIn('id', $productIds)
			->pluck('name', 'id');

		$rows = collect();
		$sr = 1;
		foreach ($productIds as $productId) {
			$openQty = ($inwardBefore[$productId]->qty ?? 0) - ($outwardBefore[$productId]->qty ?? 0);
			$openRate = $inwardBefore[$productId]->avg_rate ?? null;
			$openAmount = $openRate ? $openQty * $openRate : 0;

			$purchaseQty = $inwardInRange[$productId]->qty ?? 0;
			$purchaseRate = $inwardInRange[$productId]->avg_rate ?? null;
			$purchaseAmount = $purchaseRate ? $purchaseQty * $purchaseRate : 0;

			$consumptionQty = $outwardInRange[$productId]->qty ?? 0;
			$consumptionRate = $outwardInRange[$productId]->avg_rate ?? null;
			$consumptionAmount = $consumptionRate ? $consumptionQty * $consumptionRate : 0;

			$closingQty = $openQty + $purchaseQty - $consumptionQty;
			$closingRate = $purchaseRate ?? $openRate ?? $consumptionRate ?? 0; // fallback
			$closingAmount = $closingRate ? $closingQty * $closingRate : 0;

			$rows->push([
				'sr' => $sr++,
				'product_id' => $productId,
				'product_name' => $products[$productId] ?? ('Product #' . $productId),
				'opening_qty' => (float)$openQty,
				'opening_rate' => $openRate ? (float)$openRate : null,
				'opening_amount' => (float)$openAmount,
				'purchase_qty' => (float)$purchaseQty,
				'purchase_rate' => $purchaseRate ? (float)$purchaseRate : null,
				'purchase_amount' => (float)$purchaseAmount,
				'consumption_qty' => (float)$consumptionQty,
				'consumption_rate' => $consumptionRate ? (float)$consumptionRate : null,
				'consumption_amount' => (float)$consumptionAmount,
				'closing_qty' => (float)$closingQty,
				'closing_rate' => $closingRate ? (float)$closingRate : null,
				'closing_amount' => (float)$closingAmount,
			]);
		}

		return $rows;
	}
}