// Consignment types
export interface Consignment {
    id: number;
    user_id: number;
    service_id: number;
    hawb: string;
    awb: string;
    shipment_status: number;
    shipment_type: string;
    reference?: string;
    date_created: string;
    date_label_created?: string;

    // Receiver address
    company?: string;
    contact: string;
    address_line_1: string;
    address_line_2?: string;
    address_line_3?: string;
    city: string;
    state?: string;
    postcode?: string;
    country_id: number;
    telephone?: string;
    email?: string;

    // Sender address
    sender_company?: string;
    sender_contact?: string;
    sender_address_line_1?: string;
    sender_address_line_2?: string;
    sender_city?: string;
    sender_postcode?: string;
    sender_country_id?: number;
    sender_telephone?: string;
    sender_email?: string;

    // Parcel details
    number_pieces: number;
    weight: number;
    vol_weight?: number;
    charge_weight?: number;
    description: string;
    value?: number;
    currency?: string;
    is_doc: boolean;

    // Special fields
    remote_charges?: boolean;
    is_insured?: boolean;
    eori_number?: string;
    vat_number?: string;
    label_file?: string;

    // Relationships
    service?: Service;
    country?: Country;
    parcels?: Parcel[];
    charges?: ConsignmentCharge[];
}

export interface Parcel {
    id: number;
    consignment_id: number;
    tracking_number?: string;
    length: number;
    width: number;
    height: number;
    weight: number;
    barcode_data?: string;
}

export interface ConsignmentCharge {
    id: number;
    consignment_id: number;
    charge_type: string;
    amount: number;
    currency: string;
    description?: string;
}

// Service types
export interface Service {
    id: number;
    carrier_id: number;
    name: string;
    code: string;
    carrier_service_code?: string;
    type: 'D' | 'I' | 'E' | 'R'; // Domestic, International, Europe Road, Return
    active: boolean;
    from_weight: number;
    to_weight: number;
    label_class_name: string;
    volumetric_denominator: number;
    fuel_surcharge?: number;
    tracking_flag: boolean;
    insurance_available: boolean;

    // Relationships
    carrier?: Carrier;
}

// Carrier types
export interface Carrier {
    id: number;
    carrier: string;
    carrier_display_name?: string;
    logo?: string;
    status: number; // 0=Inactive, 1=Active, 2=Deleted
    country_id?: number;
    currency_code?: string;

    // Relationships
    services?: Service[];
}

// Country types
export interface Country {
    id: number;
    name: string;
    iso: string;
    region?: string;
    postcode_required: boolean;
}

// Consignment status constants
export const CONSIGNMENT_STATUS = {
    NEW: 10,
    INVALID: 11,
    READY_TO_PRINT: 12,
    LABEL_CREATED: 13,
    RECEIVED: 14,
    DISPATCHED: 16,
    INTRANSIT: 18,
    DELIVERED: 19,
    RECYCLED: 22,
    CANCELLED: 23,
    HOLD: 24,
    PROBLEM: 25,
    RETURNED: 27,
} as const;

export const CONSIGNMENT_STATUS_LABELS: Record<number, string> = {
    10: 'New',
    11: 'Invalid',
    12: 'Ready to Print',
    13: 'Label Created',
    14: 'Received',
    16: 'Dispatched',
    18: 'In Transit',
    19: 'Delivered',
    22: 'Recycled',
    23: 'Cancelled',
    24: 'On Hold',
    25: 'Problem',
    27: 'Returned',
};

// Carrier status constants
export const CARRIER_STATUS = {
    INACTIVE: 0,
    ACTIVE: 1,
    DELETED: 2,
} as const;

// Service type labels
export const SERVICE_TYPE_LABELS: Record<string, string> = {
    D: 'Domestic',
    I: 'International',
    E: 'Europe Road',
    R: 'Return',
};
