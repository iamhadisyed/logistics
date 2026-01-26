<?php
require_once(__DIR__ . "/../../includes/settings/config.inc.php");
include_classes([
    'carrierservice.class',
    ],'general');
include_classes([
    'royalmail.class',
    'royalmailtrackingstatus.class'
    ],'labels');
include_classes([
    'nusoap',
    ],'nusoap');


include_classes([
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'parcel.class',
    'parcelfilter.class',
    'services.class',
    'servicefilter.class',
    'agentdata.class',
    'agentdatafilter.class',
    'serviceconstantvalue.class',
    'serviceconstantvaluefilter.class',
    'tracking.class',
    'trackingdata.class',
    'trackingdatafilter.class',
    
    ]);

include_classes([
    'SFTP'
],'3rdparty/Net');


$StatusCodes = array('EVAIE' => array("EVAIE", "Pre-Advice", "Pre-advice Item Passed - Received with errors", "We're expecting it", "The sender has let us know this item will be with us soon."),
    'EVAIP' => array("EVAIP", "Pre-Advice", "Pre-advice Item Passed - Validation Passed", "We're expecting it", "The sender has let us know this item will be with us soon."),
    'EVAPA' => array("EVAPA", "Pre-Advice", "Pre-advice", "We're expecting it", "The sender has let us know this item will be with us soon."),
    'EVBAH' => array("EVBAH", "Outward RDC", "Outward RDC - Handheld Acceptance", "We've got it", "We have your item at 'EventLocationName' and its on its way. More information will be available as it travels through our network."),
    'EVBAV' => array("EVBAV", "Outward RDC", "Outward RDC - Volumetric Acceptance", "We've got it", "We have your item and it's on its way. More information will be available as it travels through our network."),
    'EVBAW' => array("EVBAW", "Outward RDC", "Rejected from machine overweight for machine", "We've got it", ""),
    'EVBDM' => array("EVBDM", "Outward RDC", "Outward RDC - Damaged Scan", "We've got it", "Item Received"),
    'EVBMI' => array("EVBMI", "Outward RDC", "Outward RDC Mis-sort Scan", "We've got it", "Sorry, your item went to 'EventLocationName' in error, so we re-routed it immediately. More information will be available as it travels through our network."),
    'EVBOF' => array("EVBOF", "Outward RDC", "Rejected From machine", "We've got it", ""),
    'EVBPM' => array("EVBPM", "Outward RDC", "Outward RDC Primary Mixed - Handheld Acceptance", "We've got it", "We have your item at 'EventLocationName' and it's on its way. More information will be available as it travels through our network."),
    'EVBPR' => array("EVBPR", "Outward RDC", "Outward RDC Primary Regular - Handheld Acceptance", "We've got it", "We have your item at 'EventLocationName' and it's on its way. More information will be available as it travels through our network."),
    'EVBRA' => array("EVBRA", "Outward RDC", "Outward RDC - Rejected RTS - Address Incomplete", "Returned to Sender", "Incorrectly addressed. Returned to sender."),
    'EVBRB' => array("EVBRB", "Outward RDC", "Outward RDC - Rejected Barcode", "Returned to Sender", "Sorry, we were unable to process this item at 'EventLocationName' due to a problem with the barcode on the label. We're returning it to the sender."),
    'EVBRS' => array("EVBRS", "Outward RDC", "Outward RDC - Rejected Oversize", "We've got it", "Item Received"),
    'EVBRT' => array("EVBRT", "Outward RDC", "Outward RDC - Returned to Sender", "Returned to Sender", "This item will now be returned to the sender."),
    'EVBRW' => array("EVBRW", "Outward RDC", "Outward RDC - Rejected Overweight", "Returned to Sender", "Item Received"),
    'EVBSA' => array("EVBSA", "Outward RDC", "Outward RDC - RTS Address Incomplete Handheld", "Returned to Sender", "Sorry, we were unable to process this item at 'EventLocationName' as it wasn't correctly addressed. We're returning it to the sender.                  "),
    'EVBSD' => array("EVBSD", "Outward RDC", "Outward RDC - Damaged Handheld", "We've got it", "We have your item at 'EventLocationName' and it's on its way. More information will be available as it travels through our network."),
    'EVBSM' => array("EVBSM", "Outward RDC", "Outward RDC Secondary Mixed - Handheld Acceptance", "We've got it", "We have your item at 'EventLocationName' and it's on its way. More information will be available as it travels through our network."),
    'EVBSR' => array("EVBSR", "Outward RDC", "Outward RDC Secondary Regular - Handheld Acceptance", "We've got it", "We have your item at 'EventLocationName' and it's on its way. More information will be available as it travels through our network."),
    'EVBSS' => array("EVBSS", "Outward RDC", "Outward RDC - Rejected Oversize", "We've got it", "We have your item at 'EventLocationName' and it's on its way. More information will be available as it travels through our network."),
    'EVBSW' => array("EVBSW", "Outward RDC", "Outward RDC - Rejected Overweight", "We've got it", "We have your item at 'EventLocationName' and it's on its way. More information will be available as it travels through our network."),
    'EVCAH' => array("EVCAH", "Inward RDC", "Inward RDC - Handheld Acceptance", "In Transit", "Your item has reached 'EventLocationName' and will now be sent to your local delivery office."),
    'EVCAV' => array("EVCAV", "Inward RDC", "Inward RDC - Volumetric Acceptance", "In Transit", "Your item has reached 'EventLocationName' and will now be sent to your local delivery office."),
    'EVCDM' => array("EVCDM", "Inward RDC", "Inward RDC - Damaged Scan", "In Transit", "Item Received"),
    'EVCMI' => array("EVCMI", "Inward RDC", "Mis-sort Scan: Inward RDC Handheld", "In Transit", "Item Received"),
    'EVCOF' => array("EVCOF", "Inward RDC", "Rejected From machine", "In Transit", ""),
    'EVCPA' => array("EVCPA", "Inward RDC", "Inward RDC - Rejected RTS Address Incomplete", "Returned to Sender", "Sorry, we were unable to process this item at 'EventLocationName' as it wasn't correctly addressed. We're returning it to the sender.                   "),
    'EVCPD' => array("EVCPD", "Inward RDC", "Inward RDC - Damaged Scan", "In Transit", "Your item has reached 'EventLocationName' and will now be sent to your local delivery office."),
    'EVCPI' => array("EVCPI", "Inward RDC", "Mis-sort Scan: Inward RDC Handheld", "In Transit", "Sorry, your item went to 'EventLocationName' in error, so we re-routed it immediately. More information will be available as it travels through our network."),
    'EVCPS' => array("EVCPS", "Inward RDC", "Inward RDC - Rejected Oversize", "In Transit", "Your item has reached 'EventLocationName' and will now be sent to your local delivery office."),
    'EVCPW' => array("EVCPW", "Inward RDC", "Inward RDC - Rejected Overweight", "In Transit", "Your item has reached 'EventLocationName' and will now be sent to your local delivery office."),
    'EVCRA' => array("EVCRA", "Inward RDC", "Inward RDC - Rejected RTS Address Incomplete", "Returned to Sender", "Incorrectly addressed. Returned to sender."),
    'EVCRB' => array("EVCRB", "Inward RDC", "Inward RDC - Rejected Barcode", "Returned to Sender", "Sorry, we were unable to process this item at 'EventLocationName' due to a problem with the barcode on the label. We're returning it to the sender."),
    'EVCRS' => array("EVCRS", "Inward RDC", "Inward RDC - Rejected Oversize", "In Transit", "Item Received"),
    'EVCRW' => array("EVCRW", "Inward RDC", "Inward RDC - Rejected Overweight", "In Transit", "Item Received"),
    'EVDAA' => array("EVDAA", "Outward MC", "Rejected from machine not in sort plan", "We've got it", "We have your item at 'EventLocationName' and it's on its way. More information will be available as it travels through our network."),
    'EVDAC' => array("EVDAC", "Outward MC", "Outward MC - Handheld Acceptance", "We've got it", "We have your item at 'EventLocationName' and it's on its way. More information will be available as it travels through our network."),
    'EVDAV' => array("EVDAV", "Outward MC", "Outward Mail Centre Automation Acceptance", "We've got it", "We have your item at 'EventLocationName' and it's on its way. More information will be available as it travels through our network."),
    'EVDAW' => array("EVDAW", "Outward MC", "Rejected from machine overweight for machine", "We've got it", "We have your item at 'EventLocationName' and it's on its way. More information will be available as it travels through our network."),
    'EVDDM' => array("EVDDM", "Outward MC", "Outward MC - Damaged Handheld", "We've got it", "Item Received"),
    'EVDLS' => array("EVDLS", "Outward MC", "Item lost by  machine", "We've got it", ""),
    'EVDMI' => array("EVDMI", "Outward MC", "Outward MC Mis-sort Scan", "In Transit", "Sorry, your item went to 'EventLocationName' in error, so we re-routed it immediately. More information will be available as it travels through our network."),
    'EVDOF' => array("EVDOF", "Outward MC", "Rejected From machine", "We've got it", "We have your item at 'EventLocationName' and it's on its way. More information will be available as it travels through our network."),
    'EVDPL' => array("EVDPL", "Outward MC", "Outward MC Primary Large - Handheld Acceptance", "We've got it", "We have your item at 'EventLocationName' and it's on its way. More information will be available as it travels through our network."),
    'EVDPM' => array("EVDPM", "Outward MC", "Outward MC Primary Mixed - Handheld Acceptance", "We've got it", "We have your item at 'EventLocationName' and it's on its way. More information will be available as it travels through our network."),
    'EVDPR' => array("EVDPR", "Outward MC", "Outward MC Primary Regular - Handheld Acceptance", "We've got it", "We have your item at 'EventLocationName' and it's on its way. More information will be available as it travels through our network."),
    'EVDRA' => array("EVDRA", "Outward MC", "Outward MC - RTS Address Incomplete Handheld", "Returned to Sender", "Sorry, we were unable to process this item at 'EventLocationName' as it wasn't correctly addressed. We're returning it to the sender."),
    'EVDRB' => array("EVDRB", "Outward MC", "Outward MC - Rejected Barcode", "Returned to Sender", "Sorry, we were unable to process this item at 'EventLocationName' due to a problem with the barcode on the label. We're returning it to the sender."),
    'EVDRO' => array("EVDRO", "Outward MC", "Rejected from machine oversize for machine", "We've got it", "We have your item at 'EventLocationName' and it's on its way. More information will be available as it travels through our network."),
    'EVDRS' => array("EVDRS", "Outward MC", "Outward MC - Oversize Handheld", "We've got it", "We have your item at 'EventLocationName' and it's on its way. More information will be available as it travels through our network."),
    'EVDRW' => array("EVDRW", "Outward MC", "Outward MC - Overweight Handheld", "Returned to Sender", "Sorry, we were unable to process this item at 'EventLocationName' due to the item being overweight. It will be returned to the sender.                   "),
    'EVDSA' => array("EVDSA", "Outward MC", "Outward MC - RTS Address Incomplete Handheld", "Returned to Sender", "Sorry, we were unable to process this item at 'EventLocationName', as it wasn't correctly addressed. We're returning it to the sender.             "),
    'EVDSD' => array("EVDSD", "Outward MC", "Outward MC - Damaged Handheld", "We've got it", "We have your item at 'EventLocationName' and it's on its way. More information will be available as it travels through our network."),
    'EVDSL' => array("EVDSL", "Outward MC", "Outward MC Secondary Large - Handheld Acceptance", "We've got it", "We have your item at 'EventLocationName' and it's on its way. More information will be available as it travels through our network."),
    'EVDSM' => array("EVDSM", "Outward MC", "Outward MC Secondary Mixed - Handheld Acceptance", "We've got it", "We have your item at 'EventLocationName' and it's on its way. More information will be available as it travels through our network."),
    'EVDSR' => array("EVDSR", "Outward MC", "Outward MC Secondary Regular - Handheld Acceptance", "We've got it", "We have your item at 'EventLocationName' and it's on its way. More information will be available as it travels through our network."),
    'EVDSS' => array("EVDSS", "Outward MC", "Outward MC - Oversize Handheld", "We've got it", "We have your item at 'EventLocationName' and it's on its way. More information will be available as it travels through our network."),
    'EVDSW' => array("EVDSW", "Outward MC", "Outward MC - Overweight Handheld", "Returned to Sender", "Sorry, we were unable to process this item at 'EventLocationName' due to the item being overweight. It will be returned to the sender.                   "),
    'EVGDM' => array("EVGDM", "Delivery Office", "Indoor Sorting - Damaged", "In Transit", ""),
    'EVGDO' => array("EVGDO", "Inward MC/Delivery Office", "Delivery Office - Item received in a bag from another Delivery Office", "In Transit", "Arrived at (scan location shows)"),
    'EVGMI' => array("EVGMI", "Delivery Office", "Indoor Sorting - Mis-sort - Redirected", "In Transit", "Sorry, your item went to 'EventLocationName' in error, so we re-routed it immediately. More information will be available as it travels through our network."),
    'EVGPD' => array("EVGPD", "Delivery Office", "Indoor Sorting - Ready For Delivery", "Ready for Delivery", "Your Item was received by 'EventLocationName' on 'EventDateTime' and is now due for delivery today."),
    'EVHAC' => array("EVHAC", "HWDC", "Outward Collection", "In Transit", "Item Received"),
    'EVHAP' => array("EVHAP", "HWDC", "Export Cancellation due to Awaiting Payment of Charges", "Pending", "This item cannot be exported as it is awaiting the payment of necessary customs charges."),
    'EVHAR' => array("EVHAR", "HWDC", "Export Cancellation At Addressee's Request", "Pending", "This item will not be exported due to the addressee's request."),
    'EVHCA' => array("EVHCA", "HWDC", "Export Cancellation due to Incorrect / Illegible / Incomplete Address", "Returned to Sender", "Sorry, we're unable to export this item. See below for further information."),
    'EVHCR' => array("EVHCR", "HWDC", "Export Cancellation due to Does Not Meet Customs Requirements", "Returned to Sender", "Sorry, we're unable to export this item. See below for further information."),
    'EVHDI' => array("EVHDI", "HWDC", "Export Cancellation due to Item Damaged and / or Missing Contents", "Returned to Sender", "Sorry, we're unable to export this item. See below for further information."),
    'EVHFM' => array("EVHFM", "HWDC", "Export Cancellation due to Force Majeure / Exceptional Event", "Pending", "Sorry, we're unable to export this item at this time. See below for further information."),
    'EVHID' => array("EVHID", "HWDC", "Export Cancellation due to Insufficient / Incomplete / Incorrect Documentation", "Returned to Sender", "Sorry, we're unable to export this item. See below for further information."),
    'EVHNC' => array("EVHNC", "HWDC", "Export Cancellation due to Impossible to Contact Customer for Information", "Pending", "Sorry, we're unable to export this item as we're unable to obtain the required information from the sender."),
    'EVHOE' => array("EVHOE", "HWDC", "Departure from Outward Office of Exchange", "In Transit", "Item Leaving the UK"),
    'EVHSR' => array("EVHSR", "HWDC", "Export Cancellation At Sender's Request", "Pending", "This item will not be exported due to the sender's request."),
    'EVIAV' => array("EVIAV", "Inward MC", "Inward MC - Automation Acceptance", "In Transit", "Your item has reached 'EventLocationName' and will now be sent to your local delivery office."),
    'EVIDM' => array("EVIDM", "Inward MC", "Inward MC - Damaged Handheld", "In Transit", "Item Received"),
    'EVILC' => array("EVILC", "ILC", "Item despatched from Heathrow ILC  to RMG Location", "In Transit", "Item has left the overseas International Processing Centre"),
    'EVIMC' => array("EVIMC", "Inward MC", "Inward MC - Handheld Acceptance", "In Transit", "Your item has reached 'EventLocationName' and will now be sent to your local delivery office."),
    'EVIMI' => array("EVIMI", "Inward MC", "Mis-sort: Inward MC - Handheld", "In Transit", "Item Received"),
    'EVIMS' => array("EVIMS", "Inward MC", "Rejected from machine not in sort plan", "In Transit", "Your item has reached 'EventLocationName' and will now be sent to your local delivery office."),
    'EVIOF' => array("EVIOF", "Inward MC", "Rejected from machine", "In Transit", "Your item has reached 'EventLocationName' and will now be sent to your local delivery office."),
    'EVIPA' => array("EVIPA", "Inward MC", "Rejected Address Incomplete at Inward Mail Centre", "Returned to Sender", "Sorry, we were unable to process this item at 'EventLocationName' as it wasn't correctly addressed. We're returning it to the sender.                 "),
    'EVIPD' => array("EVIPD", "Inward MC", "Inward MC - Damaged Handheld", "In Transit", "Your item has reached 'EventLocationName' and will now be sent to your local delivery office."),
    'EVIPI' => array("EVIPI", "Inward MC", "Mis-sort: Inward MC - Handheld", "In Transit", "Sorry, your item went to 'EventLocationName' in error, so we re-routed it immediately. More information will be available as it travels through our network."),
    'EVIPR' => array("EVIPR", "Inward MC", "Inward MC Primary Regular - Handheld Acceptance", "In Transit", "Your item has reached 'EventLocationName' and will now be sent to your local delivery office."),
    'EVIPS' => array("EVIPS", "Inward MC", "Inward MC - Oversize Handheld", "In Transit", "Your item has reached 'EventLocationName' and will now be sent to your local delivery office."),
    'EVIPW' => array("EVIPW", "Inward MC", "Inward MC - Overweight Handheld", "In Transit", "Your item has reached 'EventLocationName' and will now be sent to your local delivery office."),
    'EVIRM' => array("EVIRM", "Inward MC", "Rejected from machine overweight for machine", "In Transit", "Your item has reached 'EventLocationName' and will now be sent to your local delivery office."),
    'EVIRO' => array("EVIRO", "Inward MC", "Rejected from machine oversize for machine", "In Transit", "Your item has reached 'EventLocationName' and will now be sent to your local delivery office."),
    'EVJAP' => array("EVJAP", "ILC", "Held by Import Customs due to Awaiting Presentation to Border Agency / Security", "Pending", "This item is still being assessed by the destination customs authority.  If any further information is required before release they will contact the recipient directly"),
    'EVJBA' => array("EVJBA", "ILC", "Held by Import Customs due to Retained by Border Agency / Security for Unspecified Reason", "Pending", "This item is still being assessed by the destination customs authority.  If any further information is required before release they will contact the recipient directly"),
    'EVJCA' => array("EVJCA", "ILC", "Held by Import Customs due to Handed Over to Customs Authority for Final Delivery", "Pending", "This item is still being assessed by the destination customs authority.  If any further information is required before release they will contact the recipient directly"),
    'EVJCC' => array("EVJCC", "ILC", "Held by Import Customs due to Impossible to Contact Customer for Information", "Pending", "This item is still being assessed by the destination customs authority.  If any further information is required before release they will contact the recipient directly"),
    'EVJCD' => array("EVJCD", "ILC", "Held by Import Customs due to Customs Declaration Missing / Inappropriate", "Pending", "This item is still being assessed by the destination customs authority.  If any further information is required before release they will contact the recipient directly"),
    'EVJCO' => array("EVJCO", "ILC", "Held by Import Customs - due to Certificate of Origin Missing / Inappropriate", "Pending", "This item is still being assessed by the destination customs authority.  If any further information is required before release they will contact the recipient directly"),
    'EVJDP' => array("EVJDP", "ILC", "Returned from Import Customs due to Import Authorised; Taxes and / or Duties to be Paid", "Released from Customs", "Your item has been released from Customs."),
    'EVJDU' => array("EVJDU", "ILC", "Departure from Inward Office of Exchange", "In Transit", "Your item has left the overseas International Logistics Centre and is on its way. More information will be available as it travels through the network."),
    'EVJEA' => array("EVJEA", "ILC", "Returned from Import Customs due to Export Authorised", "Released from Customs", "Your item has been released from Customs. More information will be available as it travels through the network"),
    'EVJEM' => array("EVJEM", "ILC", "Held by Import Customs due to Invoice Missing / Inappropriate", "Pending", "This item is still being assessed by the destination customs authority.  If any further information is required before release they will contact the recipient directly"),
    'EVJES' => array("EVJES", "ILC", "Held by Import Customs due to Incomplete Shipment", "Pending", "This item is still being assessed by the destination customs authority.  If any further information is required before release they will contact the recipient directly"),
    'EVJFC' => array("EVJFC", "ILC", "Returned from Import Customs due to Import Authorised only for Forwarding for Clearance in a Remote Customs Office", "Released from Customs", "Your item has been released from Customs."),
    'EVJIA' => array("EVJIA", "ILC", "Returned from Import Customs due to Import Authorised", "Released from Customs", "Your item has been released from Customs."),
    'EVJIC' => array("EVJIC", "ILC", "Presented to Import Customs", "In Customs", "Your item has been presented to Customs on 'EventDateTime' for assessment. More information will be available once the assessment is complete."),
    'EVJIN' => array("EVJIN", "ILC", "Held by Import Customs due to Insufficient / Incomplete / Incorrect Documentation; Awaiting Additional Documentation", "Pending", "This item is still being assessed by the destination customs authority.  If any further information is required before release they will contact the recipient directly"),
    'EVJJV' => array("EVJJV", "ILC", "Departure from Inward Office of Exchange - High Value Item", "In Transit", "Your item has left the overseas International Logistics Centre and is on its way. More information will be available as it travels through the network."),
    'EVJLV' => array("EVJLV", "ILC", "Departure from Inward Office of Exchange - Low Value Item", "#REF!", "#REF!"),
    'EVJML' => array("EVJML", "ILC", "Held by Import Customs due to Articles whose Importation is subject to Restrictions - Import Licence Missing / Inapropriate", "Pending", "This item is still being assessed by the destination customs authority.  If any further information is required before release they will contact the recipient directly"),
    'EVJMV' => array("EVJMV", "ILC", "Departure from Inward Office of Exchange - Medium Value Item", "In Transit", "Your item has left the overseas International Logistics Centre and is on its way. More information will be available as it travels through the network."),
    'EVJNT' => array("EVJNT", "ILC", "Returned from Import Customs due to Import Authorised; No Taxes or Duties to be Paid", "Released from Customs", "Your item has been released from Customs."),
    'EVJOE' => array("EVJOE", "ILC", "Arrival at Inward Office of Exchange", "We've got it", "Item Received"),
    'EVJPA' => array("EVJPA", "ILC", "Held by Import Customs due to Prohibited Articles", "Pending", "This item is still being assessed by the destination customs authority.  If any further information is required before release they will contact the recipient directly"),
    'EVJSR' => array("EVJSR", "ILC", "Held by Import Customs due to Articles whose Exportation is Subject to Restrictions - Export License Missing / Inappropriate", "Pending", "This item is still being assessed by the destination customs authority.  If any further information is required before release they will contact the recipient directly"),
    'EVKAA' => array("EVKAA", "Outdoor", "Address Inaccessible", "Delivery Attempted", "Delivery Attempted - Address Inaccessible"),
    'EVKAI' => array("EVKAI", "Outdoor", "Address Incomplete/Incorrect", "Returned to Sender", "Incorrectly addressed. Returned to sender."),
    'EVKDN' => array("EVKDN", "Outdoor", "Delivered to Neighbour - No Signature", "Delivered", "We delivered your item to a neighbour at 'Neighbour address' on 'EventDateTime'."),
    'EVKGA' => array("EVKGA", "Outdoor", "Gone Away", "Returned to Sender", "Recipient not at address. Returned to sender."),
    'EVKLC' => array("EVKLC", "Outdoor", "Local Collect - No Signature (Delivered to POL)", "Delivered", "Your item has been delivered to your nominated collection point at 'EventLocationName' at 'EventDateTime' and it's now ready for you to collect."),
    'EVKLS' => array("EVKLS", "Outdoor", "Local Collect - Signature", "Delivered", "Your item has been delivered to your nominated collection point at 'EventLocationName' at 'EventDateTime' and it's now ready for you to collect."),
    'EVKNA' => array("EVKNA", "Outdoor", "No Answer", "Delivery Attempted", "Delivery Attempted - No Answer"),
    'EVKNS' => array("EVKNS", "Outdoor", "Delivered to Neighbour - Signature", "Delivered", "We delivered your item to a neighbour at 'Neighbour address' on 'EventDateTime'."),
    'EVKOP' => array("EVKOP", "Inward MC", "Delivered - No Signature", "Delivered", "Your item was delivered on 'EventDateTime'."),
    'EVKRF' => array("EVKRF", "Outdoor", "Refused", "Returned to Sender", "Refused - Returned to Sender"),
    'EVKSF' => array("EVKSF", "Outdoor", "Safeplace Delivered", "Delivered", "Your item was delivered to your requested Safeplace on 'EventDateTime'."),
    'EVKSP' => array("EVKSP", "Outdoor", "Delivered - Signature", "Delivered", "Delivered and Signed"),
    'EVKSU' => array("EVKSU", "Outdoor", "Unable to deliver to Safeplace", "Delivery Attempted", "Sorry, we've been unable to deliver your item to your nominated Safeplace today, 'EventDateTime'. Please choose an option below"),
    'EVNAA' => array("EVNAA", "Customer Service Point", "Available For Collection - Address Inaccessible", "Pending", "Sorry, we were unable to deliver this item at 'EventDateTime' as the address was inaccessible. We will attempt to deliver your item again on the next working day."),
    'EVNAB' => array("EVNAB", "Customer Service Point", "Marked for return - refused", "Returned to Sender", "Refused - Returned to Sender"),
    'EVNAC' => array("EVNAC", "Customer Service Point", "Marked for return - gone away", "Returned to Sender", "Recipient not at address. Returned to sender."),
    'EVNAD' => array("EVNAD", "Customer Service Point", "Marked for return - retention exceeded", "Returned to Sender", "Holding period exceeded - Returned to Sender"),
    'EVNAE' => array("EVNAE", "Customer Service Point", "Allocated to Customer Service Point", "Pending", "Available for Collection or Redelivery"),
    'EVNAI' => array("EVNAI", "Customer Service Point", "Return To Sender - Address Incomplete", "Returned to Sender", "Sorry, we were unable to deliver this item at 'EventDateTime' as it was not possible to identify the delivery address. It will now be returned to the sender."),
    'EVNBA' => array("EVNBA", "Customer Service Point", "Delivery attempted late - no answer recorded at delivery office", "Delivery Attempted", "Delivery Attempted - No Answer"),
    'EVNCE' => array("EVNCE", "Customer Service Point", "Item Collected - from SPS site using E739", "Collected", "Your item was collected from EventLocationName at EventDateTime."),
    'EVNCF' => array("EVNCF", "Delivery Office", "Hold with fee to pay", "Pending", "We're holding your item at  'EventLocationName' as there is a fee to pay. We've  left a card asking  for payment as either not enough postage was paid by the sender, or there is a customs charge due for the item."),
    'EVNCL' => array("EVNCL", "Customer Service Point", "Item not located", "Not Applicable", ""),
    'EVNCO' => array("EVNCO", "Customer Service Point", "Item Collected - from SPS site", "Collected", "Your item was collected from 'EventLocationName' at 'EventDateTime'."),
    'EVNDA' => array("EVNDA", "Customer Service Point", "Available For Collection - No Answer", "Pending", "Sorry, we tried to deliver your parcel on 'EventDateTime' but there didn't seem to be anyone in. Please choose an option below."),
    'EVNDL' => array("EVNDL", "Customer Service Point", "Item destroyed locally", "Not Applicable", ""),
    'EVNDN' => array("EVNDN", "Customer Service Point", "Delivery Not Attempted", "Pending", "Sorry, we were unable to delivery this item on 'EventDateTime'. We'll attempt to deliver it on the next working day. See below for more information."),
    'EVNDO' => array("EVNDO", "Delivery Office", "Forwarded to Delivery Office", "In Transit", ""),
    'EVNDS' => array("EVNDS", "Customer Service Point", "Item deleted in SPS", "Not Applicable", ""),
    'EVNFS' => array("EVNFS", "Customer Service Point", "Delivery Recorded at the Delivery Office", "Delivered", "Your item was delivered on 'EventDateTime'."),
    'EVNGA' => array("EVNGA", "Customer Service Point", "Return To Sender - Gone Away", "Returned to Sender", "Recipient not at address. Returned to sender."),
    'EVNGL' => array("EVNGL", "Customer Service Point", "Delivery attempted late - gone away recorded at delivery office", "Returned to Sender", "Recipient not at address. Returned to sender."),
    'EVNHE' => array("EVNHE", "Customer Service Point", "Returned to HWDC - retention exceeded", "Returned to Sender", "This item is being returned to the sender because it's exceeded the holding period at 'EventLocationName'."),
    'EVNHR' => array("EVNHR", "Customer Service Point", "Returned to HWDC - refused", "Returned to Sender", "Sorry, we were unable to deliver this item at 'EventDateTime' as the recipient refused to accept it. It will now be returned to the sender."),
    'EVNIL' => array("EVNIL", "Customer Service Point", "Delivery attempted late - address inaccessible recorded at delivery office", "Delivery Attempted", "Delivery Attempted - Address Inaccessible"),
    'EVNIO' => array("EVNIO", "Customer Service Point", "Delivery attempted on time - address inaccessible recorded at delivery office", "Delivery Attempted", "Delivery Attempted - Address Inaccessible"),
    'EVNKS' => array("EVNKS", "Customer Service Point", "Available For Collection - Keepsafe", "Pending", "As requested, we're holding this item at 'EventLocationName' as part of our Keepsafe service. See below for more information."),
    'EVNLA' => array("EVNLA", "Customer Service Point", "Return to NRC - Address Incomplete", "Returned to Sender", "Address Incomplete - Forwarded to National Returns Centre"),
    'EVNLC' => array("EVNLC", "Customer Service Point", "Available For Collection - Local Collect", "Delivered", "This item was delivered to our Customer Service Point at 'EventLocationName' on 'EventDateTime' and is ready for collection."),
    'EVNLG' => array("EVNLG", "Customer Service Point", "Return to NRC - Gone Away", "#REF!", "Recipient not at address. Returned to sender."),
    'EVNLI' => array("EVNLI", "Customer Service Point", "Delivery attempted late - incomplete address recorded at delivery office", "Returned to Sender", "Incorrectly addressed. Returned to sender."),
    'EVNLP' => array("EVNLP", "Customer Service Point", "Item Delivered - RECORDED LATE AT DELIVERY OFFICE", "Delivered", "Your item was delivered on 'EventDateTime'."),
    'EVNLR' => array("EVNLR", "Customer Service Point", "Return to NRC - Refused", "Returned to Sender", "Refused - Forwarded to National Returns Centre"),
    'EVNMC' => array("EVNMC", "Delivery Office", "Forwarded to Mail Centre", "In Transit", ""),
    'EVNMI' => array("EVNMI", "Customer Service Point", "Forwarded - Mis-sort", "In Transit", "Sorry, your item went to 'EventLocationName' in error, so we re-routed it immediately. More information will be available as it travels through our network."),
    'EVNOC' => array("EVNOC", "Customer Service Point", "Item Collected", "Collected", "Collected from Royal Mail Customer Service Point"),
    'EVNOI' => array("EVNOI", "System generated", "Implied delivery", "Not Applicable", "We have a record of item 'Barcode' as being delivered from 'EventLocationName' on 'EventDateTime'."),
    'EVNOU' => array("EVNOU", "Customer Service Point", "Booked Out", "Not Applicable", ""),
    'EVNPB' => array("EVNPB", "Customer Service Point", "Accepted by Royal Mail at Customer Service Point", "We've got it", "Your item was posted at the Royal Mail Customer Service Point at 'EventLocationName' on 'EventDateTime'. More information will be available as it travels through our network."),
    'EVNPO' => array("EVNPO", "Delivery Office", "Delivered to PO Box", "Delivered", "Delivered to PO box"),
    'EVNRC' => array("EVNRC", "Customer Service Point", "Selected for redelivery to Local Collect", "0", "We have received an instruction for this item to be sent to the nominated Post Office&reg;. See below for more information."),
    'EVNRD' => array("EVNRD", "Delivery Office", "Item redirected", "Pending", "Redirection in place - item forwarded"),
    'EVNRE' => array("EVNRE", "Customer Service Point", "Return To Sender - Retention Exceeded", "Returned to Sender", "Retention exceeded - Returned to Sender"),
    'EVNRF' => array("EVNRF", "Customer Service Point", "Return To Sender - Refused", "Returned to Sender", "Sorry, we were unable to deliver this item at 'EventDateTime' as the recipient refused to accept it. It will now be returned to the sender."),
    'EVNRL' => array("EVNRL", "Customer Service Point", "Return to NRC - Retention Exceeded", "Returned to Sender", "This item is being forwarded to our National Returns Centre to determine the sender's details because it's exceeded the holding period at 'EventLocationName'."),
    'EVNRN' => array("EVNRN", "Customer Service Point", "Returned to NRC", "Pending", "Returned to National Returns Centre"),
    'EVNRO' => array("EVNRO", "Customer Service Point", "Delivery attempted on time - refused recorded at delivery office", "Returned to Sender", "Refused - Returned to Sender"),
    'EVNRR' => array("EVNRR", "Customer Service Point", "Delivery attempted late - refused recorded at delivery office", "Returned to Sender", "Refused - Returned to Sender"),
    'EVNRS' => array("EVNRS", "Customer Service Point", "Returned to Sender", "Returned to Sender", ""),
    'EVNRT' => array("EVNRT", "Delivery Office", "Held for Retention", "Pending", "Item Retention"),
    'EVNSA' => array("EVNSA", "Customer Service Point", "Delivery attempted on time- no answer recorded at delivery office", "Delivery Attempted", "Delivery Attempted - No Answer"),
    'EVNSC' => array("EVNSC", "Customer Service Point", "Item Collected - Signature", "Collected", "Your item was collected from 'EventLocationName' at 'EventDateTime'."),
    'EVNSI' => array("EVNSI", "Customer Service Point", "Delivery attempted on time - incomplete address recorded at delivery office", "Returned to Sender", "Incorrectly addressed. Returned to sender."),
    'EVNSR' => array("EVNSR", "Customer Service Point", "Selected For Redelivery", "Pending", "We've received a Redelivery request for this item. See below for more information."),
    'EVNUK' => array("EVNUK", "Customer Service Point", "Marked for return - addressee unknown", "Returned to Sender", "Unknown recipient. Returned to sender."),
    'EVNUL' => array("EVNUL", "Customer Service Point", "Delivery attempted late - unknown addressee recorded at delivery office", "Delivery Attempted", "Unknown recipient. Returned to sender."),
    'EVNUO' => array("EVNUO", "Customer Service Point", "Delivery attempted on time - unknown addressee recorded at delivery office", "Delivery Attempted", "Unknown recipient. Returned to sender."),
    'EVNWA' => array("EVNWA", "Customer Service Point", "Forwarded - Wrong Address", "In Transit", "Sorry, your item was incorrectly addressed. We've corrected that and we're forwarding it to the right address."),
    'EVOAC' => array("EVOAC", "Outward MC", "Item Acceptance", "We've got it", "Item received at (scan location shows)"),
    'EVOBC' => array("EVOBC", "Outward MC", "Acceptance from bulk receipt", "We've got it", "Item received at (scan location shows)"),
    'EVODO' => array("EVODO", "Outward MC/Inward MC", "Item despatched (Inferred)", "In Transit", "Item Despatched"),
    'EVOLB' => array("EVOLB", "Across the network", "Mail Centre - Item received in wrongly labelled bag", "In Transit", "Arrived at (scan location shows)"),
    'EVOMI' => array("EVOMI", "Across the network", "Item mis-sorted", "In Transit", "Arrived at (scan location shows)"),
    'EVOOC' => array("EVOOC", "Across the network", "Mail Centre - Item out of Course", "In Transit", "Arrived at (scan location shows)"),
    'EVORC' => array("EVORC", "Inward MC/Delivery Office", "Item received in container", "In Transit", "Arrived at (scan location shows)"),
    'EVORI' => array("EVORI", "Inward MC/Delivery Office", "MC Handheld Acceptance – inferred from container receipt", "In Transit", "Arrived at (scan location shows)"),
    'EVORR' => array("EVORR", "Across the network", "Item received and identified as a return to sender item", "Returned to Sender", "Returned to Sender"),
    'EVORT' => array("EVORT", "Outward MC", "Item received for RTS from the DO at the MC", "In Transit", ""),
    'EVORW' => array("EVORW", "Across the network", "Mail Centre - Bag received without DUN", "In Transit", "Arrived at (scan location shows)"),
    'EVOUF' => array("EVOUF", "Across the network", "Item refused by CAA", "In Transit", "Item on its way"),
    'EVOVB' => array("EVOVB", "Across the network", "Mail Centre - Tampered/Violated Bag", "In Transit", "Arrived at (scan location shows)"),
    'EVPLA' => array("EVPLA", "POL", "Local Collect arrived at Post Office", "Delivered", "Item 'Barcode' was delivered to the addressee's Post Office at 'POLLOcationName' on the 'EventDateTime' for collection. The addressee is using our Local Collect service.                          "),
    'EVPLC' => array("EVPLC", "POL", "Local Collect collected from Post Office - No Fee Paid", "Collected", "Item 'Barcode' was collected by the customer from 'POLLocationName' on 'EventDateTime'."),
    'EVPLF' => array("EVPLF", "POL", "Local Collect collected from Post Office - Fee Paid", "Collected", "Item 'Barcode' was collected by the customer from 'POLLocationName' on 'EventDateTime'."),
    'EVPLI' => array("EVPLI", "POL", "Local Collect request to return to sender", "Returned to Sender", "Sender Request - Returned to Sender"),
    'EVPLN' => array("EVPLN", "POL", "Local Collect not collected return to sender", "Returned to Sender", "Not Collected - Returned to Sender"),
    'EVPLR' => array("EVPLR", "POL", "Local Collect refused return to sender", "Returned to Sender", "Refused - Returned to Sender"),
    'EVPLU' => array("EVPLU", "POL", "Local Collect addressee unknown return to sender", "Returned to Sender", "Item 'Barcode' has been returned from 'POLLocationName' on 'EventDateTime' to be returned to the sender as the recipient is not known at the address."),
    'EVPLX' => array("EVPLX", "POL", "Local Collect waiting at Post Office for return to sender", "Returned to Sender", "Returned to Sender"),
    'EVPOA' => array("EVPOA", "POL", "Undelivered - Attempted on time – Address Inaccessible", "Delivery Attempted", "Delivery Attempted - Address Inaccessible"),
    'EVPOG' => array("EVPOG", "POL", "Undelivered – Attempted on time – Addressee Gone Away", "Returned to Sender", "Recipient not at address. Returned to sender."),
    'EVPOI' => array("EVPOI", "POL", "Undelivered - Attempted on time – Address Incomplete", "Returned to Sender", "Incorrectly addressed. Returned to sender."),
    'EVPON' => array("EVPON", "POL", "Undelivered - Attempted on time – No Answer", "Delivery Attempted", "Delivery Attempted - No Answer"),
    'EVPOR' => array("EVPOR", "POL", "Undelivered - Attempted on time – Refused", "Returned to Sender", "Refused - Returned to Sender"),
    'EVPOU' => array("EVPOU", "POL", "Undelivered - Attempted on time – Address Unknown", "Delivery Attempted", "Unknown recipient. Returned to sender."),
    'EVPPA' => array("EVPPA", "POL", "Receipt scan for all product arriving at POL", "We've got it", "Accepted at Post Office."),
    'EVPPB' => array("EVPPB", "Pre-Advice", "Parcel Received at POL (inferred)", "We're expecting it", "The sender has let us know this item will be with us soon."),
    'EVPUX' => array("EVPUX", "POL", "Undelivered item – Accepted for return by POL", "Returned to Sender", ""),
    'EVPXA' => array("EVPXA", "POL", "Undelivered - Attempted Late – Address Inaccessible", "Delivery Attempted", "Delivery Attempted - Address Inaccessible"),
    'EVPXG' => array("EVPXG", "POL", "Undelivered - Attempted Late – Addressee Gone Away", "Returned to Sender", "Recipient not at address. Returned to sender."),
    'EVPXI' => array("EVPXI", "POL", "Undelivered - Attempted Late – Address Incomplete", "Returned to Sender", "Incorrectly addressed. Returned to sender."),
    'EVPXN' => array("EVPXN", "POL", "Undelivered - Attempted Late  – No Answer", "Delivery Attempted", "Delivery Attempted - No Answer"),
    'EVPXR' => array("EVPXR", "POL", "Undelivered - Attempted Late – Refused", "Returned to Sender", "Refused - Returned to Sender"),
    'EVPXU' => array("EVPXU", "POL", "Undelivered - Attempted Late – Address Unknown", "Delivery Attempted", "Unknown recipient. Returned to sender."),
    'EVSGD' => array("EVSGD", "Delivery Office", "Confirmed dangerous goods and disposed of", "Restricted or Prohibited Contents", "Confirmed dangerous goods"),
    'EVSGS' => array("EVSGS", "Delivery Office", "Confirmed mixed goods - dangerous goods disposed of and remaining items allowed to to continue", "Restricted or Prohibited Contents", "Restricted or Prohibited Contents"),
    'EVSUS' => array("EVSUS", "Delivery Office", "Suspected prohibited goods ", "Restricted or Prohibited Contents", "Suspected prohibited goods"),
    'EVZAM' => array("EVZAM", "System generated", "Delivery Attempted Notification", "Not Applicable", ""),
    'EVZBF' => array("EVZBF", "System generated", "Billing request processed", "Not Applicable", ""),
    'EVZDM' => array("EVZDM", "System generated", "Delivery Made Notification", "Not Applicable", ""),
    'EVZDR' => array("EVZDR", "Notifications", "Due to be Returned Notification", "Not Applicable", ""),
    'EVZLM' => array("EVZLM", "System generated", "Item Left Notification", "Not Applicable"),
    'EVZRD' => array("EVZRD", "System generated", "Due to be Returned Notification", "Not Applicable", ""),
    'EVZRM' => array("EVZRM", "System generated", "Ready for Delivery Notification", "Not Applicable", ""),
    'EVZSO' => array("EVZSO", "System generated", "Sales Order raised in SAP", "Not Applicable", ""),
    'NDEDF' => array("NDEDF", "Notifications", "Item delivered notification unable to reach device", "Not Applicable", "Notification unsuccessful"),
    'NDERF' => array("NDERF", "Notifications", "Item delivered notification requested failed", "Not Applicable", "We're holding your item at  'EventLocationName' as there is a fee to pay. We've  left a card asking  for payment as either not enough postage was paid by the sender, or there is a customs charge due for the item."),
    'NDERS' => array("NDERS", "Notifications", "Item delivered notification requested successfully", "Not Applicable", "We're holding your item at  'EventLocationName' as there is a fee to pay. We've  left a card asking  for payment as either not enough postage was paid by the sender, or there is a customs charge due for the item."),
    'NDESF' => array("NDESF", "Notifications", "Item delivered notification unable to reach operator", "Not Applicable", "Notification unsuccessful"),
    'NDESS' => array("NDESS", "Notifications", "Item delivered notification sent successfully to operator", "Not Applicable", "Notification issued"),
    'NEPDF' => array("NEPDF", "Notifications", "E739 notification unable to reach device", "Not Applicable", ""),
    'NEPRF' => array("NEPRF", "Notifications", "E739 notification requested failed", "Not Applicable", ""),
    'NEPRS' => array("NEPRS", "Notifications", "E739 notification requested successfully", "Not Applicable", ""),
    'NEPSF' => array("NEPSF", "Notifications", "E739 notification unable to reach operator", "Not Applicable", ""),
    'NEPSS' => array("NEPSS", "Notifications", "E739 notification sent successfully to the operator", "Not Applicable", ""),
    'NFNDF' => array("NFNDF", "Notifications", "We still have your item notification unable to reach device", "Not Applicable", "Notification unsuccessful"),
    'NFNRF' => array("NFNRF", "Notifications", "We still have your item notification requested failed", "Not Applicable"),
    'NFNRS' => array("NFNRS", "Notifications", "We still have your item notification requested successfully", "Not Applicable", ""),
    'NFNSF' => array("NFNSF", "Notifications", "We still have your item notification unable to reach operator", "Not Applicable", "Notification unsuccessful"),
    'NFNSS' => array("NFNSS", "Notifications", "We still have your item notification sent successfully to operator", "Not Applicable", "Notification issued"),
    'NLCDF' => array("NLCDF", "Notifications", "Item delivered to your nominated Local Collect destination notification unable to reach device", "Not Applicable", "Notification unsuccessful"),
    'NLCRF' => array("NLCRF", "Notifications", "Item delivered to your nominated Local Collect destination notification requested failed", "Not Applicable", ""),
    'NLCRS' => array("NLCRS", "Notifications", "Item delivered to your nominated Local Collect destination notification requested successfully", "Not Applicable", ""),
    'NLCSF' => array("NLCSF", "Notifications", "Item delivered to your nominated Local Collect destination notification unable to reach operator", "Not Applicable", "Notification unsuccessful"),
    'NLCSS' => array("NLCSS", "Notifications", "Item delivered to your nominated Local Collect destination notification sent successfully to operator", "Not Applicable", "Notification issued"),
    'NRDDF' => array("NRDDF", "Notifications", "Item ready for delivery notification unable to reach device", "Not Applicable", "Notification unsuccessful"),
    'NRDRF' => array("NRDRF", "Notifications", "Item ready for delivery notification requested failed", "Not Applicable", ""),
    'NRDRS' => array("NRDRS", "Notifications", "Item ready for delivery  notification requested successfully", "Not Applicable", ""),
    'NRDSF' => array("NRDSF", "Notifications", "Item ready for delivery notification unable to reach operator", "Not Applicable", "Notification unsuccessful"),
    'NRDSS' => array("NRDSS", "Notifications", "Item ready for delivery notification sent successfully to operator", "Not Applicable", "Notification issued"),
    'NREDF' => array("NREDF", "Notifications", "Item returned to sender notification unable to reach device", "Not Applicable", "Notification unsuccessful"),
    'NRERF' => array("NRERF", "Notifications", "Item returned to sender notification requested failed", "Not Applicable", ""),
    'NRERS' => array("NRERS", "Notifications", "Item returned to sender notification requested successfully", "Not Applicable", ""),
    'NRESF' => array("NRESF", "Notifications", "Item returned to sender notification unable to reach operator", "Not Applicable", "Notification unsuccessful"),
    'NRESS' => array("NRESS", "Notifications", "Item returned to sender notification sent successfully to operator", "Not Applicable", "Notification issued"),
    'NSFDF' => array("NSFDF", "Notifications", "Item delivered to Safeplace notification unable to reach device", "Not Applicable", "Notification unsuccessful"),
    'NSFRF' => array("NSFRF", "Notifications", "Item delivered to Safeplace notification requested failed", "Not Applicable", ""),
    'NSFRS' => array("NSFRS", "Notifications", "Item delivered to Safeplace notification requested successfully", "Not Applicable", ""),
    'NSFSF' => array("NSFSF", "Notifications", "Item delivered to Safeplace notification unable to reach operator", "Not Applicable", "Notification unsuccessful"),
    'NSFSS' => array("NSFSS", "Notifications", "Item delivered to Safeplace notification sent successfully to operator", "Not Applicable", "Notification issued"),
    'NUNDF' => array("NUNDF", "Notifications", "Unable to deliver your item notification unable to reach device", "Not Applicable", "Notification unsuccessful"),
    'NUNRF' => array("NUNRF", "Notifications", "Unable to deliver your item notification requested failed", "Not Applicable", ""),
    'NUNRS' => array("NUNRS", "Notifications", "Unable to deliver your item notification requested successfully", "Not Applicable", ""),
    'NUNSF' => array("NUNSF", "Notifications", "Unable to deliver your item notification unable to reach operator", "Not Applicable", "Notification unsuccessful"),
    'NUNSS' => array("NUNSS", "Notifications", "Unable to deliver your item notification sent successfully to operator", "Not Applicable", "Notification issued"));

    $sql = "     SELECT 
                    service_constant.constant, 
                    service_constant_value.service_id,
                    service_constant_value.agent_id,
                    service_constant_value.constant_value
                FROM
                    service_constant INNER JOIN service_constant_value ON service_constant.id = service_constant_value.constant_id
                WHERE
                    carrier_id = '15' AND service_constant.constant IN (

                    'ROYALMAIL_TRACKING_FTP_USER',
                    'ROYALMAIL_TRACKING_FTP_PASSWORD',
                    'ROYALMAIL_TRACKING_FTP_SITE'
                    ) AND trim(service_constant_value.constant_value) <> ''";
    
    $constantObj = ServiceConstantValue::getServiceConstantValueListFromSql($sql);
    $ftpDetailsArray = [];
	/*$ftpDetailsArray[99][10] = array(
	"ROYALMAIL_TRACKING_FTP_USER" => "E000451",
    "ROYALMAIL_TRACKING_FTP_PASSWORD"=> "QiU87TpK",
    "ROYALMAIL_TRACKING_FTP_SITE"=> "144.87.142.139"
	);*/
				
	$ftpDetailsArray[$agentId][$serviceId][$constantName] = $constantValue;
    if(count($constantObj)>0){
        foreach($constantObj as $constantItems){
            $serviceId      = $constantItems->getServiceId();
            $agentId        = $constantItems->getAgentId();
            $constantName   = $constantItems->getConstant();
            $constantValue  = $constantItems->getConstantValue();
         
            $ftpDetailsArray[$agentId][$serviceId][$constantName] = $constantValue;
        }
    }
    
    if(!empty($ftpDetailsArray)){
        foreach($ftpDetailsArray as $agentKey=>$serviceArray){
            $ftpDetailsArray[$agentKey]= array_map("unserialize", array_unique(array_map("serialize", $serviceArray)));
        }
    }
  /*  echo "<pre>";
    print_r($ftpDetailsArray);
    die; */
