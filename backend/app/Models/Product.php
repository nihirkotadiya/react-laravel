<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    // Fields that can be mass-assigned
    protected $fillable = [
        'name',
        'category_id',
        'price',
        'stock',
        'description',
        'status',
    ];

    // A product belongs to a category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
