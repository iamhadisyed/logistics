<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ServicesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('services')->delete();
        
        \DB::table('services')->insert(array (
            0 => 
            array (
                'id' => 226,
                'name' => 'Next Day Delivery',
                'code' => 'AMZ001',
                'carrier_id' => 16,
                'account_number' => NULL,
                'type' => 'ALL',
                'from_weight' => '0.000',
                'to_weight' => '300.000',
                'wieght_type' => 1,
                'supplier' => NULL,
                'service_type' => 'D',
                'drop_off_service_id' => 307,
                'description' => '<div class="container1">


<div class="row1 margin-bottom-10">
<div class="col-md-2">
<div class="inset-details">
<img ng-src="/images/carrierlogo/thumbnail/owe_100_yodel_1508334978.png" alt="Yodel" class="hidden-xs" 

src="/images/carrierlogo/thumbnail/owe_100_yodel_1508334978.png">

</div>
</div>
<div class="col-md-8">
<h2 class="palette-primary ng-binding">YODEL 24</h2>
<!-- ngIf: quote.Service.IsDropOff -->
<div>
<!-- ngIf: workingDays == 1 --><span ng-if="workingDays == 1" class="ng-scope">
Delivery within <span class="palette-primary">1 working day</span>
</span><!-- end ngIf: workingDays == 1 -->
<!-- ngIf: workingDays != 1 -->
</div>
</div>

<br clear="all"/ class="margin-bottom-10">
</div>



<div class="row1">
<div>

<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
<li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">More 

Info</a></li>
<li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Printer</a></li>

<!-- <li role="presentation"><a href="#settings" aria-controls="settings" role="tab" data-

toggle="tab">Settings</a></li> -->
</ul>

<!-- Tab panes -->
<div class="tab-content">
<div role="tabpanel" class="tab-pane active" id="home">


<div class="row1">


<div class="col-sm-6">
<h3 class="text-primary">Service Details</h3>
<span ng-bind-html="quote.Service.Description | html" class="ng-binding">

Parcel delivery within 1 working day throughout the most of UK\'s mainland. Deliveries are 
not guaranteed but enjoy over 98% success. Delivery Service time are between 8am â€“ 6pm.
<br>
<br>
<b>About Correct Weight and Dimension at all times:</b></br> 
Failure to do so administrative penalty as well as additional charges will be applicable.</span>
</div>
<div class="col-sm-6">
<h3 class="text-primary">Key Features</h3>
<ul class="fa-ul margin-none">
<li ng-show="quote.Service.TimedDeliveryTime" class="ng-hide"><i class="fa-li fa fa-check palette-

primary"></i> Delivery by <strong class="ng-binding"></strong></li>
<li ng-show="quote.Service.IsDropOff" class="ng-hide"><i class="fa-li fa fa-check palette-primary"></i> 

Drop Off service</li>
<li><i class="fa-li fa fa-check palette-primary"></i>Â£20.00 inclusive cover</li>                
<li><i class="fa-li fa fa-check palette-primary"></i>Fully tracked service</li>
<li><i class="fa-li fa fa-check palette-primary"></i>From UK to UK </li>
<li><i class="fa-li fa fa-check palette-primary"></i> Door to Door Delivery</li>
<li ng-show="quote.Service.DeliveryAlert" class="ng-hide"><i class="fa-li fa fa-check palette-
primary"></i> SMS alert optional</li>
<li class="ng-binding"><i class="fa-li fa fa-check palette-primary"></i> Protect your parcel up to the 

value of Â£2,500.00</li>
</ul>
<hr/>
<h3 class="text-primary">Restrictions</h3>
<ul class="fa-ul margin-none">
<!-- ngIf: !quote.Service.MoreInfo --><li ng-if="!quote.Service.MoreInfo" class="ng-scope"><i class="fa-li 

fa fa-ban palette-primary"></i> <a target="_blank" href="http://www.oneworldexpress.com/how-to/ship-prohibited-items/">Check the prohibited items list</a></li><!-- 

end ngIf: !quote.Service.MoreInfo -->
<li class="ng-binding"><i class="fa-li fa fa-balance-scale palette-primary"></i> Maximum Weight 25 kg</li>
<li class="ng-binding"><i class="fa-li fa fa-expand palette-primary"></i> Maximum Length 1.2 m</li>

</ul>
</div>
</div>




</div>
<div role="tabpanel" class="tab-pane" id="profile">
<div class="row1">
<center><i class="fa fa-print fa-5x"></i></center>
<div class="col-md-12">

<h3 class="text-primary">Printer</h3>
With this service you will need to print your label, so having access to a printer is essential. An A4 

printer will suffice for all label printing.
Alternatively on the order confirmation page you can choose on of the following options for your printing 

needs:


<hr>
<ul class="fa-ul margin-none">
<li>     <i class="fa-li fa fa-check palette-primary"></i>  4x6 label printer </li>
<li>     <i class="fa-li fa fa-check palette-primary"></i> A4 printer (4x 4x6 labels)  </li>
<li> <i class="fa-li fa fa-check palette-primary"></i> Address labels (A4 sheet)  </li>

</ul>


<br clear="all"/>
</div>
</div>
</div>

<!-- <div role="tabpanel" class="tab-pane" id="settings">...</div> -->
</div>

</div>

</div>
<br clear="all"/>
</div>',
                'fuel_surcharge_cost' => NULL,
                'fuel_surcharge' => '0.00',
                'fuel_surcharge_type' => '',
                'max_length' => '50.00',
                'max_width' => '45.00',
                'max_height' => '45.00',
                'max_volumetric_weight' => '30.00',
                'volumetric_denominator' => 5000,
                'send_data_courier' => 0,
                'is_document' => 0,
                'friday_only_flag' => 0,
                'saturday_only_flag' => 0,
                'sunday_only_flag' => 0,
                'product_owner' => NULL,
                'active' => 1,
                'deletedq' => 0,
                'added_on' => NULL,
                'added_by' => '58',
                'changed_on' => '2020-02-06 10:03:53',
                'changed_by' => '58',
                'uploaded_currency' => 'GBP',
                'uploaded_currency_value' => '1.00',
                'registration_fee' => NULL,
                'weight_after' => '0.00',
                'aditional_charge' => NULL,
                'origin_country' => 225,
                'is_untrack' => 0,
                'account_owner' => 175,
                'remotearea' => 'ON_PIECE',
                'carrier_address_limit' => 35,
                'label_class_name' => 'amazonshipping',
                'transit_time' => NULL,
                'required_email' => 0,
                'required_telephone' => 0,
                'shipment_type' => 'PARCEL',
                'pre_sort' => 'NO',
                'proforma_invoice' => 0,
                'agent_dispatch' => 'N',
                'brief_manifest' => 'N',
                'delivery_mode' => NULL,
                'insurance_available' => 0,
                'vol_wgt_formula' => 'L*W*H/D',
                'is_remotearea' => 'N',
                'is_customized' => 0,
                'pre_advise' => 'N',
                'pre_alert' => 'N',
                'pre_alert_email' => '',
                'cut_off_time' => '',
                'label_charges' => NULL,
                'allow_oversize' => 0,
                'allow_overweight' => 1,
                'maximum_allowed_dimension' => 120,
                'maximum_dim_formula' => 'L+W+H',
                'validation_type' => 'courier',
                'zone_type' => 'country',
                'tariff_type' => 'single',
                'girth' => NULL,
                'girth_formula' => '',
                'mail_type' => NULL,
                'mail_option' => NULL,
                'is_reschedulable' => 0,
                'carrier_service_code' => 'SWA-UK-PREM',
                'is_eori_required' => 0,
                'delivery_type' => 'all',
                'is_commercials' => 'not required',
                'is_cn' => 'not required',
            ),
            1 => 
            array (
                'id' => 333,
                'name' => 'Two Day Delivery',
                'code' => 'AMZ002',
                'carrier_id' => 16,
                'account_number' => '',
                'type' => 'ALL',
                'from_weight' => '0.000',
                'to_weight' => NULL,
                'wieght_type' => 2,
                'supplier' => '',
                'service_type' => 'D',
                'drop_off_service_id' => 307,
                'description' => '',
                'fuel_surcharge_cost' => NULL,
                'fuel_surcharge' => '0.00',
                'fuel_surcharge_type' => '',
                'max_length' => '0.00',
                'max_width' => '0.00',
                'max_height' => '0.00',
                'max_volumetric_weight' => '0.00',
                'volumetric_denominator' => NULL,
                'send_data_courier' => 0,
                'is_document' => 0,
                'friday_only_flag' => 0,
                'saturday_only_flag' => 0,
                'sunday_only_flag' => 0,
                'product_owner' => NULL,
                'active' => 1,
                'deletedq' => 0,
                'added_on' => '2024-02-24 16:06:08',
                'added_by' => '58',
                'changed_on' => '2024-02-24 16:06:08',
                'changed_by' => '58',
                'uploaded_currency' => '',
                'uploaded_currency_value' => NULL,
                'registration_fee' => NULL,
                'weight_after' => '0.00',
                'aditional_charge' => NULL,
                'origin_country' => 225,
                'is_untrack' => 0,
                'account_owner' => 0,
                'remotearea' => 'ON_PIECE',
                'carrier_address_limit' => NULL,
                'label_class_name' => 'amazonshipping',
                'transit_time' => 0,
                'required_email' => 0,
                'required_telephone' => 0,
                'shipment_type' => 'PARCEL',
                'pre_sort' => 'NO',
                'proforma_invoice' => 0,
                'agent_dispatch' => 'N',
                'brief_manifest' => 'N',
                'delivery_mode' => NULL,
                'insurance_available' => 0,
                'vol_wgt_formula' => '',
                'is_remotearea' => 'N',
                'is_customized' => 0,
                'pre_advise' => 'N',
                'pre_alert' => 'N',
                'pre_alert_email' => '',
                'cut_off_time' => '',
                'label_charges' => NULL,
                'allow_oversize' => 0,
                'allow_overweight' => 0,
                'maximum_allowed_dimension' => 0,
                'maximum_dim_formula' => '',
                'validation_type' => 'mail',
                'zone_type' => 'postcode',
                'tariff_type' => 'multi',
                'girth' => '0.00',
                'girth_formula' => '',
                'mail_type' => NULL,
                'mail_option' => NULL,
                'is_reschedulable' => 0,
                'carrier_service_code' => 'SWA-UK-2D',
                'is_eori_required' => 0,
                'delivery_type' => 'all',
                'is_commercials' => 'not required',
                'is_cn' => 'not required',
            ),
        ));
        
        
    }
}