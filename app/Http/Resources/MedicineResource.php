<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicineResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'brand' => $this->brand,
            'dosage_form' => $this->dosage_form,
            'unit_price' => number_format($this->unit_price, 0, ',', '.'),
            'stock_quantity' => $this->stock_quantity,
            'stock_status' => $this->stock_quantity <= $this->minimum_stock ? 'low' : 'normal',
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
