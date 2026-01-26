<?php 
/*
 *  List of error code constants
 */
define("ERROR_MAINTENANCE", 1001);
define("ERROR_AUTHENTICATION_PROBLEMS", 1002);
define("CREATOR_OF_DOCUMENT", 1003);
define("ERROR_ACCOUNT_LOCKED", 1010);

define("ERROR_SETTING_SERVICE_NOT_AVAILABLE", 1050);
define("ERROR_UNKNOWN_EXCEPTION", 1051);
define("ERROR_EXCEPTION_1", 1052);
define("ERROR_EXCEPTION_2", 1053);

define("ERROR_PROCESSING_PROBLEMS", 1100);
define("ERROR_PACKAGE_INVALID_DATA", 1101);
define("ERROR_PACKAGE_UNKNOWN_PARAMETER", 1110);
define("ERROR_PACKAGE_TOO_HEAVY", 1111);


define("ERROR_PACKAGE_TOO_BIG", 1200);
define("ERROR_EXPORTED_FILE", 1201);

define("ERROR_REQUIRED_FILEDS_EMPTY", 9001);
define("ERROR_INVALID_DATA", 9002);
define("ERROR_FAILED_REQUEST", 9003);
define("ERROR_INVALID_FILE", 9004);
define("ERROR_FILE_EMPTY", 9005);
define("ERROR_COUNTRY_EMPTY", 9006);
define("ERROR_CARRIER_DUPLICATE_ENTRY", 9007);
define("ERROR_FILE_UPLOADED", 9008);
define("ERROR_INVALID_RECORD", 9009);
define("ERROR_VALUE_GREATER", 9010);
define("ERROR_NO_ACCOUNT", 9011);
define("ERROR_VALUE_MISMATCH", 9012);
define("ERROR_NO_SERVICE", 9013);
define("ERROR_NO_SERVICE_DETAIL", 9014);
define("ERROR_HAWB_EXIST", 9015);


define("ERROR_NO_RECORD", 9016);
define("ERROR_LANGUAGE_EXIST", 9017);
define("ERROR_FILE_MISMATCH", 9018);
define("ERROR_FILE_OPEN", 9019);
define("ERROR_FILE_SELECT", 9020);
define("ERROR_ACCOUNT_MISMATCH", 9021);
define("ERROR_FILE_DOWNLOAD", 9022);
define("ERROR_DELETE_AGENT", 9023);
define("ERROR_AGENTCODE_REPEATED", 9024);
define("ERROR_USER_ACTION_NOT_ALLOWED", 9025);
define("NO_WEIGHT_DISCREPENCY", 9026);
define("ERROR_WEIGHT_UPDATE", 9027);
define("ERROR_DATA_NOT_FOUND", 9028);
define("ERROR_CONTACT_CS", 9029);
define("ERROR_PARCEL_NOT_FOUND", 9030);
define("ERROR_GROUP_ASSIGNED", 9031);
define("ERROR_PASSWORD_VERIFY", 9056);
define("ERROR_SALE_RATE", 9033);
define("ERROR_PARENT_USER", 9034);
define("ERROR_SERVICE_CHARGES", 9035);
define("ERROR_URL_INVALID", 9036);
define("ERROR_SELECT_SERVICE", 9037);
define("ERROR_DELETE_CONTRACT", 9038);
define("ERROR_SERVICE_AGENT_SELECT", 9039);
define("ERROR_SERVICE_WEIGHT", 9040);
define("ERROR_FROM_WEIGHT", 9041);
define("ERROR_TO_WEIGHT", 9042);
define("ERROR_WEIGHT_RANGE", 9043);
define("ERROR_RETURN_CODE", 9044);
define("ERROR_PICKUP", 9045);
define("ERROR_DATA_EXPORT", 9046);
define("ERROR_CONTACT_FINANCE", 9047);
define("ERROR_SERVICE_CARRIER", 9048);
define("ERROR_INVALID_CARRIER", 9049);
define("ERROR_COUNTRY_NOT_AVAILABLE", 9050);
define("ERROR_INVALID_USER_DATA", 9051);
define("ERROR_FILE_REJECTED", 9052);
define("ERROR_ACCOUNT_SPACES", 9053);
define("ERROR_DUPLICATE_ACCOUNT", 9054);
define("ERROR_HAWB_EMPTY", 9055);
//define("",Please provide all required value.)
//define("SUCCESS_USER_CREATED", 9067);
/*
 *  List of success code constants
 */
