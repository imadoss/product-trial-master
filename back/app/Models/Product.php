<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    
    protected $guarded = ["id"];

    protected $casts = [
        "price" => "double",
        "quantity" => "integer",
        "shellId" => "integer",
        "rating" => "double",
    ];
}
