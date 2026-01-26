<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShipmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authentication handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'consignment' => 'required|array',
            'consignment.customer_id' => 'required|integer',
            'consignment.service_type' => 'required|string',
            'consignment.warehouse_id' => 'nullable|integer',
            'consignment.reference' => 'required|string|max:50',
            'consignment.notes' => 'nullable|string',

            // Receiver Validation
            'consignment.company' => 'required|string|max:100', // OR contact name required
            'consignment.contact' => 'required|string|max:100',
            'consignment.email' => 'nullable|email',
            'consignment.telephone' => 'required|string|max:20',
            'consignment.address_line_1' => 'required|string|max:255',
            'consignment.address_line_2' => 'nullable|string|max:255',
            'consignment.city' => 'required|string|max:50',
            'consignment.state' => 'nullable|string|max:50',
            'consignment.postcode' => 'required|string|max:20',
            'consignment.country_id' => 'required|integer',

            // Sender Validation (Optional)
            'consignment.sender_company' => 'nullable|string|max:100',
            'consignment.sender_contact' => 'nullable|string|max:100',

            'parcels' => 'required|array|min:1',
            'parcels.*.weight' => 'required|numeric|min:0.01',
            'parcels.*.length' => 'required|numeric|min:0.01',
            'parcels.*.width' => 'required|numeric|min:0.01',
            'parcels.*.height' => 'required|numeric|min:0.01',
            'parcels.*.notes' => 'nullable|string',

            'parcels.*.items' => 'nullable|array',
            'parcels.*.items.*.description' => 'required_with:parcels.*.items|string|max:255',
            'parcels.*.items.*.quantity' => 'required_with:parcels.*.items|integer|min:1',
            'parcels.*.items.*.weight' => 'required_with:parcels.*.items|numeric|min:0',
            'parcels.*.items.*.value' => 'required_with:parcels.*.items|numeric|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'parcels.min' => 'At least one parcel is required per consignment.',
        ];
    }
}