$remotefile 			= "/pub/tracked/outgoing/";
$remotefileprocessed 	= "/pub/tracked/outgoing_processed/";
$remotefileMove 		= "/pub/tracked/outgoing_tmp/";
$fileScannedFolder 		= SETTING_DIR_ASSETS . 'tracking_data/ROYALMAIL/data_in/';
$fileScannedFolderProcessed = SETTING_DIR_ASSETS . 'tracking_data/ROYALMAIL/data_process/';
$fileScannedFolderError = SETTING_DIR_ASSETS . 'tracking_data/ROYALMAIL/data_error/';

    if(!empty($ftpDetailsArray)){
// echo "<pre>";
// print_r($ftpDetailsArray);
// die;
        foreach($ftpDetailsArray as $agentKey=>$serviceArray){
            if(trim($agentKey)== '' || $agentKey == 0)
                continue;
            foreach($serviceArray as $serviceId=> $ftpArray){
                $servicesData = new Services($serviceId);
                $agentData      =   new AgentData($agentKey);
                    
                $ftpUserName    = @$ftpArray["ROYALMAIL_TRACKING_FTP_USER"];
                $ftpPassword    = @$ftpArray["ROYALMAIL_TRACKING_FTP_PASSWORD"];
                $ftpLink        = @$ftpArray["ROYALMAIL_TRACKING_FTP_SITE"];
				if(count($agentData)>0 && count($servicesData)>0 ){
    			if(trim($ftpUserName) == '' || trim($ftpPassword) == '' || trim($ftpLink) == '' ){
                    $to = "itsupport@oneworldexpress.com";
                    $subject = "ROYALMAIL TRACKING FTP CREDENTIAL NOT COMPLETE";
                    $message = "Dear Team, <br>"
                            . "<br>Agent Id:<b>".$agentData->getAgentName()." (".$agentData->getAgentCode().")</b> service id:<b>".$servicesData->getName()." (".$servicesData->getCode().")</b> has invalid params of FTP to connect for tracking. Please review and correct."
                            . "<br><br><br>"
                            . "<b>Current Details:</b>".$ftpLink
                            . "<br><b>Username:</b>".$ftpUserName
                            . "<br><b>Password:</b>".$ftpPassword
                            . "<br><br><br>"
                            . "<br>Thanks & Regards<br>"
                            . "SmartTrack Schedualr Team";
                    // Always set content-type when sending HTML email
                    $headers = "MIME-Version: 1.0" . "\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

                    // More headers
                    $headers .= 'From: SmartTrack<smart@smarttrack.co>' . "\r\n";
                    //$headers .= 'Cc: myboss@example.com' . "\r\n";
                    mail($to,$subject,$message,$headers);
                    continue;
                }
				}
				//$ftpconnection = new Net_SFTP("144.87.142.139", 22022);
                //$ftpLoginCheck  = $ftpconnection->login("E000770", "C7iCzjka");
                $ftpconnection = new Net_SFTP(trim($ftpLink), 22022);
                $ftpLoginCheck  = $ftpconnection->login(trim($ftpUserName), trim($ftpPassword));
                if(!$ftpLoginCheck){
                    $to = "itsupport@oneworldexpress.com";
                    $subject = "ROYALMAIL TRACKING FTP CREDENTIAL NOT VALID";
                    $message = "Dear Team, <br."
                            . "<br>Agent Id:<b>".$agentData->getAgentName()." (".$agentData->getAgentCode().")</b> service id:<b>".$servicesData->getName()." (".$servicesData->getCode().")</b> has invalid params of FTP to connect for tracking. Please review and correct."
                            . "<br><br><br>"
                            . "<b>Current Details:</b>".$ftpLink
                            . "<br><b>Username:</b>".$ftpUserName
                            . "<br><b>Password:</b>".$ftpPassword
                            . "<br><br><br>"
                            . "<br>Thanks & Regards<br>"
                            . "SmartTrack Schedualr Team";
                    
                    // Always set content-type when sending HTML email
                    $headers = "MIME-Version: 1.0" . "\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                    // More headers
                    $headers .= 'From: SmartTrack<smart@smarttrack.co>' . "\r\n";
                    //$headers .= 'Cc: myboss@example.com' . "\r\n";
                    mail($to,$subject,$message,$headers);
                    continue;
                }
                

                // Copy files from royalmail to process files 
                $getAllFiles = $ftpconnection->_list($remotefile);
                if (count($getAllFiles) > 0) {
                    foreach ($getAllFiles as $keyFile => $valueFile) {

                        $filename = $valueFile['filename'];
                        if (in_array($filename, array('.', '..')))
                            continue;
                        $remote_file = $remotefile . $filename;
                        $remote_file_processed = $remotefileprocessed . $filename . time();
                        $ftpconnection->get($remote_file, $fileScannedFolder. $filename);
                        if(strlen($filename)>40)
                            $ftpconnection->rename($remote_file, $remote_file_processed);
                        else
                            $ftpconnection->rename($remote_file, $remote_file. time());
                    }
                }
            }
        }
		
    }
    /**********************************************/
                $ftpLink    = "213.246.110.102";
                $ftpUserName    = "rmserver6";
                $ftpPassword        = "M]{45^J4@E7>3rm#";
				$folder_path = SETTING_DIR_ASSETS . "tracking_data/ROYALMAIL/data_in/";
				if (!file_exists($folder_path))
					@mkdir($folder_path, 0777, true);
							
				$conn_id = ftp_connect($ftpLink);
				if (!$conn_id) {
					echo "Old System FTP connection failed";
					exit;
				} else {
					$fileNoteCoppied = [];
					ftp_login($conn_id, $ftpUserName, $ftpPassword);
					ftp_pasv($conn_id, true);
					$remoteFilePath = "/data_process/";
					$arrfile = ftp_nlist($conn_id, $remoteFilePath);
					//echo "<pre>";
					//print_r($arrfile);
					//die;
					if (sizeof($arrfile) > 0) {
						$matchfile = [];
						foreach ($arrfile as $fileKye=>$filename) {
							if (in_array($filename, array('.', '..')))
								continue;
							
							
							$filename = str_replace("/data_process/","",$filename);
							$localFilePath = $folder_path . $filename; 
						
							chmod($folder_path, 0777);
							$fp = fopen($localFilePath, 'w');
							/*if($filename == '/data_process/W46E200630DF' || $filename == 'W46E200630DF'){
								echo "<pre>";
								echo $filename;
								echo "<br>";
								echo $localFilePath;
								print_r(ftp_fget($conn_id, $fp, $remoteFilePath . $filename, FTP_ASCII, FTP_AUTORESUME));
								die;
							}*/
							if (ftp_fget($conn_id, $fp, $remoteFilePath . $filename, FTP_ASCII, FTP_AUTORESUME)) {
								if (!ftp_put($conn_id, $remoteFilePath . $filename, "" . $localFilePath, FTP_ASCII)) {
									$fileNoteCoppied[] = $filename;
								} else {
									$oldFileName = $remoteFilePath.$filename;
									$newFileName = "/data_process_by_server6/".substr($filename, 0 ,12);
									ftp_rename ( $conn_id ,$oldFileName , $newFileName ) ;
								}
							} else{
								echo $remoteFilePath . $filename."<br>";
							}
							
							if($fileKye == 1000)
								break;
						}
					}
				}

                if(count($fileNoteCoppied)>0){
						$to = "itsupport@oneworldexpress.com";
						$subject = "ROYALMAIL TRACKING FILE SERVER4 NOT COPIED";
						$message = "Dear Team, <br."
								. "<br>Please check below file which are not copied from server, Please check ."
								. "<br><br><br>";
							foreach($fileNoteCoppied as $fileNameNotC)	
								$message .=	"<b>File Name:</b>".$fileNameNotC;
								
							$message .=     "<br><br><br>"
								. "<br>Thanks & Regards<br>"
								. "SmartTrack Schedualr Team";
						
						// Always set content-type when sending HTML email
						$headers = "MIME-Version: 1.0" . "\r\n";
						$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
						// More headers
						$headers .= 'From: SmartTrack<smart@smarttrack.co>' . "\r\n";
						//$headers .= 'Cc: myboss@example.com' . "\r\n";
						mail($to,$subject,$message,$headers);
					}
	/****************************************/
