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
        $finalPrice = $this->discount ? ($this->price * (1 - $this->discount / 100)) : $this->price;

        return [
            'id' => $this->id,
            'name' => $this->title,
            'title' => $this->title,
            'slug' => $this->slug,
            'brand' => $this->brand ? $this->brand->title : null,
            'category' => $this->cat_info ? $this->cat_info->title : null,
            'price' => (float) $this->price,
            'final_price' => (float) $finalPrice,
            'discount' => $this->discount ? (float) $this->discount : null,
            'stock' => (int) $this->stock,
            'size' => $this->size,
            'condition' => $this->condition,
            'url' => route('product-detail', $this->slug),
            'image' => $this->photo ? asset('storage/photos/1/' . $this->photo) : null,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
