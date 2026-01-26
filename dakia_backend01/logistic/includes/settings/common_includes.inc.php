<?php
////////////////////////////////////////////////////
// Classes configuration file
////////////////////////////////////////////////////
// GENERAL LIBRARIES
require_once(BASE_PATH."includes/library/dbaccess3.class.php");
require_once(BASE_PATH."includes/library/basepage.class.php");
require_once(BASE_PATH."includes/library/ivisualcomponent.php"); 
require_once(BASE_PATH."includes/visualcomponents/errorlist.class.php"); 
require_once(BASE_PATH."includes/visualcomponents/adminmenu.class.php"); 

require_once(BASE_PATH."includes/library/util.inc.php");
require_once(BASE_PATH."includes/library/ddl.inc.php");
require_once(BASE_PATH."includes/library/easyphpthumbnail.class.php");
require_once(BASE_PATH."includes/library/trace.class.php");
require_once(BASE_PATH."includes/library/auditlogs.inc.php");
require_once(BASE_PATH."includes/general/Sessionmanager.class.php");
require_once(BASE_PATH."includes/general/FlashMessages.class.php");

require_once(BASE_PATH."includes/mapping/user.class.php");
require_once(BASE_PATH."includes/mapping/userfilter.class.php");
require_once(BASE_PATH."includes/mapping/customeraccount.class.php");
require_once(BASE_PATH."includes/mapping/useraccountfilter.class.php");
require_once(BASE_PATH."includes/mapping/useraudit.class.php");
require_once(BASE_PATH."includes/mapping/userauditfilter.class.php");
require_once(BASE_PATH."includes/mapping/permissions.class.php");
require_once(BASE_PATH."includes/mapping/permissionsfilter.class.php");

require_once(BASE_PATH."includes/mapping/languages.class.php");
require_once(BASE_PATH."includes/mapping/languagefilter.class.php");
require_once(BASE_PATH."includes/mapping/languagekeys.class.php");
require_once(BASE_PATH."includes/mapping/languagekeysfilter.class.php");
require_once(BASE_PATH."includes/mapping/invoices.class.php");
require_once(BASE_PATH."includes/mapping/invoicesfilter.class.php");

