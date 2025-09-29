<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
		'product_id',
		'location_id',
		'purchased_at',
		'quantity',
		'unit_price',
		'reference',
	];

	protected $casts = [
		'purchased_at' => 'datetime',
		'quantity' => 'decimal:4',
		'unit_price' => 'decimal:4',
	];

	public function product(): BelongsTo
	{
		return $this->belongsTo(Product::class);
	}

	public function location(): BelongsTo
	{
		return $this->belongsTo(Location::class);
	}
}