define("SUCCESS_EXPORTED_FILE", 5001);
define("SUCCESS_SHIPMENT_DELETE", 5002);
define("SUCCESS_CHANGE_CARRIER_STATUS", 5003);
define("SUCCESS_CARRIER_UPDATE", 5004);
define("SUCCESS_SERVICE_REMOTE_AREA_ADDED", 5005);
define("SUCCESS_IMPORTED_RECORD", 5006);
define("MESSAGE_REMOTE_AREA", 5007);
define("SUCCESS_SERVICE_ASSIGNED", 5008);
define("SUCCESS_SERVICE_DELETE", 5009);
define("SUCCESS_RECORD_DELETED", 5010);
define("SUCCESS_TARRIF_EXPIRYDATE", 5011);
define("SUCCESS_CUSTOM_EXPORT_NUMBER", 5012);
define("SUCCESS_WEIGHT_SAVED", 5013);
define("SUCCESS_PARCEL_DETAIL_SAVED", 5014);
define("SUCCESS_STATUS_UPDATED", 5015);
define("SUCCESS_EMAIL_SENT", 5016);
define("SUCCESS_DATA_SAVED", 5017);
define("SUCCESS_ADDRESS_ADDED", 5018);
define("SUCCESS_KEY_GENERATION" , 5019);
define("SUCCESS_CONTRACT_ADDED", 5020);
define("SUCCESS_DELETE_CONTRACT", 5021);
define("SUCCESS_SERVICE_ADDED", 5022);
define("SUCCESS_COUNTRY_RANGE_ADDED", 5023);
define("SUCCESS_UPLOADED", 5024);
define("SUCCESS_USER_UPDATED", 5025);
define("SUCCESS_USER_CREATED", 5026);
define("SUCCESS_ACCOUNT_CREATED", 5027);
define("SUCCESS_DATA_UPDATED", 5028);
define("SUCCESS_ACCOUNT_DELETED", 5029);
define("SUCCESS_ROUTINE_ADDED", 5030);
define("MESSAGE_REMOTE_AREA_CHARGE", 5031);
define("ERROR_NO_USER", 9032);


