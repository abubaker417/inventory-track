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

	public function exportPdf(Request $request, InventoryReportService $service)
	{
		[$location, $from, $to, $rows] = $this->resolveReport($request, $service);

		$view = view('reports.inventory', compact('location', 'from', 'to', 'rows'));

		if (class_exists('Barryvdh\\DomPDF\\Facade\\Pdf')) {
			$pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($view->render())->setPaper('a4', 'landscape');
			return $pdf->download('inventory-register.pdf');
		}

		return response($view)->header('Content-Type', 'text/html');
	}

	public function exportExcel(Request $request, InventoryReportService $service)
	{
		[$location, $from, $to, $rows] = $this->resolveReport($request, $service);

		if (class_exists('Maatwebsite\\Excel\\Facades\\Excel')) {
			$export = new \App\Exports\InventoryRegisterExport($location, $from, $to, $rows);
			return \Maatwebsite\Excel\Facades\Excel::download($export, 'inventory-register.xlsx');
		}

		$csv = $this->toCsv($location, $from, $to, $rows);
		return response($csv)
			->header('Content-Type', 'text/csv')
			->header('Content-Disposition', 'attachment; filename="inventory-register.csv"');
	}

	private function resolveReport(Request $request, InventoryReportService $service): array
	{
		$from = $request->input('from', '2020-02-01');
		$to = $request->input('to', '2025-02-01');
		$location = null;
		if ($request->filled('location_id')) {
			$location = Location::find($request->integer('location_id'));
		} elseif ($request->filled('location_name')) {
			$location = Location::where('name', $request->string('location_name'))->first();
		} else {
			$location = Location::where('name', 'Muzaffar Garh Factory')->first();
		}
		$rows = collect();
		if ($location) {
			$rows = $service->buildRegister($location->id, $from, $to);
		}
		return [$location, $from, $to, $rows];
	}

    public function exportCsv(Request $request, InventoryReportService $service)
	{
		[$location, $from, $to, $rows] = $this->resolveReport($request, $service);
		$csv = $this->toCsv($location, $from, $to, $rows);
		return response($csv)
			->header('Content-Type', 'text/csv')
			->header('Content-Disposition', 'attachment; filename="inventory-register.csv"');
	}

	private function toCsv($location, string $from, string $to, $rows): string
	{
		$fh = fopen('php://temp', 'w+');
		fputcsv($fh, ['Inventory Control Register']);
		fputcsv($fh, ['Location', $location?->name ?? '-']);
		fputcsv($fh, ['Date', $from . ' to ' . $to]);
		fputcsv($fh, []);
		fputcsv($fh, ['Sr #', 'Product Name',
			'Opening Qty','Opening Rate','Opening Amount',
			'Purchase Qty','Purchase Rate','Purchase Amount',
			'Consumption Qty','Consumption Rate','Consumption Amount',
			'Closing Qty','Closing Rate','Closing Amount']);
		foreach ($rows as $r) {
			fputcsv($fh, [
				$r['sr'], $r['product_name'],
				$r['opening_qty'], $r['opening_rate'] ?? '-', $r['opening_amount'],
				$r['purchase_qty'], $r['purchase_rate'] ?? '-', $r['purchase_amount'],
				$r['consumption_qty'], $r['consumption_rate'] ?? '-', $r['consumption_amount'],
				$r['closing_qty'], $r['closing_rate'] ?? '-', $r['closing_amount'],
			]);
		}
		rewind($fh);
		$csv = stream_get_contents($fh);
		fclose($fh);
		return $csv;
	}
}
