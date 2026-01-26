<?php
// get settings
require_once("../includes/settings/config.inc.php");
class Page extends BasePage
{
    private $trackingResults;
    private $shipmentDetail;
    private $trackingEvents;
    private $trackingStatus;
    private $trackingStatusDescription;
    /*     * *
     * Controller logic
     */
    protected function init()
    {
    }
    /*     * *
     * Insert content in to HTML Head section
     */
    protected function renderHead()
    {
        ?>
        <link href="/assets/layouts/layout4/css/custom.css" rel="stylesheet" type="text/css"/>
        <?php
    }
    /*     * *
     * Content View
     */
    protected function renderBody()
    {
        //echo "<pre>"; print_r($this->trackingResults); echo "</pre>";
        ?>
        <br clear="all">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"><i class="fa fa-upload"></i>
                    ONE WORLD EXPRESS INC. LIMITED TERMS AND CONDITIONS OF CARRIAGE (“Terms and Conditions”)
                </div>
            </div>
            <div class="portlet-body">
<h3>IMPORTANT NOTICE</h3>
<p>
When ordering One World Express Inc. Limited’s services you, as “Shipper”, are agreeing, on your behalf and on behalf of any other third party directly or indirectly employed, with an interest in the Shipment, that the Terms and Conditions shall apply from the time that One World Express Inc. Limited accepts the Shipment unless otherwise agreed in writing by an authorised officer of One World Express Inc. Limited. The Shipper’s statutory rights and entitlements under any defined service feature (for which additional payment has been made) are not affected.
“Shipment” means all documents or parcels that travel under one waybill and which may be carried by any means One World Express Inc. Limited chooses, including air, road or any other carrier. A “waybill” shall include any label produced by One World Express Inc. Limited automated systems, air waybill, or consignment note and shall incorporate these Terms and Conditions. Every Shipment is transported on a limited liability basis as provided herein. If Shipper requires greater protection, then insurance may be arranged at an additional cost (Please see below for further information). “One World Express Inc. Limited” means any member of the ONE WORLD EXPRESS INC LIMITED Network.
</p>
<div class="panel-group accordion" id="accordion3">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_1"> Customs, Exports and Imports </a>
                                                </h4>
                                            </div>
                                            <div id="collapse_3_1" class="panel-collapse in">
                                                <div class="panel-body">
                                                    <p> One World Express Inc. Limited may perform any of the following activities on Shipper’s behalf in order to provide its services to Shipper: (1) complete any documents, amend product or service codes, and pay any duties or taxes required under applicable laws and regulations, (2) act as Shipper’s forwarding agent for customs and export control purposes and as Receiver solely for the purpose of designating a customs broker to perform customs clearance and entry and (3) redirect the Shipment to Receiver’s import broker or other address upon request by any person who One World Express Inc. Limited believes in its reasonable opinion to be authorised.
                                                        </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_2"> Unacceptable Shipments </a>
                                                </h4>
                                            </div>
                                            <div id="collapse_3_2" class="panel-collapse collapse">
                                                <div class="panel-body">
                                                    <p>Shipper agrees that its Shipment is acceptable for transportation and is deemed unacceptable if:</p>
                                                    <ul>
<li>it is classified as hazardous material, dangerous goods, prohibited or restricted articles by IATA (International Air Transport Association), ICAO (International Civil Aviation Organisation), any applicable government department or other relevant organisation;</li>
<li>no customs declaration is made when required by applicable customs regulations; or</li>
<li>a postal item does not comply with the “Postal Export Guide” published by any designated postal operator; or does not comply with the UPU regulations either for letter, or parcel post items; or</li>
<li>One World Express Inc. Limited decides it cannot transport an item safely or legally (such items include but are not limited to: animals, bullion, currency, bearer form negotiable instruments, precious metals and stones, firearms, parts thereof and ammunition, human remains, pornography and illegal narcotics/drugs);</li>
<li>and any other material which may not be carried under UK, EU or International Law not withstanding items which may not be acceptable by any third party carrier under their terms and conditions (Please check this prior to shipping).</li>
</ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_3"> Deliveries & Undeliverables</a>
                                                </h4>
                                            </div>
                                            <div id="collapse_3_3" class="panel-collapse collapse">
                                                <div class="panel-body">
<p>Shipments cannot be delivered to PO Boxes or postal codes. Shipments are delivered to the&nbsp;Receiver’s address given by Shipper (which in the case of mail services shall be deemed to be the first receiving postal service) but not necessarily to the named Receiver personally. Shipments to addresses with a central receiving area will be delivered to that area. If Receiver refuses delivery or to pay for delivery, or the Shipment is deemed to be unacceptable, or it has been undervalued for customs purposes, or Receiver cannot be reasonably identified or located, One World Express Inc. Limited shall use reasonable efforts to return the Shipment to Shipper at Shipper’s cost failing which the Shipment may be released, disposed of or sold by One World Express Inc. Limited without incurring any liability whatsoever to Shipper or anyone else, with the proceeds applied against service charges and related administrative costs and the balance of the proceeds of a sale to be returned to Shipper. Delivery;</p>
<p>a. The parties of these terms and conditions will agree in writing the dates and times on or after which One World Express Inc. Limited will deliver or collect your goods (the scheduled delivery date). Those dates and times are only estimates and not guarantees of delivery days or times no matter the supplier or what is verbally agreed by any member of One World Express Inc. Limited or any carrier.</p>
<p>b. It is the obligation of the Shipper to inform the person the goods are being delivered to what the scheduled delivery date is and make sure we can make the delivery then.</p>
<p>c. One World Express Inc. Limited will try to deliver the goods during the scheduled delivery date and to the address marked on the goods. One World Express Inc. Limited will not accept liability for any loss suffered as a result of a delay in us delivering or us failing to deliver, except as set out in these conditions, and One World Express Inc. Limited liability will be limited by these conditions (see clauses 2, 5, 6, 7, 9, 10)</p>
<p>d. If One World Express Inc. Limited cannot deliver the goods at first try to, One World Express Inc. Limited will charge £6.50 or half the original charge, whichever is greater, every other time One World Express Inc. Limited try to deliver the goods, or:</p>
<ul>
<li>One World Express Inc. Limited can choose to store, return or dispose of the goods; and</li>
<li>The Shipper must pay our costs for holding, returning or keeping the goods as soon as One World Express Inc. Limited ask for those costs.</li>
</ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_4"> Inspection </a>
                                                </h4>
                                            </div>
                                            <div id="collapse_3_4" class="panel-collapse collapse">
                                                <div class="panel-body">
<p>a. One World Express Inc. Limited has legal rights to keep the goods One World Express Inc. Limited are carrying for the Shipper until Shipper pay One World Express Inc. Limited all money due to One World Express Inc. Limited in connection with the goods and any other money Shipper owe One World Express Inc. Limited.</p>
<p>b. If Shipper does not pay One World Express Inc. Limited any money Shipper owe One World Express Inc. Limited within one calendar month of One World Express Inc. Limited giving Shipper notice that One World Express Inc. Limited are keeping hold of SHIPPER goods under clause 4a above, One World Express Inc. Limited may sell the goods as One World Express Inc. Limited choose. After One World Express Inc. Limited has taken costs of selling the goods, One World Express Inc. Limited will put any remaining proceeds towards any amount due to One World Express Inc. Limited. This does not affect One World Express Inc. Limited right to recover any amount remaining from Shipper.</p>
<p>c. One World Express Inc. Limited can also dispose of the goods by selling or disposing of them in any other way One World Express Inc. Limited consider suitable if: Shipper goods (or part of them) have perished, deteriorated or altered, or are likely to do so in the immediate future; or One World Express Inc. Limited have not been able to deliver Shipper goods for any of the reasons set out in section 3 and One World Express Inc. Limited have held Shipper goods for 10 days; and One World Express Inc. Limited have made reasonable efforts to contact anyone who could reasonably have an interest in Shipper goods.</p>
<p>d. When One World Express Inc. Limited ask, Shipper must immediately pay One World Express Inc. Limited all costs, charges and expenses for storing and disposing of the goods or any part of them.</p>
<p>e One World Express Inc. Limited will give Shipper credit for any amount left from the proceeds of One World Express Inc. Limited selling Shipper goods after One World Express Inc. Limited have taken any amounts Shipper owe One World Express Inc. Limited and any of One World Express Inc. Limited costs, charges or expenses.</p>
<p>f. If One World Express Inc. Limited have already settled a claim Shipper have made for lost goods which are then found, One World Express Inc. Limited can dispose of those goods as One World Express Inc. Limited see fit and keep the proceeds.</p>
<p>g. The rights set out in this clause are in addition to any other legal rights One World Express Inc. Limited may have.</p>
                                                </div>
                                            </div>
                                        </div>
  <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3-5"> Shipment Charges & Billing </a>
                                                </h4>
                                            </div>
                                            <div id="collapse_3-5" class="panel-collapse collapse">
                                                <div class="panel-body">
<p>One World Express Inc. Limited’s Shipment charges are calculated according to the higher of actual or volumetric weight and any Shipment may be re-weighed and re-measured by One World Express Inc. Limited to confirm this calculation. Shipper shall pay or reimburse One World Express Inc. Limited for all Shipment charges, storage charges, duties and taxes owed for services provided by One World Express Inc. Limited or incurred by One World Express Inc. Limited on Shipper’s or Receiver’s or any third party’s behalf and all claims, damages, fines and expenses incurred if the Shipment is deemed unacceptable for transport as described in Section</p>
<p>2. Payment terms;</p>
<p>a. Shipper must pay all One World Express Inc. Limited invoices, by direct debit, within 14 days of the date of the invoice unless agreed alternate methods of payments have been arranged.a. Shipper must pay all One World Express Inc. Limited invoices, by direct debit, within 14 days of the date of the invoice unless agreed alternate methods of payments have been arranged.</p>
<p>b. One World Express Inc. Limited can invoice Shipper at any time on or after the last working day in the month One World Express Inc. Limited provide the relevant service.</p>
<p>c. If One World Express Inc. Limited do not receive the payment for any invoice on time, One World Express Inc. Limited can charge interest cumulatively at the rate of 2.5% a calendar month or £10 a calendar month, whichever is greater. One World Express Inc. Limited will count any part of a calendar month as a full calendar month.</p>
<p>d. One World Express Inc. Limited operate under a minimum invoice value of £50 per invoice. Shipper will be required to pay this minimum amount if the charge for the service falls under the£50 threshold.</p>
<p>e. Shipper must pay One World Express Inc. Limited all amounts Shipper owe One World Express Inc. Limited without taking off any amount, and Shipper must not put off paying One World Express Inc. Limited because of any claim Shipper are making against One World Express Inc. Limited or any amount Shipper think One World Express Inc. Limited owe Shipper.</p>
<p>f. One World Express Inc. Limited can immediately increase One World Express Inc. Limited charges from time to time to reflect any increase in the costs One World Express Inc. Limited have to pay to provide the service (for example, increased costs of fuel and congestion charges), provided One World Express Inc. Limited give Shipper notice within a reasonable time of that increase.</p>
<p>g. For details of One World Express Inc. Limited charges please contact One World Express Inc. Limited. The charges One World Express Inc. Limited quote do not include VAT, local taxes and custom duties, which Shipper might also have to pay. The charges One World Express Inc. Limited quote assume that Shipper will accept them immediately, and One World Express Inc. Limited can withdraw or change any quoted charge with or without giving Shipper notice.</p>
<p>h. One World Express Inc. Limited will apply an extra charge for delivering to mail order companies or any other party who needs us to book in the delivery or wait to make a delivery(Waiting Charge) or for deliveries to exhibitions or conferences whether disclosed or not.</p>
                                                </div>
                                            </div>
                                        </div>
  <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3-6">One World Express Inc. Limited’s Liability</a>
                                                </h4>
                                            </div>
                                            <div id="collapse_3-6" class="panel-collapse collapse">
                                                <div class="panel-body">
<p>One World Express Inc. Limited contracts with Shipper on the basis that One World Express Inc.&nbsp;Limited’s liability is strictly limited to direct loss only and to the per kilogram limits in this Section</p>
<p>6. All other types of loss or damage are excluded (including but not limited to lost profits, income, interest, future business), whether such loss or damage is special or indirect, and even if the risk of such loss or damage was brought to One World Express Inc. Limited’s attention before or after acceptance of the Shipment since special risks can be insured by Shipper. If a Shipment combines carriage by air, road or other mode of transport, it shall be presumed that any loss or damage occurred during the air period of such carriage unless proven otherwise. One World Express Inc. Limited’s liability in respect of any one Shipment transported, without prejudice to Sections 7-11, is limited to its actual cash value and shall not exceed the greater of £50.00 or:6. All other types of loss or damage are excluded (including but not limited to lost profits, income, interest, future business), whether such loss or damage is special or indirect, and even if the risk of such loss or damage was brought to One World Express Inc. Limited’s attention before or after acceptance of the Shipment since special risks can be insured by Shipper. If a Shipment combines carriage by air, road or other mode of transport, it shall be presumed that any loss or damage occurred during the air period of such carriage unless proven otherwise. One World Express Inc. Limited’s liability in respect of any one Shipment transported, without prejudice to Sections 7-11, is limited to its actual cash value and shall not exceed the greater of £50.00 or:</p>
<ul>
<li>£10.00/kilogram for Shipments transported by air or other non-road mode of transportation; or</li>
<li>£5.00/kilogram for Shipments transported by road (not applicable to the US).</li>
</ul>
<p>One World Express Inc. Limited’s liability in respect of any one Shipment transported within the boundaries of the United Kingdom, is limited to £5.00/kilogram or the Invoice value whichever is lower.</p>
<p>This Section 6 is without prejudice to Sections 7-11.</p>
<p>Claims are limited to one claim per Shipment settlement of which will be full and final settlement for all loss or damage in connection therewith. If Shipper regards these limits as insufficient it must make a special declaration of value and request insurance as described in Clause 8<br>
(Shipment Insurance) or make its own insurance arrangements, failing which Shipper assumes all risks of loss or damage.</p>
<p>Shipper will be responsible for protecting One World Express Inc. Limited from, not holding One World Express Inc. Limited responsible for and paying any liabilities, claims, loss, damage, fines, costs or expenses arising from:</p>
<ul>
<li>One World Express Inc. Limited following Shipper’s instructions;</li>
<li>One World Express Inc. Limited following Shipper’s instructions;</li>
<li>Shipper breaking any of these conditions;</li>
<li>Shipper’s negligence;</li>
<li>Insufficient or incorrect packing;</li>
<li>Shipping any prohibited or dangerous material as set out in section 2; or</li>
<li>any and all duties, taxes and so on charged by any authority together with all payments, fines, costs, expenses, loss or damage relating to Shipper’s goods;</li>
<li>damages caused by third parties or any accidental damage caused during transit beyond either One World Express Inc. Limited carriers or One World Express Inc. Limited control.</li>
</ul>
<p>b. Shipper must pay any claim under clause 6a above within seven days of the date of the relevant invoice. After seven days One World Express Inc. Limited will treat the claim as a payment due to One World Express Inc. Limited under clause 5 of these conditions.</p>
                                                </div>
                                            </div>
                                        </div>
  <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3-7">Time Limits for Claims</a>
                                                </h4>
                                            </div>
                                            <div id="collapse_3-7" class="panel-collapse collapse">
                                                <div class="panel-body">
<p>All claims must be submitted in writing to One World Express Inc. Limited within ten (10) days from the date that One World Express Inc. Limited accepted the Shipment failing which One World Express Inc. Limited nor its suppliers or carriers shall have no liability whatsoever.</p>
                                                </div>
                                            </div>
                                        </div>
  <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3-8">Shipment Insurance</a>
                                                </h4>
                                            </div>
                                            <div id="collapse_3-8" class="panel-collapse collapse">
                                                <div class="panel-body">
<p>One World Express Inc. Limited can arrange insurance for Shipper covering the actual cash value in respect of loss of or physical damage to the Shipment, provided the Shipper completes the insurance section on the front of the waybill or requests it via One World Express Inc. Limited’s automated systems and pays the applicable premium. Shipment insurance does not cover indirect loss or damage, or loss or damage caused by delays.
Insurance cover is not guaranteed for every product range, One World Express Inc. Limited recommend that before Shipper ship any product requiring insurance cover that Shipper contact the team who can advise and quote based on Shipper’s requirement.</p>
                                                </div>
                                            </div>
                                        </div>
  <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3-9">Delayed Shipments</a>
                                                </h4>
                                            </div>
                                            <div id="collapse_3-9" class="panel-collapse collapse">
                                                <div class="panel-body">
<p>One World Express Inc. Limited will make every reasonable effort to deliver the Shipment according to One World Express Inc. Limited’s regular delivery schedules, but these are not guaranteed and do not form part of the contract. One World Express Inc. Limited is not liable for any damages or loss caused by delays.</p>
                                                </div>
                                            </div>
                                        </div>
  <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3-10">Delayed Shipments</a>
                                                </h4>
                                            </div>
                                            <div id="collapse_3-10" class="panel-collapse collapse">
                                                <div class="panel-body">
<p>One World Express Inc. Limited will make every reasonable effort to deliver the Shipment according to One World Express Inc. Limited’s regular delivery schedules, but these are not guaranteed and do not form part of the contract. One World Express Inc. Limited is not liable for any damages or loss caused by delays.</p>
                                                </div>
                                            </div>
                                        </div>
  <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3-11">Circumstances beyond One World Express Inc. Limited’s control</a>
                                                </h4>
                                            </div>
                                            <div id="collapse_3-11" class="panel-collapse collapse">
                                                <div class="panel-body">
<p>One World Express Inc. Limited is not liable for any loss or damage arising out of circumstances beyond One World Express Inc. Limited’s control. These include but are not limited to:- “Act of God” – e.g. earthquake, cyclone, storm, flood, fog; “Force Majeure” – e.g. war, plane crash or embargo; any defect or characteristic related to the nature of the Shipment, even if known to One World Express Inc. Limited; riot or civil commotion; any action or omission by a person not employed or contracted by One World Express Inc. Limited e.g. Shipper, Receiver, third party, Customs or other Government official; industrial action; and electrical or magnetic damage to, or erasure of, electronic or photographic images, data or recordings.</p>
                                                </div>
                                            </div>
                                        </div>
  <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3-12">Warsaw Convention</a>
                                                </h4>
                                            </div>
                                            <div id="collapse_3-12" class="panel-collapse collapse">
                                                <div class="panel-body">
<p>If the Shipment is transported by air and involves an ultimate destination or stop in a country other than the country of departure, the Warsaw Convention (“Warsaw”), if applicable, governs and limits One World Express Inc. Limited’s liability for loss or damage.</p>
                                                </div>
                                            </div>
                                        </div>
  <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3-13">Shipper’s Warranties and Indemnity</a>
                                                </h4>
                                            </div>
                                            <div id="collapse_3-13" class="panel-collapse collapse">
                                                <div class="panel-body">
<p>Shipper shall indemnify and hold One World Express Inc. Limited harmless for any loss or damage arising out of Shipper’s failure to comply with any applicable laws or regulations and for Shipper’s breach of the following warranties and representations:</p>
<ul>
<li>all information provided by Shipper or its representatives is complete and accurate;</li>
<li>all information provided by Shipper or its representatives is complete and accurate;</li>
<li>the Shipment was prepared in secure premises by Shipper’s employees;</li>
<li>Shipper employed reliable staff to prepare the Shipment;</li>
<li>Shipper protected the Shipment against unauthorised interference during preparation, storage and transportation to One World Express Inc. Limited;</li>
<li>the Shipment is properly marked and addressed and packed to ensure safe transportation with ordinary care in handling;</li>
<li>all applicable customs, import, export and other laws and regulations have been complied with; and</li>
<li>the waybill has been signed by Shipper’s authorised representative and the Terms and Conditions constitute binding and enforceable obligations of Shipper.</li>
<li>the address you provide on our label or waybill is final and any errors in details cannot be directed to One World Express Inc. Limited its carriers, partners or employee’s, this is the sole responsibility of the Shipper and all costs relating to relabeling, re-direction, service type/code or loss as a result of any other error of input, upload or other on your part is your responsibility. One World Express Inc. Limited will not be held liable for any costs associated with your error/s and will require full payment for any of the above (see section 3, 6).</li>
</ul>
                                                </div>
                                            </div>
                                        </div>
  <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3-14">Routing</a>
                                                </h4>
                                            </div>
                                            <div id="collapse_3-14" class="panel-collapse collapse">
                                                <div class="panel-body">
<p>Shipper agrees to all routing and diversion, including the possibility that the Shipment may be carried via intermediate stopping places.
</p>
                                                </div>
                                            </div>
                                        </div>
  <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3-15">Governing Law</a>
                                                </h4>
                                            </div>
                                            <div id="collapse_3-15" class="panel-collapse collapse">
                                                <div class="panel-body">
<p>Any dispute arising under or in any way connected with these Terms and Conditions shall be subject, for the benefit of One World Express Inc. Limited, to the non-exclusive jurisdiction of the courts of, and governed by the law of, the country of origin of the Shipment and Shipper irrevocably submits to such jurisdiction, unless contrary to applicable law.
</p>
                                                </div>
                                            </div>
                                        </div>
  <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3-16">Severability</a>
                                                </h4>
                                            </div>
                                            <div id="collapse_3-16" class="panel-collapse collapse">
                                                <div class="panel-body">
<p>The invalidity or unenforceability of any provision shall not affect any other part of these Terms and Conditions.
</p>
                                                </div>
                                            </div>
                                        </div>
  <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3-17">Carrier Terms and conditions</a>
                                                </h4>
                                            </div>
                                            <div id="collapse_3-17" class="panel-collapse collapse">
                                                <div class="panel-body">
<p>In addition to these terms and conditions, it is accepted that both you and we are bound by all service carriers terms and conditions which may supersede these terms and conditions where applicable. Copies of all Carrier terms and conditions are available on the carrier’s websites or available on request from One World Express Inc. Limited.
</p>
                                                </div>
                                            </div>
                                        </div>
  <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3-18">Surcharges</a>
                                                </h4>
                                            </div>
                                            <div id="collapse_3-18" class="panel-collapse collapse">
                                                <div class="panel-body">
<p>Surcharges apply to all tariff cards or agreed rates either written or verbal. These surcharges are applicable to;
</p>
<ul>
<li>Fuel and War</li>
<li>Delivery, all modes</li>
<li>Remote areas; Includes Highlands, Islands, Other remote areas or zones disclosed or other</li>
<li>Timed deliveries</li>
<li>Handling, re-packaging, re-sorting, cross-sorting and Labelling, including re-labelling</li>
<li>Returns, including handling</li>
<li>Waiting time, bookings (see section3)</li>
<li>Outsized, out of gauge, out of girth, overweight or undersized</li>
<li>Dangerous or hazardous material, whether declared or undeclared</li>
<li>Any other surcharges not set out in this section but which may be covered by specific tariff or service guides.</li>
</ul>
<p>
Any and all charges set out under this clause or any other agreed rate or terms and conditions are subject to change with or without notice.
</p>
                                                </div>
                                            </div>
                                        </div>
  <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3-19">Claims against Shipper</a>
                                                </h4>
                                            </div>
                                            <div id="collapse_3-19" class="panel-collapse collapse">
                                                <div class="panel-body">
<p>One World Express Inc. Limited reserve the right to make claim against the shipper, consignor or undersigned either corporate or personally for any damage to One World Express Inc. Limited, its employees or carriers in the event that Shipper knowingly or unknowingly ship any material which is hazardous or dangerous or which may cause harm to anyone or anything while in One World Express Inc. Limited’s care. One World Express Inc. Limited care refers to goods in transit, processed in One World Express Inc. Limited’s warehouse or through any of One World Express Inc. Limited’s third party carriers. One World Express Inc. Limited reserves the right to re-invoice Shipper for a period of up to seven (7) years post fact for any un-billed, incorrectly billed or outstanding shipping, duty, taxes, clearing fees, fines, out of gauge, outsized, oversized, undersized consignments, remote area or other surcharges or any other un-billed item or in the event that Shipper shipped material through One World Express Inc. Limited services and knowingly under-valued or mis-declared the material intrinsic value for customs purposes. Should Shipper, Shipper’s company or other refute these claims One World Express Inc. Limited reserves the right to take full legal action against Shipper to recover any lost or outstanding revenue from Shipper’s company or other entity related to Shipper. One World Express Inc. Limited reserves the right to report or blacklist Shipper, Shipper’s company or other entity with any and all necessary public authorities, this includes; airlines, Customs and excise, other shipping and logistics carriers, partners, agents and any other public or private entity relating to One World Express Inc. Limited business.
</p>
                                                </div>
                                            </div>
                                        </div>
  <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3-20">Data Protection</a>
                                                </h4>
                                            </div>
                                            <div id="collapse_3-20" class="panel-collapse collapse">
                                                <div class="panel-body">
<p>One World Express Inc. Limited processes data provided through any party and has the right to transmit this data on Shipper’s behalf to ensure that any and all shipment related item identification (i.e. labels) and other documentation can be created for use in its SMARTTRACK™ system. By accepting its use Shipper indemnify One World Express Inc. Limited for any wrong doing for any and all data transmissions pertaining to that data. One World Express Inc. Limited acknowledges and abides by the UK and EU Data Protection Act 1998/2003 to ensure that all data is used fairly and lawfully, for stated purposes which is adequate and not excessive, accurate and not modified in any way, kept no longer than is required or necessary for the use of said data (depending on the data and the requirement this may be held indefinitely), handled in accordance with all necessary data protection rights, kept safe and secure and transmitted securely when required. In the case One World Express Inc. Limited processes, encompasses the storage, amends, transfers, blocks or erasures, personal data on behalf of the Shipper, One World Express Inc. Limited and Shipper enters into a contract of data processing on behalf of the Shipper. This contract specifies the data protection obligations of One World Express Inc. Limited (acting as processor) and the Shipper (acting as controller), which applies to all activities performed in connection with the main contractual obligations, stated in these terms and conditions, in which the staff of One World Express Inc. Limited or a third party acting on behalf of the One World Express Inc. Limited may come into contact with personal data of the Shipper. “Personal Data” means any individual element of information concerning the personal or material circumstances of an identified or identifiable individual.
Relevant Data which is transferred to non-EU entities is required for the purposes of creating and authenticating international services using the SMARTTRACK™ system, by signing this document and related contract in relation of data processing on behalf of the Shipper, Shipper authorises One World Express Inc. Limited to use any and all of Shipper’s or Shipper’s customers personal data to ensure the smooth and expedient delivery of Shipper or Shipper’s customers postal or parcel items. One World Express Inc. Limited reserves the right to share this data for marketing, training, analytical, statistical and other purposes – this data may include, names, addresses, contact information such as email address, phone number and company information.
</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>








<p>Download PDF for signature:&nbsp;<a href="http://www.oneworldexpress.com/wp-content/uploads/2017/07/Terms-and-conditions_2017.pdf" style="color: e22227;">Terms and conditions_2017</a></p>


<p><span style="color: #231f20;"><strong>Important.&nbsp;</strong></span></p>
<p><strong>Please note that all pages on these terms and conditions of carriage must be read and initialed and dated by the customer or their representative and a copy of identification be attached. </strong></p>
<p><strong>All terms and conditions subject to change without notice, all terms set out herein are binding unless superseded or where a carrier terms and conditions differ from those described herein, One World Express Inc. Limited reserves the right to choose which terms and conditions are satisfied and those terms will be binding under applicable law. </strong></p>
<p><strong>E &amp; O.E. </strong></p>




            </div>
        </div>
        <?php
    }
    /**
     * Return to source page
     * @param none
     */
    /**
     * Return to source page
     * @param $filter_set
     */
    public function renderMenu()
    {
        if (isset($_SESSION['admin'])) {
            $menu = new Adminmenu(Adminmenu::COURIERS);
            $menu->render();
        }
    }
    protected function addPagelavelCss()
    {
        ?>
        <link href="../assets/layouts/layout4/css/multitrack.css" rel="stylesheet">
        <style type="text/css">
            .page-breadcrumb {
                display: none
            }
            .icon-status-box {
                text-align: center;
                padding: 3em 0 0 0;
                width: 100%
            }
            .icon-status-box .fa {
                font-size: 3em;
                text-align: center;
                color: #26C281;
            }
            @media (min-width: 992px) {
                .page-content-wrapper .page-content {
                    padding-top: 0px !important;
                }
            }
            .page-header.navbar .page-logo .logo-default {
                margin: 17px 10px 0 !important;
                max-width: 180px !important;
                max-height: 43px !important;
            }
            .page-sidebar-hide {
                margin-left: 0px !important;;
                padding-left: 0px !important;
            }
            .dashboard-stat2 h3 {
                font-size: 24px !important;
            }
        </style>
        <?php
    }
    protected function addPagelavelJs()
    {
        ?>
        <script src="../assets/global/plugins/jquery-knob/js/jquery.knob.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/components-knob-dials.min.js" type="text/javascript"></script>
        <?php
        if (!isset($_SESSION['admin'])) {
            ?>
            <script type="text/javascript">
                $(document).ready(function () {
                    $(".page-content-wrapper > .page-content").addClass('page-sidebar-hide');
                    $(".sidebar-toggler").hide();
                })
            </script>
            <?php
        }
    }
}
/* ------------------------------------------------------------------------------ */
// create and render page
//$page = new Page("noheader");
//$page->show();
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();
?>