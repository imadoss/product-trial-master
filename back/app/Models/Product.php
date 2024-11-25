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

    public function scopeFilterByKeyword($query, $keyword)
    {
        $query->when($keyword, function ($query, $keyword) {
            $query->where("name", "like", "%$keyword%")
                ->orWhere("description", "like", "%$keyword%")
                ->orWhere("internalReference", "like", "%$keyword%")
                ->orWhere("shellId", "like", "%$keyword%");
        });
    }

    public function scopeFilterByStatus($query, $status)
    {
        $query->when($status, function ($query, $status) {
            $query->where("inventoryStatus", "like", "$status");
        });
    }

    public function scopeFilterByCategory($query, $category)
    {
        $query->when($category, function ($query, $category) {
            $query->where("category", "like", "$category");
        });
    }
}
