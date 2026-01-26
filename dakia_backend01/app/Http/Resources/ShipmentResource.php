<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentResource extends JsonResource
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
            'uuid' => $this->uuid ?? null,
            'customer_id' => $this->customer_id,
            'service_type' => $this->service_type,
            'warehouse_id' => $this->warehouse_id ?? null,
            'reference' => $this->reference,
            'notes' => $this->notes ?? null,
            'status' => $this->status ?? 'booked',
            'label_generated' => $this->label_generated ?? false,
            'label_generated_at' => $this->label_generated_at ?? null,
            'label_url' => $this->label_generated ? ("/labels/LBL-{$this->uuid}.pdf") : null,
            'parcels_count' => $this->whenCounted('parcels'),
            'items_count' => $this->whenLoaded('parcels', function() {
                if (!$this->parcels || $this->parcels->isEmpty()) {
                    return 0;
                }
                try {
                    return $this->parcels->sum(function($p) {
                        if (!$p->relationLoaded('items')) {
                            return 0;
                        }
                        return $p->items ? $p->items->count() : 0;
                    });
                } catch (\Exception $e) {
                    return 0;
                }
            }),
            'parcels' => $this->whenLoaded('parcels', function() {
                if (!$this->parcels || $this->parcels->isEmpty()) {
                    return [];
                }
                try {
                    return ShipmentParcelResource::collection($this->parcels);
                } catch (\Exception $e) {
                    \Log::error('ShipmentParcelResource collection error: ' . $e->getMessage());
                    return [];
                }
            }) ?? [],
            // Receiver Address
            'company' => $this->company ?? null,
            'contact' => $this->contact ?? null,
            'email' => $this->email ?? null,
            'telephone' => $this->telephone ?? null,
            'address_line_1' => $this->address_line_1 ?? null,
            'address_line_2' => $this->address_line_2 ?? null,
            'address_line_3' => $this->address_line_3 ?? null,
            'city' => $this->city ?? null,
            'state' => $this->state ?? null,
            'postcode' => $this->postcode ?? null,
            'country_id' => $this->country_id ?? null,
            // Sender Address
            'sender_company' => $this->sender_company ?? null,
            'sender_contact' => $this->sender_contact ?? null,
            'sender_email' => $this->sender_email ?? null,
            'sender_telephone' => $this->sender_telephone ?? null,
            'sender_address_line_1' => $this->sender_address_line_1 ?? null,
            'sender_address_line_2' => $this->sender_address_line_2 ?? null,
            'sender_address_line_3' => $this->sender_address_line_3 ?? null,
            'sender_city' => $this->sender_city ?? null,
            'sender_state' => $this->sender_state ?? null,
            'sender_postcode' => $this->sender_postcode ?? null,
            'sender_country_id' => $this->sender_country_id ?? null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
