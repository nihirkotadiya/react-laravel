<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // Fields that can be mass-assigned
    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    // A category has many products
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