//need to check
require_once(BASE_PATH."includes/library/emailsend.class.php");
require_once(BASE_PATH."includes/general/carrierservice.class.php");
/*

require_once(BASE_PATH."includes/general/carrierservice.class.php");

require_once(BASE_PATH."includes/library/httpasynccomm.class.php");
require_once(BASE_PATH."includes/library/httpcommunication.class.php");
require_once(BASE_PATH."includes/library/ivisualcomponent.php");
require_once(BASE_PATH."includes/library/trace.class.php");


require_once(BASE_PATH."includes/library/iaddress.class.php");


// GENERAL FUNCTIONS & CLASSES
require_once(BASE_PATH."includes/general/Lightbox.class.php");

require_once(BASE_PATH."includes/general/app.inc.php");
require_once(BASE_PATH."includes/general/html.inc.php");

require_once(BASE_PATH."includes/3rdparty/Paypal.class.php");
require_once(BASE_PATH."includes/3rdparty/tcpdf/tcpdf.php");
require_once(BASE_PATH."includes/3rdparty/calendar/classes/tc_calendar.php");

require_once(BASE_PATH."includes/mapping/checkdigit.class.php");
require_once(BASE_PATH."includes/mapping/user.class.php");
require_once(BASE_PATH."includes/mapping/userfilter.class.php");

require_once(BASE_PATH."includes/mapping/licenceplate.class.php");
require_once(BASE_PATH."includes/mapping/addresslookup.class.php");

require_once(BASE_PATH."includes/mapping/collectiontime.class.php");
require_once(BASE_PATH."includes/mapping/collectiontimegroup.class.php");
require_once(BASE_PATH."includes/mapping/collectiontimegroupfilter.class.php");
require_once(BASE_PATH."includes/mapping/country.class.php");
require_once(BASE_PATH."includes/mapping/countryfilter.class.php");

require_once(BASE_PATH."includes/mapping/currency.class.php"); // new class added by shabbir
require_once(BASE_PATH."includes/mapping/currencyfilter.class.php"); 
require_once(BASE_PATH."includes/mapping/countryrateband.class.php"); // new class added by shabbir
require_once(BASE_PATH."includes/mapping/countryratebandfilter.class.php"); 
require_once(BASE_PATH."includes/mapping/tariff.class.php");
require_once(BASE_PATH."includes/mapping/tarifffilter.class.php"); 

require_once(BASE_PATH."includes/mapping/invoices.class.php"); // new class added by rafid
require_once(BASE_PATH."includes/mapping/invoicesfilter.class.php"); 

require_once(BASE_PATH."includes/mapping/consignment.class.php"); // copied from smartsystem  by rafid
require_once(BASE_PATH."includes/mapping/consignmentfilter.class.php"); // copied from smartsystem  by rafid

//require_once(BASE_PATH."includes/autoload/InvoicePDF.class.php"); // copied from smartsystem  by rafid

require_once(BASE_PATH."includes/mapping/services.class.php");  // copied from smartsystem  by rafid
require_once(BASE_PATH."includes/mapping/servicefilter.class.php");  // copied from smartsystem  by rafid

require_once(BASE_PATH."includes/mapping/courier.class.php");
require_once(BASE_PATH."includes/mapping/courierfilter.class.php");

require_once(BASE_PATH."includes/mapping/parcel.class.php");
require_once(BASE_PATH."includes/mapping/parcelfilter.class.php");
require_once(BASE_PATH."includes/mapping/Rateband.class.php");
require_once(BASE_PATH."includes/mapping/ratebandfilter.class.php");
require_once(BASE_PATH."includes/mapping/userservicescharges.class.php");
require_once(BASE_PATH."includes/mapping/userserviceschargesfilter.class.php");
require_once(BASE_PATH."includes/mapping/tariffsaccountmapping.class.php");
require_once(BASE_PATH."includes/mapping/tariffsaccountmappingfilter.class.php");

require_once(BASE_PATH."includes/mapping/remoteareaUserMapping.class.php");
require_once(BASE_PATH."includes/mapping/remoteareaUserMappingFilter.class.php");

require_once(BASE_PATH."includes/mapping/postcodeuserservicecharges.class.php");
require_once(BASE_PATH."includes/mapping/postcodeuserservicechargesfilter.class.php");

/*
*	Manual Invoice Files Include
* /
require_once(BASE_PATH."includes/mapping/invoicesmanual.class.php");
require_once(BASE_PATH."includes/mapping/invoicesmanualfilter.class.php");
require_once(BASE_PATH."includes/mapping/invoicesmanualdetails.class.php");
require_once(BASE_PATH."includes/mapping/invoicesmanualdetailsfilter.class.php");

require_once(BASE_PATH."includes/mapping/invoicedetail.class.php");
require_once(BASE_PATH."includes/mapping/invoicedetailfilter.class.php");

require_once(BASE_PATH."includes/mapping/creditnotedetails.class.php");
require_once(BASE_PATH."includes/mapping/creditnotedetailsfilter.class.php");
require_once(BASE_PATH."includes/mapping/cslog.class.php");
require_once(BASE_PATH."includes/mapping/cslogfilter.class.php");
require_once(BASE_PATH."includes/mapping/consignmentlog.class.php");
require_once(BASE_PATH."includes/mapping/consignmentlogfilter.class.php");
require_once(BASE_PATH."includes/mapping/statuslog.class.php");
require_once(BASE_PATH."includes/mapping/statuslogfilter.class.php");
require_once(BASE_PATH."includes/mapping/notfoundrecord.class.php");
require_once(BASE_PATH."includes/mapping/notfoundrecordfilter.class.php");
require_once(BASE_PATH."includes/mapping/customizeduserservicesrouting.class.php");
require_once(BASE_PATH."includes/mapping/customizeduserservicesroutingfilter.class.php");

require_once(BASE_PATH."includes/mapping/remoteareaUserMapping.class.php");
require_once(BASE_PATH."includes/mapping/remoteareaUserMappingFilter.class.php");

require_once(BASE_PATH."includes/mapping/tariffs.class.php");

require_once(BASE_PATH."includes/mapping/bagaddreason.class.php");
require_once(BASE_PATH."includes/mapping/bagaddreasonfilter.class.php");

require_once(BASE_PATH."includes/mapping/sorterpostcodezone.class.php");
require_once(BASE_PATH."includes/mapping/sorterpostcodezonefilter.class.php");

require_once(BASE_PATH."includes/mapping/cacesaroutine.class.php");
require_once(BASE_PATH."includes/mapping/cacesaroutinefilter.class.php");

require_once(BASE_PATH."includes/mapping/agentdata.class.php");
require_once(BASE_PATH."includes/mapping/agentdatafilter.class.php");
require_once(BASE_PATH."includes/mapping/tracking_data.class.php");
require_once(BASE_PATH."includes/mapping/trackingdatatfilter.class.php");
require_once(BASE_PATH."includes/mapping/creditnote.class.php");
require_once(BASE_PATH."includes/mapping/creditnotefilter.class.php");
require_once(BASE_PATH."includes/mapping/quotation.class.php");
require_once(BASE_PATH."includes/mapping/quotationdetails.class.php");
require_once(BASE_PATH."includes/mapping/quotationdetailsfilter.class.php");
//for reporting
require_once(BASE_PATH."includes/mapping/reporting.class.php");
require_once(BASE_PATH."includes/mapping/reportingfilter.class.php");

require_once(BASE_PATH."includes/mapping/invoiceextrachargestypes.class.php");
require_once(BASE_PATH."includes/mapping/invoiceextrachargestypesfilter.class.php");

require_once(BASE_PATH."includes/mapping/invoiceextracharges.class.php");
require_once(BASE_PATH."includes/mapping/invoiceextrachargesfilter.class.php");

/////////////////// Warehouse ////////////////////////
require_once(BASE_PATH."includes/mapping/warehouse.class.php");
require_once(BASE_PATH."includes/mapping/warehousefilter.class.php");
require_once(BASE_PATH."includes/mapping/Rack.class.php");
require_once(BASE_PATH."includes/mapping/rackfilter.class.php"); 
require_once(BASE_PATH."includes/mapping/RackShelf.class.php");
require_once(BASE_PATH."includes/mapping/rackshelffilter.class.php");
require_once(BASE_PATH."includes/mapping/RackShelfItem.class.php");
require_once(BASE_PATH."includes/mapping/logRackShelf.class.php");
require_once(BASE_PATH."includes/mapping/lograckshelffilter.class.php");

require_once(BASE_PATH."includes/mapping/generalreporting.class.php");

require_once(BASE_PATH."includes/mapping/prealert.class.php");
require_once(BASE_PATH."includes/mapping/prealertfilter.class.php");

require_once(BASE_PATH."includes/mapping/estimatedeliverytiming.class.php");
require_once(BASE_PATH."includes/mapping/estimatedeliverytimingfilter.class.php");

require_once(BASE_PATH."includes/mapping/optimusfilename.class.php");
require_once(BASE_PATH."includes/mapping/optimusfilenamefilter.class.php");

require_once(BASE_PATH."includes/mapping/opssummary.class.php");
require_once(BASE_PATH."includes/mapping/cartonpalletnumber.class.php");

require_once(BASE_PATH."includes/mapping/palletlabel.class.php");
require_once(BASE_PATH."includes/mapping/consignmentbillinghold.class.php");
require_once(BASE_PATH."includes/mapping/consignmentbillingholdfilter.class.php");

require_once(BASE_PATH."includes/");

require_once(BASE_PATH."includes/mapping/carrier.class.php");
require_once(BASE_PATH."includes/mapping/carrierfilter.class.php");

require_once(BASE_PATH."includes/mapping/labelfile.class.php");
require_once(BASE_PATH."includes/mapping/labelfilefilter.class.php");

require_once(BASE_PATH."includes/mapping/trackingestimatedtimefilter.class.php");
require_once(BASE_PATH."includes/mapping/trackingestimatedtime.class.php");

require_once(BASE_PATH."includes/mapping/bagnumbers.class.php");
require_once(BASE_PATH."includes/mapping/bagnumbersfilter.class.php");

require_once(BASE_PATH."includes/mapping/manifestconsignmentdatatfilter.class.php");

require_once(BASE_PATH."includes/mapping/manifestconsignment.class.php");

require_once(BASE_PATH."includes/mapping/pallet.class.php");
require_once(BASE_PATH."includes/mapping/palletfilter.class.php");

require_once(BASE_PATH."includes/mapping/manifestconsignmentmapping.class.php");
require_once(BASE_PATH."includes/mapping/manifestdatatfilter.class.php");

require_once(BASE_PATH."includes/mapping/consignmenthscodefilter.class.php");
require_once(BASE_PATH."includes/mapping/consignmenthscode.class.php");

require_once(BASE_PATH."includes/mapping/proformainvoicebilling.class.php");
require_once(BASE_PATH."includes/mapping/proformainvoicebillingfilter.class.php");

require_once(BASE_PATH."includes/mapping/location.class.php");
require_once(BASE_PATH."includes/mapping/locationfilter.class.php");

require_once(BASE_PATH."includes/mapping/palletlocation.class.php");
require_once(BASE_PATH."includes/mapping/palletlocationfilter.class.php");
require_once(BASE_PATH."includes/mapping/palletdispatchlabel.class.php");


require_once(BASE_PATH."includes/mapping/dxroutingfilter.class.php");
require_once(BASE_PATH."includes/mapping/dxrouting.class.php");

require_once(BASE_PATH."includes/mapping/domesticdaydeffile.class.php");
require_once(BASE_PATH."includes/mapping/domesticdaydeffilefilter.class.php");
require_once(BASE_PATH."includes/mapping/internationaldaydeffile.class.php");
require_once(BASE_PATH."includes/mapping/internationaldaydeffilefilter.class.php");
require_once(BASE_PATH."includes/mapping/royalmail48deffile.php");
require_once(BASE_PATH."includes/mapping/royalmail48deffilefilter.php");
require_once(BASE_PATH."includes/mapping/royalmail48signdeffile.php");
require_once(BASE_PATH."includes/mapping/royalmail48signdeffilefilter.php");
require_once(BASE_PATH."includes/mapping/eurodaydeffile.class.php");
require_once(BASE_PATH."includes/mapping/eurodaydeffilefilter.class.php");
require_once(BASE_PATH."includes/mapping/fftinfile.class.php");
require_once(BASE_PATH."includes/mapping/fftinfilefilter.class.php");
require_once(BASE_PATH."includes/mapping/dpddatafileid.class.php");
require_once(BASE_PATH."includes/mapping/dpddatafileidfilter.class.php");
require_once(BASE_PATH."includes/mapping/czdatafileid.class.php");
require_once(BASE_PATH."includes/mapping/czdatafileidfilter.class.php");
require_once(BASE_PATH."includes/mapping/hermesdatafileid.class.php");
require_once(BASE_PATH."includes/mapping/hermesdatafileidfilter.class.php");
require_once(BASE_PATH."includes/mapping/cpostmanifest.class.php");
require_once(BASE_PATH."includes/mapping/cpostmanifestfilter.class.php");
require_once(BASE_PATH."includes/mapping/czintdatafileid.class.php");
require_once(BASE_PATH."includes/mapping/czintdatafileidfilter.class.php");
require_once(BASE_PATH."includes/mapping/whistldepodetail.class.php");
require_once(BASE_PATH."includes/mapping/whistldepodetailfilter.class.php");
require_once(BASE_PATH."includes/mapping/cttdatafileid.class.php");
require_once(BASE_PATH."includes/mapping/cttdatafileidfilter.class.php");
require_once(BASE_PATH."includes/mapping/pbtdatafileid.class.php");
require_once(BASE_PATH."includes/mapping/pbtdatafileidfilter.class.php");
require_once(BASE_PATH."includes/mapping/parcelforcedatafileid.class.php");
require_once(BASE_PATH."includes/mapping/parcelforcedatafileIdfilter.class.php");
require_once(BASE_PATH."includes/mapping/correosbrazildatafile.class.php");
require_once(BASE_PATH."includes/mapping/correosbrazildatafilefilter.class.php");
require_once(BASE_PATH."includes/mapping/parcelforcedepodetail.class.php");
require_once(BASE_PATH."includes/mapping/parcelforcedepodetailfilter.class.php");
require_once(BASE_PATH."includes/mapping/parcelforcehubdetails.class.php");
require_once(BASE_PATH."includes/mapping/parcelforcehubdetailsfilter.class.php");
require_once(BASE_PATH."includes/mapping/pbtroutine.class.php");
require_once(BASE_PATH."includes/mapping/pbtroutinefilter.class.php");
require_once(BASE_PATH."includes/mapping/postnldatafileId.class.php");
require_once(BASE_PATH."includes/mapping/postnldatafileidfilter.class.php");

require_once(BASE_PATH."includes/mapping/serviceagentmapping.class.php");
require_once(BASE_PATH."includes/mapping/serviceagentmappingfilter.class.php");
require_once(BASE_PATH."includes/mapping/apis.class.php");
require_once(BASE_PATH."includes/mapping/importdataapis.class.php");
require_once(BASE_PATH."includes/mapping/ukmailauthentication.class.php");
require_once(BASE_PATH."includes/mapping/ukmailauthenticationfilter.class.php");
require_once(BASE_PATH."includes/mapping/consignmentrelabelfilter.class.php");
require_once(BASE_PATH."includes/mapping/consignmentrelabel.class.php");

require_once(BASE_PATH."includes/mapping/tracking.class.php");
require_once(BASE_PATH."includes/mapping/simple_html_dom.php");
require_once(BASE_PATH."includes/mapping/pickup.class.php");
require_once(BASE_PATH."includes/reamus/include_list.php");
require_once(BASE_PATH."includes/mapping/exportpalletlabel.class.php");
require_once(BASE_PATH."includes/mapping/Payment.class.php");
require_once(BASE_PATH."includes/mapping/Paymentmethod.class.php");
require_once(BASE_PATH."includes/mapping/paymentmethodfillter.class.php");
require_once(BASE_PATH."includes/mapping/mawb.class.php");
require_once(BASE_PATH."includes/mapping/mawbfilter.class.php");
require_once(BASE_PATH."includes/mapping/hawblog.class.php");
require_once(BASE_PATH."includes/mapping/hawblogfilter.class.php");
require_once(BASE_PATH."includes/mapping/box_info.class.php");
require_once(BASE_PATH."includes/mapping/paypaldumpfillter.class.php");
require_once(BASE_PATH."includes/mapping/paypaldump.class.php");
require_once(BASE_PATH."includes/mapping/feedback.class.php");
require_once(BASE_PATH."includes/mapping/feedbackfilter.class.php");
require_once(BASE_PATH."includes/mapping/include_list.php");
require_once(BASE_PATH."includes/library/ddl.inc.php");
require_once(BASE_PATH."includes/library/auditlogs.inc.php");

require_once(BASE_PATH."includes/mapping/marketplaceorder.class.php");
require_once(BASE_PATH."includes/mapping/marketplaceorderfilter.class.php");
require_once(BASE_PATH."includes/mapping/marketplaceorderdetails.class.php");
require_once(BASE_PATH."includes/mapping/marketplaceorderdetailsfilter.class.php");



require_once(BASE_PATH."includes/labels/yodel.class.php");
require_once(BASE_PATH."includes/labels/yodeltrackingstatus.class.php");

require_once(BASE_PATH."includes/labels/dhl.class.php");
require_once(BASE_PATH."includes/labels/dhltrackingstatus.class.php");

require_once(BASE_PATH."includes/labels/swedenpost.class.php");
require_once(BASE_PATH."includes/labels/swedenposttrackingstatus.class.php");

require_once(BASE_PATH."includes/labels/deutschepost.class.php");
require_once(BASE_PATH."includes/labels/deutscheposttrackingstatus.class.php");

require_once(BASE_PATH."includes/mapping/palletentitymappingfilter.class.php");
require_once(BASE_PATH."includes/labels/cn22.class.php");

require_once(BASE_PATH."includes/labels/ukp.class.php");

require_once(BASE_PATH."includes/labels/asendiauk.class.php");
require_once(BASE_PATH."includes/labels/asendiatrackingstatus.class.php");

require_once(BASE_PATH."includes/mapping/assignvehicle.class.php");
require_once(BASE_PATH."includes/mapping/assignvehiclefilter.class.php"); 

require_once(BASE_PATH."includes/labels/wndirect.class.php");
require_once(BASE_PATH."includes/labels/wndirecttrackingstatus.class.php");

require_once(BASE_PATH."includes/labels/viva.class.php");
//require_once(BASE_PATH."includes/labels/asendiatrackingstatus.class.php");

*/