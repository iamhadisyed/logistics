<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 10mm; }
        body { font-family: 'Helvetica', sans-serif; font-size: 11px; color: #111; }
        .label { border: 2px solid #000; padding: 10px; width: 100%; box-sizing: border-box; }
        .header { display: table; width: 100%; border-bottom: 2px solid #000; padding-bottom: 6px; margin-bottom: 8px; }
        .header .service { display: table-cell; font-size: 20px; font-weight: bold; }
        .header .ref { display: table-cell; text-align: right; font-size: 14px; }
        .section-title { font-size: 9px; text-transform: uppercase; color: #555; margin-top: 8px; margin-bottom: 2px; }
        .address { font-size: 13px; line-height: 1.4; }
        .address .name { font-weight: bold; font-size: 15px; }
        .barcode { text-align: center; margin: 14px 0; }
        /* Not a real scannable barcode (no barcode font available in this
           environment) — a bordered reference block for now. Replace with a
           real barcode/QR renderer (e.g. picqer/php-barcode-generator or a
           QR package) before this label is used in production. */
        .barcode .bars { font-family: monospace; font-size: 18px; letter-spacing: 4px; border: 1px solid #000; padding: 6px 4px; display: inline-block; }
        .barcode .code { font-size: 12px; letter-spacing: 2px; margin-top: 4px; }
        .meta-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .meta-table td { border: 1px solid #999; padding: 4px 6px; font-size: 10px; }
        .parcel-list { margin-top: 8px; font-size: 10px; }
    </style>
</head>
<body>
    <div class="label">
        <div class="header">
            <div class="service">{{ $shipment->service_type }}</div>
            <div class="ref">HAWB: {{ $shipment->reference }}</div>
        </div>

        <div class="section-title">Deliver To</div>
        <div class="address">
            <div class="name">{{ $shipment->company ?: $shipment->contact }}</div>
            @if($shipment->contact && $shipment->company)
                <div>{{ $shipment->contact }}</div>
            @endif
            <div>{{ $shipment->address_line_1 }}</div>
            @if($shipment->address_line_2)<div>{{ $shipment->address_line_2 }}</div>@endif
            @if($shipment->address_line_3)<div>{{ $shipment->address_line_3 }}</div>@endif
            <div>{{ $shipment->city }}{{ $shipment->state ? ', ' . $shipment->state : '' }} {{ $shipment->postcode }}</div>
            <div>{{ $countryName }}</div>
            @if($shipment->telephone)<div>Tel: {{ $shipment->telephone }}</div>@endif
        </div>

        @if($shipment->sender_company || $shipment->sender_address_line_1)
        <div class="section-title">Ship From</div>
        <div class="address" style="font-size: 11px;">
            <div>{{ $shipment->sender_company ?: $shipment->sender_contact }}</div>
            <div>{{ $shipment->sender_address_line_1 }}, {{ $shipment->sender_city }} {{ $shipment->sender_postcode }}</div>
        </div>
        @endif

        <div class="barcode">
            <div class="bars">*{{ $shipment->uuid }}*</div>
            <div class="code">{{ strtoupper($shipment->reference) }}</div>
        </div>

        <table class="meta-table">
            <tr>
                <td><strong>Parcels</strong><br>{{ $shipment->parcels->count() }}</td>
                <td><strong>Total Weight</strong><br>{{ number_format($shipment->parcels->sum('weight'), 2) }} kg</td>
                <td><strong>Generated</strong><br>{{ $generatedAt }}</td>
            </tr>
        </table>

        <div class="parcel-list">
            @foreach($shipment->parcels as $index => $parcel)
                <div>Parcel {{ $index + 1 }}: {{ $parcel->weight }}kg, {{ $parcel->length }}x{{ $parcel->width }}x{{ $parcel->height }}cm
                    ({{ $parcel->items->count() }} item{{ $parcel->items->count() === 1 ? '' : 's' }})
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>
