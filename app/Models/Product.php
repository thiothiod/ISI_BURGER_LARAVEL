<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'image',
        'stock',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function isAvailable(): bool
    {
        return $this->status === 'disponible' && $this->stock > 0;
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'disponible')->where('stock', '>', 0);
    }

    public function scopeNotArchived($query)
    {
        return $query->where('status', '!=', 'archive');
    }
}