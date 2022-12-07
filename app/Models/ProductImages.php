<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImages extends Model
{
    use HasFactory;
    protected $table = "product_images";
    protected $fillable = [
        'id','seller_id','product_id','image_url','created_at','updated_at'
    ];
}
