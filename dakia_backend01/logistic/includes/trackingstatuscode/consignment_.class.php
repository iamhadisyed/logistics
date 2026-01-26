class Consignment extends DbAccess3 implements iAddress {

  // consignment service type
    const SERVICE_DOMESTIC = "DBP";
    const SERVICE_INTERNATIONAL = "INT";
    const SERVICE_EUROPE_ROAD = "R1";
    const SERVICE_RETURN = "RTN";
    const SERVICE_ROYALMAIL_48 = "RM48";
    const SERVICE_ROYALMAIL_48_SIGN = "RM48S";
    const SERVICE_ROYALMAIL_24 = "RM24";
    const SERVICE_ROYALMAIL_24_SIGN = "RM24S";
    const ROUTING_SERVICE_ONEWORLD = "OR";
    const ROUTING_SERVICE_PARTNER = "PR";
    const ROUTING_SPECIAL_SERVICE = "S";
    const SERVICE_WEIGHT_TYPE_PARCEL = "PARCEL";
    //
    const VALUE_HIGH = "HV";
    const VALUE_LOW = "LV";
    const VALUE_MEDIUM = "MV";
    //Consignment status constant
    const STATUS_NEW = 10;
    const STATUS_INVALID = 11;
    const STATUS_READY_TO_PRINT = 12;
    const STATUS_PRINTED = 13;
    const STATUS_PRINTING = 14;
    const STATUS_RECEIVED = 15;
    const STATUS_WAREHOUSE_RECEIVED = 16;
    const STATUS_HELD = 17;
    const STATUS_DISPATCHED = 18;
    const STATUS_EXPORTING = 19;
    const STATUS_EXPORTED = 20;
    const STATUS_DELIVERED = 21;
    const STATUS_PROBLEM = 22;
    const STATUS_RECYCLED = 23;
    const STATUS_RETURNED = 24;
    const STATUS_SUPPLIER_RETURNED = 25;
    const STATUS_HOLD_RETURN = 26;
    const STATUS_SUPPLIER_READY_TO_DISPATCH = 27;
    const STATUS_RETURNED_BOOKED = 28;
    const STATUS_CANCELLED = 29;
    const STATUS_PARCEL_DEPARTURE = 30;
    const STATUS_PARCEL_LEAVING_PORT = 31;
    const STATUS_RELABEL = 32;
    const STATUS_HOLD = 33;
    const STATUS_CUSTOMER_HOLD = 34; // customer_hold
    const STATUS_DATAREADY = 35;
    const STATUS_POLAND_WAREHOUSE_RECEIVED = 36;
    const STATUS_POLAND_BOOKED = 37;
    const STATUS_DATAREADY_SUPPLIER = 38;
    const STATUS_TRACKING_DATA = 39;
    const STATUS_CLOSED = 40;
    const STATUS_AWAITING_CLAIM = 41;
    const STATUS_INTRANSIT = 42;
    const STATUS_ORDER_CREATED = 43;
    const STATUS_IN_DELIVERY = 44;
    const STATUS_RETURN = 45;
    const STATUS_PARTIAL_DELIVERED = 46;
    const TRACK_PROBLEM = 47;
    
//added new

CONST STATUS DAMAGED = 49; 
CONST STATUS CLEARANCE_PROCESSING = 50; 
CONST STATUS SHIPMENT  = 51; 
CONST STATUS Warehouse_Expecting_Parcel = 52;



public static $status_array = array(
        10 => "New",
        11 => "Invalid",
        12 => "Ready To Print",
        13 => "Printed",
        14 => "Printing",
        15 => "Label Created",
        16 => "Warehouse Reciveived",
        17 => "Held",
        18 => "Shipped",
        19 => "Exporting",
        20 => "Exported",
        21 => "Delivered",
        22 => "Discrepancy",
        23 => "Recycled",
        24 => "Returned",
        25 => "Supplier Returned",
        26 => "Hold Return",
        27 => "ready to dispatch",
        28 => "Returned Booked",
        29 => "Cancelled",
        30 => "Parcel Departure",
        31 => "Parcel Leaving Port",
        32 => "Relabel",
        33 => "Hold",
        34 => "Customer Hold",
        35 => "Ready To Dispatch",
        36 => "Poland Received",
        37 => "Poland Shipped",
        38 => "Ready To Dispatch",
        39 => "Tracking Data",
        40 => "Closed",
        41 => "Awaiting Claim",
        42 => "In Transit",
        43 => "Order Created",
        44 => "In Delivery",
        45 => "Return",
        46 => "Partial Delivered",
        47 => "Problem",
            //      48 => "Delivered"
    );
}
