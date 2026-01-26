export interface AgItem {
    id?: number;
    description: string;
    quantity: number;
    weight: number;
    value: number;
}

export interface AgParcel {
    id?: number;
    weight: number;
    length: number;
    width: number;
    height: number;
    notes?: string;
    items: AgItem[];
}

export interface AgConsignmentHistory {
    id: number;
    status: string;
    notes: string;
    created_at: string;
    changed_by?: number;
}

export interface AgConsignment {
    id: number;
    uuid: string;
    customer_id: number;
    service_type: string;
    warehouse_id?: number;
    reference: string;
    notes?: string;
    status: 'draft' | 'booked' | 'label_generated';
    label_generated: boolean;
    label_generated_at?: string;
    label_url?: string;
    created_at: string;
    updated_at: string;
    parcels?: AgParcel[];
    history?: AgConsignmentHistory[];
    // Receiver Address
    company?: string;
    contact?: string;
    email?: string;
    telephone?: string;
    address_line_1?: string;
    address_line_2?: string;
    address_line_3?: string;
    city?: string;
    state?: string;
    postcode?: string;
    country_id?: number;
    // Sender Address (optional)
    sender_company?: string;
    sender_contact?: string;
    sender_email?: string;
    sender_telephone?: string;
    sender_address_line_1?: string;
    sender_address_line_2?: string;
    sender_address_line_3?: string;
    sender_city?: string;
    sender_state?: string;
    sender_postcode?: string;
    sender_country_id?: number;
}

export interface CreateAgConsignmentPayload {
    consignment: {
        customer_id: number;
        service_type: string;
        warehouse_id?: number;
        reference: string;
        notes?: string;
    };
    parcels: Omit<AgParcel, 'id'>[];
}
