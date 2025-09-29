<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Inventory Control Register</title>
	<style>
		body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; }
		table { border-collapse: collapse; width: 100%; }
		th, td { border: 1px solid #777; padding: 6px 8px; text-align: right; }
		th:nth-child(1), td:nth-child(1), th:nth-child(2), td:nth-child(2) { text-align: left; }
		thead th { background: #f0f0f0; }
		.small { font-size: 11px; color: #333; }
		.header { margin-bottom: 10px; }
	</style>
</head>
<body>
	<h2>Inventory Control Register</h2>
	<div class="header small">
		<div><strong>Location:</strong> {{ $location?->name ?? 'Muzaffar Garh Factory' }}</div>
		<div><strong>Date:</strong> {{ $from }} to {{ $to }}</div>
	</div>

	<table>
		<thead>
			<tr>
				<th rowspan="2">Sr #</th>
				<th rowspan="2">Product Name</th>
				<th colspan="3">Opening</th>
				<th colspan="3">Purchase</th>
				<th colspan="3">Consumption</th>
				<th colspan="3">Closing Balance</th>
			</tr>
			<tr>
				<th>Qty</th><th>Rate</th><th>Amount</th>
				<th>Qty</th><th>Rate</th><th>Amount</th>
				<th>Qty</th><th>Rate</th><th>Amount</th>
				<th>Qty</th><th>Rate</th><th>Amount</th>
			</tr>
		</thead>
		<tbody>
		@forelse($rows as $r)
			<tr>
				<td>{{ $r['sr'] }}</td>
				<td>{{ $r['product_name'] }}</td>
				<td>{{ number_format($r['opening_qty'], 0) }}</td>
				<td>{{ $r['opening_rate'] === null ? '-' : number_format($r['opening_rate'], 0) }}</td>
				<td>{{ number_format($r['opening_amount'], 0) }}</td>
				<td>{{ number_format($r['purchase_qty'], 0) }}</td>
				<td>{{ $r['purchase_rate'] === null ? '-' : number_format($r['purchase_rate'], 0) }}</td>
				<td>{{ number_format($r['purchase_amount'], 0) }}</td>
				<td>{{ number_format($r['consumption_qty'], 0) }}</td>
				<td>{{ $r['consumption_rate'] === null ? '-' : number_format($r['consumption_rate'], 0) }}</td>
				<td>{{ number_format($r['consumption_amount'], 0) }}</td>
				<td>{{ number_format($r['closing_qty'], 0) }}</td>
				<td>{{ $r['closing_rate'] === null ? '-' : number_format($r['closing_rate'], 0) }}</td>
				<td>{{ number_format($r['closing_amount'], 0) }}</td>
			</tr>
		@empty
			<tr><td colspan="14" style="text-align:center;">No data</td></tr>
		@endforelse
		</tbody>
	</table>
</body>
</html>

