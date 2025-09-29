<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class InventoryRegisterExport implements FromView, ShouldAutoSize
{
    public function __construct(
		public ?\App\Models\Location $location,
		public string $from,
		public string $to,
		public \Illuminate\Support\Collection $rows
	) {}

	public function view(): View
	{
		return view('reports.inventory', [
			'location' => $this->location,
			'from' => $this->from,
			'to' => $this->to,
			'rows' => $this->rows,
		]);
	}
}