define("MESSAGE_CODE" ,  array(
    ERROR_MAINTENANCE => 'ERROR_MAINTENANCE',
    ERROR_AUTHENTICATION_PROBLEMS  => 'ERROR_AUTHENTICATION_PROBLEMS',
    CREATOR_OF_DOCUMENT => "[One World Express] Irshad Ali",
    ERROR_ACCOUNT_LOCKED => 'ERROR_ACCOUNT_LOCKED',
    ERROR_SETTING_SERVICE_NOT_AVAILABLE => 'ERROR_SETTING_SERVICE_NOT_AVAILABLE',
    ERROR_UNKNOWN_EXCEPTION  => 'ERROR_UNKNOWN_EXCEPTION',
    ERROR_EXCEPTION_1  => 'ERROR_EXCEPTION_1',
    ERROR_EXCEPTION_2  => 'ERROR_EXCEPTION_2',
    ERROR_PROCESSING_PROBLEMS  => 'ERROR_PROCESSING_PROBLEMS',
    ERROR_PACKAGE_INVALID_DATA  => 'ERROR_PACKAGE_INVALID_DATA',
    ERROR_PACKAGE_UNKNOWN_PARAMETER  => 'ERROR_PACKAGE_UNKNOWN_PARAMETER',
    ERROR_PACKAGE_TOO_HEAVY  => 'ERROR_PACKAGE_TOO_HEAVY',
    ERROR_PACKAGE_TOO_BIG  => 'ERROR_PACKAGE_TOO_BIG',
    ERROR_REQUIRED_FILEDS_EMPTY   =>  "Please input all required fields.",
    ERROR_FAILED_REQUEST   =>  "Failed Request.",
    ERROR_INVALID_FILE   =>  "Invalid File.",
    ERROR_FILE_EMPTY   =>  "File not found to import data.",
    ERROR_COUNTRY_EMPTY   =>  "Please select country ",
    SUCCESS_CARRIER_UPDATE   =>  "Carrier Updated successfully",
    ERROR_CARRIER_DUPLICATE_ENTRY   =>  "Carrier already exist",
    SUCCESS_SERVICE_REMOTE_AREA_ADDED   =>  "Remote area saved successfully",
    ERROR_FILE_UPLOADED   =>  "File upload failed",
    ERROR_INVALID_RECORD   =>  "Records are invalid.",
    SUCCESS_IMPORTED_RECORD   =>  "Records imported successfully.",   
    ERROR_INVALID_DATA => "Incorrect data",    
    SUCCESS_EXPORTED_FILE => 'Data successfully exported.',
    SUCCESS_SHIPMENT_DELETE => 'Shipment successfully deleted.',
    SUCCESS_CHANGE_CARRIER_STATUS=>'Carrier status changed successfully',   
    ERROR_VALUE_GREATER => 'Value must be greater than 0 for High Value Shipments',
    ERROR_NO_ACCOUNT => 'Account does not exist.',
    ERROR_VALUE_MISMATCH => 'Item value does not match with total value.',
    MESSAGE_REMOTE_AREA => 'Remote area updated successfully',
    MESSAGE_REMOTE_AREA_CHARGE => 'is a remote area. It will be charged as remote area.',
    ERROR_NO_SERVICE => 'No service found',
    ERROR_NO_SERVICE_DETAIL => 'Service details are not available.',   
    ERROR_HAWB_EXIST => 'Order Reference already exists, please change the Order Reference',
    ERROR_HAWB_EMPTY => 'Order Reference empty, Please Fill the Order Reference and then try again',
    ERROR_NO_RECORD => 'No record found',
    ERROR_LANGUAGE_EXIST => 'Language already exists.', 
    SUCCESS_DATA_SAVED => 'Data has been saved successfully',
    SUCCESS_SERVICE_ASSIGNED => "Services assigned to the countries successfully",
    SUCCESS_SERVICE_DELETE => "Service deleted successfully",
    ERROR_FILE_OPEN => "Can't open the file",   
    ERROR_FILE_SELECT => "Please select the file",
    ERROR_ACCOUNT_MISMATCH => "Rejected. Account mismatched:", 
    ERROR_FILE_DOWNLOAD => "Unable to download file.",
    SUCCESS_RECORD_DELETED => "Record deleted successfully.",
    ERROR_DELETE_AGENT => "Unable to delete agent.",
    ERROR_AGENTCODE_REPEATED => "Please add new agent code",
    SUCCESS_TARRIF_EXPIRYDATE => "Tariff expiry date saved successfully.",
    ERROR_USER_ACTION_NOT_ALLOWED =>"User is not allowed to do this action.",
    SUCCESS_CUSTOM_EXPORT_NUMBER => "Custom export number saved successfully.",
    NO_WEIGHT_DISCREPENCY => "No weight discrepency",
    SUCCESS_WEIGHT_SAVED => "Weight saved successfully.",
    ERROR_WEIGHT_UPDATE => "There is error in updating weight.",
    SUCCESS_PARCEL_DETAIL_SAVED => "Parcel Detail Save successfully.",
    SUCCESS_STATUS_UPDATED => "Status updated successfully",
    ERROR_DATA_NOT_FOUND => "Consignment data not found",
    SUCCESS_EMAIL_SENT => "Email sent successfully.",
    ERROR_CONTACT_CS => "Error, Please contact on cs@oneworldexpress.com",
    ERROR_PARCEL_NOT_FOUND => "Parcel not found. Please contact itsupport@oneworldexpress.com",
    ERROR_GROUP_ASSIGNED => "Group has already been assigned to this carrier",
    ERROR_PASSWORD_VERIFY => "The Password must be 
                                at least one upper case english letter,
                                at least one lower case english letter,
                                at least one digit,
                                at least one special character and
                                minimum 8 in length",
    ERROR_SALE_RATE => "Sales Rate exceeding the limit. Max Sales Rate is",
    ERROR_PARENT_USER => "Please select parent user",
    ERROR_DUPLICATE_ACCOUNT => "Account Number already exist, Please choose different account number",
    ERROR_ACCOUNT_SPACES => "Account Number contain space, which is not allowed, Please remove the spaces",


    ERROR_SERVICE_CHARGES => "Charges have already been assigned to this service",
    SUCCESS_ADDRESS_ADDED => "Address added successfully.",
    SUCCESS_KEY_GENERATION => "Key and Token is generate successfully.",
    ERROR_URL_INVALID => "URL is invalid",
    SUCCESS_CONTRACT_ADDED => "Contract Added Successfully.",
    ERROR_SELECT_SERVICE => "Please select service.",
    SUCCESS_DELETE_CONTRACT => "Contract Deactivated Successfully.",
    ERROR_DELETE_CONTRACT => "Unable to delete contract.",
    SUCCESS_SERVICE_ADDED => "Service added successfully.",
    ERROR_SERVICE_AGENT_SELECT => "Please select services and agent.",
    SUCCESS_COUNTRY_RANGE_ADDED => "Country range added successfully.",
    SUCCESS_UPLOADED => "Uploaded successfully.",
    SUCCESS_ACCOUNT_CREATED => "New account has been created.",
    SUCCESS_DATA_UPDATED => "Data has been updated.",
    SUCCESS_ACCOUNT_DELETED => "Accout deleted successfully.",
    SUCCESS_USER_UPDATED => "User updated successfully.",
    SUCCESS_USER_CREATED => "User created successfully.",
    SUCCESS_ROUTINE_ADDED => "Rotuine Added Successfully.",
    ERROR_SERVICE_WEIGHT => "Maximum allowed weight for selected service is",
    ERROR_FROM_WEIGHT => "From weight must be greater than or equal to",
    ERROR_TO_WEIGHT => "To weight must be less than or equal to",
    ERROR_WEIGHT_RANGE => "Allowed weight is between",
    ERROR_RETURN_CODE => "Return Code: ",
    ERROR_PICKUP => "Pickup has not been made yet.",
    ERROR_DATA_EXPORT => "Data exported error.",
    ERROR_CONTACT_FINANCE => "If you have any query, please contact us on finance@oneworldexpress.com",
    ERROR_SERVICE_CARRIER => "Service not found for the carrier",
    ERROR_INVALID_CARRIER => "Invalid carrier.",
    ERROR_COUNTRY_NOT_AVAILABLE => "Country not available",
    ERROR_INVALID_USER_DATA => "User code is invalid, Please contact to admin ",
    ERROR_FILE_REJECTED => "File rejected, Please check your account number",
    /*ERROR_DELETE_AGENT_SERVICE_MAPPING => "Unable to delete service agent details.",
    ERROR_PAY_BALANCE =>"Sorry we are unable to change in postpaid.Please pay the payment",
    ERROR_CLEAR_INVOICE =>"Sorry we are unable to change in prepaid.Please clear the all invoices",*/
    ERROR_NO_USER => 'User does not exist.',
    ));

function formatMessages($constant,$displayCode=TRUE){
    if($displayCode)
        return $constant."-".MESSAGE_CODE[$constant];
    else
        return MESSAGE_CODE[$constant];
}