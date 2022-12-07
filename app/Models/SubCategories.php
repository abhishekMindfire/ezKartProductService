<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategories extends Model
{
    use HasFactory;
    protected $table = "sub_categories";
    protected $fillable = [
        'id','category_id','name','created_at','updated_at'
    ];
}
