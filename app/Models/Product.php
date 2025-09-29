<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
		'sku',
		'name',
		'is_discontinued',
	];

	public function purchases(): HasMany
	{
		return $this->hasMany(Purchase::class);
	}

	public function sales(): HasMany
	{
		return $this->hasMany(Sale::class);
	}
}
