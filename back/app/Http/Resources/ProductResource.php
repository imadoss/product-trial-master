<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "code" => $this->code,
            "name" => $this->name,
            "description" => $this->description,
            "image" => url("storage/" . $this->image),
            "category" => $this->category,
            "price" => $this->price,
            "quantity" => $this->quantity,
            "internalReference" => $this->internalReference,
            "shellId" => $this->shellId,
            "inventoryStatus" => $this->inventoryStatus,
            "rating" => $this->rating,
            "createdAt" => $this->created_at->timestamp,
            "updatedAt" => $this->updated_at?->timestamp,
        ];
    }
}
