<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentItemResource extends JsonResource
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
            'description' => $this->description ?? '',
            'quantity' => $this->quantity ?? 1,
            'weight' => $this->weight ? (float) $this->weight : 0.0,
            'value' => $this->value ? (float) $this->value : 0.0,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
