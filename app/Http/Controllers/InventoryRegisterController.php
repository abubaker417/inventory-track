<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Services\InventoryReportService;
use Illuminate\Support\Carbon;

class InventoryRegisterController extends Controller
{
    public function index(Request $request, InventoryReportService $service)
	{
		$validated = $request->validate([
			'location_id' => ['nullable', 'integer', 'exists:locations,id'],
			'location_name' => ['nullable', 'string'],
			'from' => ['nullable', 'date'],
			'to' => ['nullable', 'date', 'after_or_equal:from'],
		]);

		$from = $validated['from'] ?? '2020-02-01';
		$to = $validated['to'] ?? '2025-02-01';

		$location = null;
		if (!empty($validated['location_id'])) {
			$location = Location::find($validated['location_id']);
		} elseif (!empty($validated['location_name'])) {
			$location = Location::where('name', $validated['location_name'])->first();
		} else {
			$location = Location::where('name', 'Muzaffar Garh Factory')->first();
		}

		$rows = collect();
		if ($location) {
			$rows = $service->buildRegister($location->id, $from, $to);
		}

		return view('reports.inventory', [
			'location' => $location,
			'from' => Carbon::parse($from)->toDateString(),
			'to' => Carbon::parse($to)->toDateString(),
			'rows' => $rows,
		]);
	}
}
