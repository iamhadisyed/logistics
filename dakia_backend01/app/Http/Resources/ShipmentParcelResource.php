<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentParcelResource extends JsonResource
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
            'shipment_id' => $this->shipment_id,
            'weight' => $this->weight ? (float) $this->weight : 0.0,
            'length' => $this->length ? (float) $this->length : 0.0,
            'width' => $this->width ? (float) $this->width : 0.0,
            'height' => $this->height ? (float) $this->height : 0.0,
            'notes' => $this->notes ?? null,
            'items' => $this->whenLoaded('items', function() {
                if (!$this->items || $this->items->isEmpty()) {
                    return [];
                }
                try {
                    return ShipmentItemResource::collection($this->items);
                } catch (\Exception $e) {
                    return [];
                }
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