$filesall = scandir($fileScannedFolder);
echo "<br>";echo "<br>";echo "<br>";
                            

if (count($filesall) > 0) {
    foreach ($filesall as $keyFileIndex => $valueFileIndex) {
        if (in_array($valueFileIndex, array('.', '..')))
            continue;
        $fullFileName = $fileScannedFolder . $valueFileIndex;
        
        $fullFileNameProcessed = $fileScannedFolderProcessed . $valueFileIndex;
		$fullFileNameError  = $fileScannedFolderError . strtr($valueFileIndex,0,12);
        if (file_exists($fullFileName)) {
            $findme = 'DF';
            $pos = strpos($valueFileIndex, $findme);
            if ($pos > 0) {
                $arrayDataAll = array();
                $insertQuery = array();
                $file = fopen($fullFileName, 'r');
				$countData = false;
                while (($line = fgetcsv($file)) !== FALSE) {
                    // print_r($line);
					if(!$countData){
						$countData = true;
						continue;
					}
                    $arrayData = array();
                    
                    $royalMailTrackingStatusCode =$line[6]; 
                    $StatusRmCodes = $StatusCodes[$royalMailTrackingStatusCode];
                  
                    if (!empty($StatusRmCodes)) {
                        
                        $saleOrderNumber = $line[5];
                        
						$arrayData['trackingNumber'] = $line[2];
                        $arrayData['status_code'] = $StatusRmCodes[3];
                        $arrayData['date'] = substr($line[8], 0, 4) . '-' . substr($line[8], 4, 2) . '-' . substr($line[8], 6, 2) . ' ' . substr($line[9], 0, 2) . ':' . substr($line[9], 2, 2) . ':' . substr($line[9], 4, 2);
                        $description = str_replace("'EventLocationName'", $StatusRmCodes[1], $StatusRmCodes[4]);
                        $description = str_replace("'EventDateTime'", $arrayData['date'], $description);
                        $arrayData['description'] = $description;

                        ////////////////////////////////// GORDON WAS COMING ON A CSV FILE ///////////////////
                        $arrayData['track_point'] = str_replace("GORDON", "", $line[5]);
                        if (trim($arrayData['status_code']) != 'Not Applicable') {
                            if (!mysqli_ping($dbConnection)) {
                                $dbConnection = DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);
                            }
                            
							$spTrackingStatus = RoyalMailTrackingStatus::getOweStatusCode($royalMailTrackingStatusCode);
                            $consignmentStatusCode = RoyalMailTrackingStatus::getConsignmentStatus($spTrackingStatus);
                        
							$trackingDataIn =  ""
                                    . "INSERT INTO "
                                    . "     tracking_data (
                                            `entity_id`,
                                            `entity_type`,
                                            `tracking_number`,
                                            `user_id`,
                                            `track_point`,
                                            `date_created`,
                                            `ip_address`,
                                            `status_code_id`,
                                            `carrier_code`,                                            
                                            `carrier_desc`
                                            
                                            )"
                                    . " SELECT "
                                    . "     p.id,"
                                    . "     'parcel',"
                                    . "     p.tracking_number,"
                                    . "     0,"
                                    . "     '" . mysqli_escape_string($dbConnection,$arrayData['track_point']) . "',"
                                    . "     '" . mysqli_escape_string($dbConnection,$arrayData['date']) . "',"
                                    . "     '000.000.000.000',"
                                    . "     '".$spTrackingStatus."',"
                                    . "     '" . mysqli_escape_string($dbConnection,$royalMailTrackingStatusCode) . "',"
                                    . "     '" . mysqli_escape_string($dbConnection,$arrayData['description']) . "'"
                                    . " FROM "
                                    . "     consignment c "
                                    . " INNER JOIN "
                                    . "     parcel p "
                                    . " ON "
                                    . "     p.consignment_id = c.id  "
                                    . " INNER JOIN "
                                    . "     tracking_data tdh "
                                    . " ON "
                                    . "     tdh.entity_id = p.id "
                                    . " WHERE "
                                    . "     c.awb = '" . mysqli_escape_string($dbConnection,$arrayData['trackingNumber']) . "' "
                                    . " AND '" . mysqli_escape_string($dbConnection,$arrayData['description']) . "' NOT IN 
									(select carrier_desc from tracking_data where entity_id = p.id) limit 1";
                          
						  
                            DbAccess3::runQueryWithError($trackingDataIn);
                          
                            
                            if (in_array(trim(strtolower($arrayData['status_code'])), array('delivered', 'collected')) && mysqli_escape_string($dbConnection,$arrayData['trackingNumber']) != '') {
                          
                             $consignmentUpdate = "UPDATE "
                                        . " consignment "
                                        . "SET "
                                        . " consignment_status = 'delivered', "
                                        . " shipment_status = '19', "
                                        . " date_delivered = '" . strtotime(mysqli_escape_string($dbConnection,$arrayData['date'])) . "' "
                                        . " date_booked = IF(date_booked is null OR trim(date_booked) = '' OR date_booked <= 0, ".time().", date_booked) "
                                        . " WHERE  awb = '" . mysqli_escape_string($dbConnection,$arrayData['trackingNumber']) . "' AND consignment_status <> '" . mysqli_escape_string($dbConnection,$arrayData['status_code']) . "' ";
                                DbAccess3::runQuery($consignmentUpdate);
                            }else{
                                $consignmentStatus = isset(Consignment::$database_status_array[$consignmentStatusCode]) ? Consignment::$database_status_array[$consignmentStatusCode] : '';
                          
                              $consignmentUpdate = "UPDATE "
                                        . " consignment "
                                        . "SET "
                                        . " consignment_status = if(trim('".$consignmentStatus."')<> '','".$consignmentStatus."', consignment_status),"
                                        . " shipment_status = if(trim('".$consignmentStatusCode."')<> '0','".$consignmentStatusCode."', shipment_status),"
                                        . " date_booked = IF(date_booked is null OR trim(date_booked) = '' OR date_booked <= 0, ".time().", date_booked) "
                                        . " WHERE  awb = '" . mysqli_escape_string($dbConnection,$arrayData['trackingNumber']) . "' AND consignment_status <> '" . mysqli_escape_string($dbConnection,$arrayData['status_code']) . "'";
                                DbAccess3::runQuery($consignmentUpdate);   
                            }
                            
                         
							 $parcelUpdate = "UPDATE parcel SET "
                                        . " parcel_status_code =  if(".$consignmentStatusCode."> 0,'".$consignmentStatusCode."', parcel_status_code), "
                                        . " owe_status_code = if('" . mysqli_escape_string($dbConnection,$arrayData['status_code']) . "' <> '','" . mysqli_escape_string($dbConnection,$arrayData['status_code']) . "', owe_status_code), "
                                        . " last_tracking_update = '" . mysqli_escape_string($dbConnection,$arrayData['date']) . "' "
                                    . " WHERE  "
                                    . "     tracking_number = '" . mysqli_escape_string($dbConnection,$arrayData['trackingNumber']) . "' "
                                    . " AND owe_status_code <> '" . mysqli_escape_string($dbConnection,$arrayData['status_code']) . "' ";
                            DbAccess3::runQuery($parcelUpdate);
                        } 
					
						
						
						if (trim($royalMailTrackingStatusCode) == 'EVZSO') { 
                            if (!mysqli_ping($dbConnection)) {
                                $dbConnection = DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);
                            }
                            
                            
                           	$docketNumberInvocieInsert  = "  INSERT 
                                                        INTO bagging 
                                                            ( bagnumber,user_id,bag_status,service,country,country_iso_code,bag_type,bag_source_country_id, bag_source_warehouse_id, bag_destination_country_id,bag_destination_warehouse_id)
                                                        SELECT '".$saleOrderNumber."',2215,1,c.service_id,c.country_id,'GB','MIX',225, 10, 225,10 "
                                                            . " FROM "
                                                            . "     consignment c "
                                                            . " WHERE "
                                                            . "         c.awb = '".$arrayData['trackingNumber']."' "
                                                            . "     AND '".$saleOrderNumber."' NOT IN (SELECT bagnumber FROM bagging)";
                            DbAccess3::runQuery($docketNumberInvocieInsert);
                            
							$bagNumberMapping = "INSERT INTO 
                                                        parcel_bagging_mapping (bag_id, parcel_id,added_by,added_date)
                                                SELECT "
                                                    . " id ,"
                                                    . " (SELECT id FROM parcel WHERE tracking_number = '".$arrayData['trackingNumber']."' ) ,"
													. " 58, "
                                                    . " NOW() "
                                                    . "FROM bagging  WHERE bagnumber = '".$saleOrderNumber."'"; 
                            DbAccess3::runQuery($bagNumberMapping);
							
							$docketNumberInvocieUpdateLogDK = "INSERT INTO `royalmail_docket_number` ( `tracking_number`, `docket_number`, `file_name`)
                                                    SELECT * FROM (SELECT '".$arrayData['trackingNumber']."', '".$saleOrderNumber."', '".substr($valueFileIndex,0,12)."') AS tmp
                                                WHERE
                                                    NOT EXISTS( SELECT 
                                                            docket_number, tracking_number
                                                        FROM
                                                            royalmail_docket_number
                                                        WHERE
                                                            docket_number = '".$saleOrderNumber."'
                                                                AND tracking_number = '".$arrayData['trackingNumber']."')";
							DbAccess3::runQuery($docketNumberInvocieUpdateLogDK);
								
							
                        }
                    }
                }
                fclose($file);
				if(strlen($valueFileIndex)>40){
                	rename($fullFileName, $fullFileNameProcessed);
				} else {
					rename($fullFileName, $fullFileName.time());
				}
            } else {
				rename($fullFileName, $fullFileNameError);
			}
        }
		
    }
}
mysqli_close($dbConnection);
