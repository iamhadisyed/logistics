export interface Carrier {
    id: number;
    carrier: string;
    carrier_display_name?: string;
    logo?: string;
    status: 0 | 1 | 2; // 0=Inactive, 1=Active, 2=Deleted
    country_id?: number;
    carrier_id?: number; // Parent carrier
    currency_code?: string;
    cut_off_time?: string;
    zone_base?: boolean;
    zone_type?: 'country' | 'postcode';
    remotearea_check?: 'c' | 's';
    is_gazetteer?: boolean;
    is_reconcile?: boolean;
    on_contract?: boolean;
    is_pallet?: boolean;

    // Relationships (when loaded)
    services?: any[];
    country?: any;
    parent_carrier?: Carrier;
    sub_carriers?: Carrier[];
}

export interface CreateCarrierPayload {
    carrier: string;
    carrier_display_name?: string;
    logo?: string;
    carrier_id?: number; // Parent carrier
    country_id?: number;
    currency_code?: string;
    cut_off_time?: string;
    zone_base?: boolean;
    zone_type?: 'country' | 'postcode';
    remotearea_check?: 'c' | 's';
    is_gazetteer?: boolean;
    is_reconcile?: boolean;
    on_contract?: boolean;
    is_pallet?: boolean;
}

export interface UpdateCarrierPayload {
    carrier?: string;
    carrier_display_name?: string;
    logo?: string;
    carrier_id?: number; // Parent carrier
    country_id?: number;
    currency_code?: string;
    cut_off_time?: string;
    zone_base?: boolean;
    zone_type?: 'country' | 'postcode';
    remotearea_check?: 'c' | 's';
    is_gazetteer?: boolean;
    is_reconcile?: boolean;
    on_contract?: boolean;
    is_pallet?: boolean;
}

export interface CarrierStatusPayload {
    status: 'active' | 'inactive' | 'delete';
}
