<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;
    protected $table = "products";
    protected $fillable = [
        'id','seller_id','category','sub_category','name','category_type','color','mrp','stock','size','description','image'
    ];

    public function category()
    {
        return $this->hasOne(\App\Models\Categories::class, 'id', 'category');
    }

    public function subCategory()
    {
        return $this->hasOne(\App\Models\SubCategories::class, 'id', 'sub_category');
    }

    public function image()
    {
        return $this->hasMany(\App\Models\ProductImages::class, 'product_id', 'id');
    }
}
