<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ShoppingPlatformsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('shopping_platforms')->delete();
        
        \DB::table('shopping_platforms')->insert(array (
            0 => 
            array (
                'id' => 1,
                'title' => 'Magento 1.0',
                'page_key' => 'magento',
                'description' => '<center><img src="../images/magento.jpg" /></center>
<div class="row">
<div class="col-md-9 col-sm-9 col-xs-9">
<div class="tab-content">
<div class="tab-pane active" id="tab_7_1">
<h3>Integrate Magento with Oneworld courier management system, to access discounted rates from multiple UK and international carriers.</h3>
<p>Oneworld\'s multi-carrier shipping system integrates seamlessly with Magento, granting you access to a range of UK and international parcel and packet carriers, including Yodel, Hermes, UK Mail, DHL etc.</p>
</div>
<div class="tab-pane fade" id="tab_7_2">
<p>Sending over 350,000 parcels per month on behalf of 500 eCommerce, eBay and Amazon sellers. OneWorld is unique in that not only is our system free of charge, 
our high volumes lead to highly competitive shipping rates. Not only do you get access to discounted rates, our in-house customer services and software development 
teams ensure maximum service as backed up by our customer feedback. Since its conception in 2010, OneWorld has provided Magento sellers with a bespoke courier 
integration via CSV upload and the new OneWorld Magento Shipping and Label Printing Plugin. As Magento is used by thousands of online retailers throughout the UK,
OneWorld has experienced strong growth from users of this eCommerce platform. OneWorld\'s innovative multi-carrier shipping software provides seamless 
integration with a wide range of UK and international parcel, packet and mail service providers, including:</p>
<ul>
<li>YODEL</li>
<li>DHL Express</li>
<li>UK Mail</li>
<li>TNT Express</li>
<li>DX Freight</li>
<li>Whistl</li>
<li>Asendia</li>
<li>OneWorldorce</li>
<li>Hermes / MyHermes</li>
<li>Collect Plus</li>
</ul>
</div>
<div class="tab-pane fade" id="tab_7_3">
<ul>
<li>Integrates multiple carriers, reducing time and paperwork.</li>
<li>Access OneWorld\'s \'pooled volume\' discounted rates and save up to 80% on your existing rates.</li>
<li>Multi-carrier web based tracking.</li>
<li>Free Magento integration provided by our team of in-house software developers.</li>
<li>Single collection, regardless of carrier choice, both on and off-site (performed by OneWorld when close to one of our depots, or by the carrier when outside of our collection zones).</li>
<li>Dedicated in-house customer services team.</li>
</ul>
</div>
<div class="tab-pane fade" id="tab_7_4">
<ul>
<li>1. In Magento admin browse to System -&gt; Magento Connect -&gt; Magento Connect Manager</li>
<li>2. Log in using your Magento admin username and password</li>
<li>3. If you have previously installed a copy of this module, locate it in the list and change the &ldquo;Actions&rdquo; dropdown to &ldquo;Uninstall&rdquo; and click &ldquo;Commit Changes&rdquo;</li>
<li>4. In the &ldquo;Direct package file upload&rdquo; section, part 2, browse and select the zip file attached. Then click &ldquo;Upload&rdquo;.</li>
</ul>
</div>
<div class="tab-pane fade" id="tab_7_5">
<h3>System Requirements</h3>
<ul>
<li>Magento 1.9 or higher</li>
<li>OneWorld shipping account</li>
<li>At least one OneWorld Service Preference List configured</li>
<li>OneWorld tracking API Key (optional)</li>
</ul>
<h3>Setup</h3>
After installing the extension you will need to ensure that your store configuration is correctly configured. The extension uses some of the existing configuration fields but also adds some new ones of its own. In your store&rsquo;s admin panel go to System -&gt; configuration. On General -&gt; General -&gt; Store Information, ensure that at least the following fields are filled in.</div>
</div>
</div>
<div class="col-md-3 col-sm-3 col-xs-3">
<ul class="nav nav-tabs tabs-right">
<li class="active"><a href="#tab_7_1" data-toggle="tab"> Integration </a></li>
<li><a href="#tab_7_2" data-toggle="tab"> Save time and money </a></li>
<li><a href="#tab_7_3" data-toggle="tab"> Features and Benefits</a></li>
<li><a href="#tab_7_4" data-toggle="tab"> Instructions for installing </a></li>
<li><a href="#tab_7_5" data-toggle="tab"> Setup Guide </a></li>
</ul>
</div>
</div>',
                'translation_key' => 'MAGENTO 1.0',
                'plugin_key' => 'magento',
                'integration_logo' => 'Magento 1.0-magento.jpg',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'title' => 'Linnworks',
                'page_key' => 'linnworks',
                'description' => '<center><img src="../images/linoworks.jpg" /></center>
<p>Integrate Linnworks with free OneWorld courier management software, to access discounted rates from multiple UK and international carriers. OneWorld\'s free multi-carrier shipping software integrates seamlessly with Linnworks, granting you access to a range of UK and international parcel and packet carriers, including DHL, UK Mail, TNT, Yodel, CollectPlus, MyHermes, Parcelforce, DX and Whistl.</p>
<h3>Save time and money on your eCommerce parcel delivery</h3>
<p>Distributing over 350,000 parcels per month on behalf of over 500 eCommerce, eBay and Amazon retailers, OneWorld a flexible and scalable eCommerce shipping platform.</p>
<center><a class="btn btn-primary" href="shopping_platform/OneWorldExpress-Linnworks-courier-API-integrationl.pdf" target="_blank">Linnworks Courier Integration Guide</a></center>',
                'translation_key' => 'LINNWORKS',
                'plugin_key' => 'linnworks',
                'integration_logo' => 'Linnworks-linnworks.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            2 => 
            array (
                'id' => 8,
                'title' => 'Vinculum',
                'page_key' => 'vinculum',
                'description' => '<center>
<p><img src="../images/vinculum.jpg" /></p>
</center>
<p>Integrate Vinculum with free OneWorld courier management software, to access discounted rates from multiple UK and international carriers. OneWorld\'s free multi-carrier shipping software integrates seamlessly with Vinculum, granting you access to a range of UK and international parcel and packet carriers, including DHL, UK Mail, TNT, Yodel, CollectPlus, MyHermes, Parcelforce, DX and Whistl.</p>
<center><a href="http://wmsuat.vineretail.com/eRetailWMS/doLogin" target="_blank" title="Vinculum Login" class="btn btn-primary"> Vinculum Login </a> <a href="shopping_platform/one-worldexpress-vinculum-integration-documentation.pdf" target="_blank" title="Vinculum Login" class="btn btn-success"> Vinculum Courier Integration Guide</a></center>',
                'translation_key' => 'VINCULUM',
                'plugin_key' => 'vinculum',
                'integration_logo' => 'Vinculum-vinculum.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 0,
            ),
            3 => 
            array (
                'id' => 9,
                'title' => 'Magento 2.0',
                'page_key' => 'magento 2.0',
                'description' => '<center><img src="../images/magento.jpg" /></center>',
                'translation_key' => 'MAGENTO 2.0',
                'plugin_key' => 'Magento 2',
                'integration_logo' => 'Magento 2.0-magento 2.0.jpg',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            4 => 
            array (
                'id' => 10,
                'title' => 'wordpress',
                'page_key' => 'wordpress',
                'description' => NULL,
                'translation_key' => 'woocommerce',
                'plugin_key' => 'woocommerce',
                'integration_logo' => 'wordpress-wordpress.jpg',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 0,
            ),
            5 => 
            array (
                'id' => 11,
                'title' => 'Plentymarkets',
                'page_key' => 'Plentymarkets',
                'description' => '',
                'translation_key' => 'PLENTYMARKETS',
                'plugin_key' => 'plentymarkets',
                'integration_logo' => 'Plentymarkets-Plentymarkets.jpg',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 0,
            ),
            6 => 
            array (
                'id' => 12,
                'title' => 'Prestashop 1.5',
                'page_key' => 'Prestashop 1.5',
                'description' => '',
                'translation_key' => 'PRESTASHOP 1.5',
                'plugin_key' => 'Prestashop 1.5',
                'integration_logo' => 'Prestashop 1.5-Prestashop 1.5.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 0,
            ),
            7 => 
            array (
                'id' => 13,
                'title' => 'Prestashop',
                'page_key' => 'Prestashop',
                'description' => NULL,
                'translation_key' => 'PRESTASHOP',
                'plugin_key' => 'Prestashop',
                'integration_logo' => 'Prestashop 1.6-Prestashop 1.6.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            8 => 
            array (
                'id' => 14,
                'title' => 'Opencart 2',
                'page_key' => 'Opencart 2',
                'description' => NULL,
                'translation_key' => 'OPENCART 2',
                'plugin_key' => 'Opencart 2',
                'integration_logo' => 'Opencart 2-Opencart 2.jpg',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 0,
            ),
            9 => 
            array (
                'id' => 15,
                'title' => 'Opencart 3',
                'page_key' => 'Opencart 3',
                'description' => NULL,
                'translation_key' => 'OPENCART 3',
                'plugin_key' => 'Opencart 3',
                'integration_logo' => 'Opencart 3-Opencart 3.jpg',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 0,
            ),
            10 => 
            array (
                'id' => 16,
                'title' => 'Jtl commerce',
                'page_key' => 'Jtl commerce',
                'description' => NULL,
                'translation_key' => 'JTLCOMMERCE',
                'plugin_key' => 'Jtl commerce',
                'integration_logo' => 'Jtl commerce-Jtl commerce.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 0,
            ),
            11 => 
            array (
                'id' => 17,
                'title' => 'Shopware',
                'page_key' => 'Shopware',
                'description' => NULL,
                'translation_key' => 'SHOPWARE',
                'plugin_key' => 'Shopware',
                'integration_logo' => 'Shopware-Shopware.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            12 => 
            array (
                'id' => 18,
                'title' => 'Mofified',
                'page_key' => 'Modified',
                'description' => '',
                'translation_key' => 'MODIFIED',
                'plugin_key' => 'Modified',
                'integration_logo' => 'Mofified-Modified.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            13 => 
            array (
                'id' => 19,
                'title' => 'Gambio',
                'page_key' => 'Gambio',
                'description' => '',
                'translation_key' => 'GAMBIO',
                'plugin_key' => 'Gambio',
                'integration_logo' => 'Gambio-Gambio.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            14 => 
            array (
                'id' => 20,
                'title' => 'Oxid',
                'page_key' => 'oxid',
                'description' => '',
                'translation_key' => 'Oxid',
                'plugin_key' => 'oxid',
                'integration_logo' => 'Oxid-oxid.jpg',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            15 => 
            array (
                'id' => 21,
                'title' => 'Smart Track',
                'page_key' => 'smarttrack',
                'description' => NULL,
                'translation_key' => 'SMART TRACK',
                'plugin_key' => 'smarttrack',
                'integration_logo' => 'inner-logo.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            16 => 
            array (
                'id' => 1,
                'title' => 'Magento 1.0',
                'page_key' => 'magento',
                'description' => '<center><img src="../images/magento.jpg" /></center>
<div class="row">
<div class="col-md-9 col-sm-9 col-xs-9">
<div class="tab-content">
<div class="tab-pane active" id="tab_7_1">
<h3>Integrate Magento with Oneworld courier management system, to access discounted rates from multiple UK and international carriers.</h3>
<p>Oneworld\'s multi-carrier shipping system integrates seamlessly with Magento, granting you access to a range of UK and international parcel and packet carriers, including Yodel, Hermes, UK Mail, DHL etc.</p>
</div>
<div class="tab-pane fade" id="tab_7_2">
<p>Sending over 350,000 parcels per month on behalf of 500 eCommerce, eBay and Amazon sellers. OneWorld is unique in that not only is our system free of charge, 
our high volumes lead to highly competitive shipping rates. Not only do you get access to discounted rates, our in-house customer services and software development 
teams ensure maximum service as backed up by our customer feedback. Since its conception in 2010, OneWorld has provided Magento sellers with a bespoke courier 
integration via CSV upload and the new OneWorld Magento Shipping and Label Printing Plugin. As Magento is used by thousands of online retailers throughout the UK,
OneWorld has experienced strong growth from users of this eCommerce platform. OneWorld\'s innovative multi-carrier shipping software provides seamless 
integration with a wide range of UK and international parcel, packet and mail service providers, including:</p>
<ul>
<li>YODEL</li>
<li>DHL Express</li>
<li>UK Mail</li>
<li>TNT Express</li>
<li>DX Freight</li>
<li>Whistl</li>
<li>Asendia</li>
<li>OneWorldorce</li>
<li>Hermes / MyHermes</li>
<li>Collect Plus</li>
</ul>
</div>
<div class="tab-pane fade" id="tab_7_3">
<ul>
<li>Integrates multiple carriers, reducing time and paperwork.</li>
<li>Access OneWorld\'s \'pooled volume\' discounted rates and save up to 80% on your existing rates.</li>
<li>Multi-carrier web based tracking.</li>
<li>Free Magento integration provided by our team of in-house software developers.</li>
<li>Single collection, regardless of carrier choice, both on and off-site (performed by OneWorld when close to one of our depots, or by the carrier when outside of our collection zones).</li>
<li>Dedicated in-house customer services team.</li>
</ul>
</div>
<div class="tab-pane fade" id="tab_7_4">
<ul>
<li>1. In Magento admin browse to System -&gt; Magento Connect -&gt; Magento Connect Manager</li>
<li>2. Log in using your Magento admin username and password</li>
<li>3. If you have previously installed a copy of this module, locate it in the list and change the &ldquo;Actions&rdquo; dropdown to &ldquo;Uninstall&rdquo; and click &ldquo;Commit Changes&rdquo;</li>
<li>4. In the &ldquo;Direct package file upload&rdquo; section, part 2, browse and select the zip file attached. Then click &ldquo;Upload&rdquo;.</li>
</ul>
</div>
<div class="tab-pane fade" id="tab_7_5">
<h3>System Requirements</h3>
<ul>
<li>Magento 1.9 or higher</li>
<li>OneWorld shipping account</li>
<li>At least one OneWorld Service Preference List configured</li>
<li>OneWorld tracking API Key (optional)</li>
</ul>
<h3>Setup</h3>
After installing the extension you will need to ensure that your store configuration is correctly configured. The extension uses some of the existing configuration fields but also adds some new ones of its own. In your store&rsquo;s admin panel go to System -&gt; configuration. On General -&gt; General -&gt; Store Information, ensure that at least the following fields are filled in.</div>
</div>
</div>
<div class="col-md-3 col-sm-3 col-xs-3">
<ul class="nav nav-tabs tabs-right">
<li class="active"><a href="#tab_7_1" data-toggle="tab"> Integration </a></li>
<li><a href="#tab_7_2" data-toggle="tab"> Save time and money </a></li>
<li><a href="#tab_7_3" data-toggle="tab"> Features and Benefits</a></li>
<li><a href="#tab_7_4" data-toggle="tab"> Instructions for installing </a></li>
<li><a href="#tab_7_5" data-toggle="tab"> Setup Guide </a></li>
</ul>
</div>
</div>',
                'translation_key' => 'MAGENTO 1.0',
                'plugin_key' => 'magento',
                'integration_logo' => 'Magento 1.0-magento.jpg',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            17 => 
            array (
                'id' => 2,
                'title' => 'Linnworks',
                'page_key' => 'linnworks',
                'description' => '<center><img src="../images/linoworks.jpg" /></center>
<p>Integrate Linnworks with free OneWorld courier management software, to access discounted rates from multiple UK and international carriers. OneWorld\'s free multi-carrier shipping software integrates seamlessly with Linnworks, granting you access to a range of UK and international parcel and packet carriers, including DHL, UK Mail, TNT, Yodel, CollectPlus, MyHermes, Parcelforce, DX and Whistl.</p>
<h3>Save time and money on your eCommerce parcel delivery</h3>
<p>Distributing over 350,000 parcels per month on behalf of over 500 eCommerce, eBay and Amazon retailers, OneWorld a flexible and scalable eCommerce shipping platform.</p>
<center><a class="btn btn-primary" href="shopping_platform/OneWorldExpress-Linnworks-courier-API-integrationl.pdf" target="_blank">Linnworks Courier Integration Guide</a></center>',
                'translation_key' => 'LINNWORKS',
                'plugin_key' => 'linnworks',
                'integration_logo' => 'Linnworks-linnworks.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            18 => 
            array (
                'id' => 8,
                'title' => 'Vinculum',
                'page_key' => 'vinculum',
                'description' => '<center>
<p><img src="../images/vinculum.jpg" /></p>
</center>
<p>Integrate Vinculum with free OneWorld courier management software, to access discounted rates from multiple UK and international carriers. OneWorld\'s free multi-carrier shipping software integrates seamlessly with Vinculum, granting you access to a range of UK and international parcel and packet carriers, including DHL, UK Mail, TNT, Yodel, CollectPlus, MyHermes, Parcelforce, DX and Whistl.</p>
<center><a href="http://wmsuat.vineretail.com/eRetailWMS/doLogin" target="_blank" title="Vinculum Login" class="btn btn-primary"> Vinculum Login </a> <a href="shopping_platform/one-worldexpress-vinculum-integration-documentation.pdf" target="_blank" title="Vinculum Login" class="btn btn-success"> Vinculum Courier Integration Guide</a></center>',
                'translation_key' => 'VINCULUM',
                'plugin_key' => 'vinculum',
                'integration_logo' => 'Vinculum-vinculum.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 0,
            ),
            19 => 
            array (
                'id' => 9,
                'title' => 'Magento 2.0',
                'page_key' => 'magento 2.0',
                'description' => '<center><img src="../images/magento.jpg" /></center>',
                'translation_key' => 'MAGENTO 2.0',
                'plugin_key' => 'Magento 2',
                'integration_logo' => 'Magento 2.0-magento 2.0.jpg',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            20 => 
            array (
                'id' => 10,
                'title' => 'wordpress',
                'page_key' => 'wordpress',
                'description' => NULL,
                'translation_key' => 'woocommerce',
                'plugin_key' => 'woocommerce',
                'integration_logo' => 'wordpress-wordpress.jpg',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 0,
            ),
            21 => 
            array (
                'id' => 11,
                'title' => 'Plentymarkets',
                'page_key' => 'Plentymarkets',
                'description' => '',
                'translation_key' => 'PLENTYMARKETS',
                'plugin_key' => 'plentymarkets',
                'integration_logo' => 'Plentymarkets-Plentymarkets.jpg',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 0,
            ),
            22 => 
            array (
                'id' => 12,
                'title' => 'Prestashop 1.5',
                'page_key' => 'Prestashop 1.5',
                'description' => '',
                'translation_key' => 'PRESTASHOP 1.5',
                'plugin_key' => 'Prestashop 1.5',
                'integration_logo' => 'Prestashop 1.5-Prestashop 1.5.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 0,
            ),
            23 => 
            array (
                'id' => 13,
                'title' => 'Prestashop',
                'page_key' => 'Prestashop',
                'description' => NULL,
                'translation_key' => 'PRESTASHOP',
                'plugin_key' => 'Prestashop',
                'integration_logo' => 'Prestashop 1.6-Prestashop 1.6.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            24 => 
            array (
                'id' => 14,
                'title' => 'Opencart 2',
                'page_key' => 'Opencart 2',
                'description' => NULL,
                'translation_key' => 'OPENCART 2',
                'plugin_key' => 'Opencart 2',
                'integration_logo' => 'Opencart 2-Opencart 2.jpg',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 0,
            ),
            25 => 
            array (
                'id' => 15,
                'title' => 'Opencart 3',
                'page_key' => 'Opencart 3',
                'description' => NULL,
                'translation_key' => 'OPENCART 3',
                'plugin_key' => 'Opencart 3',
                'integration_logo' => 'Opencart 3-Opencart 3.jpg',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 0,
            ),
            26 => 
            array (
                'id' => 16,
                'title' => 'Jtl commerce',
                'page_key' => 'Jtl commerce',
                'description' => NULL,
                'translation_key' => 'JTLCOMMERCE',
                'plugin_key' => 'Jtl commerce',
                'integration_logo' => 'Jtl commerce-Jtl commerce.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 0,
            ),
            27 => 
            array (
                'id' => 17,
                'title' => 'Shopware',
                'page_key' => 'Shopware',
                'description' => NULL,
                'translation_key' => 'SHOPWARE',
                'plugin_key' => 'Shopware',
                'integration_logo' => 'Shopware-Shopware.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            28 => 
            array (
                'id' => 18,
                'title' => 'Mofified',
                'page_key' => 'Modified',
                'description' => '',
                'translation_key' => 'MODIFIED',
                'plugin_key' => 'Modified',
                'integration_logo' => 'Mofified-Modified.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            29 => 
            array (
                'id' => 19,
                'title' => 'Gambio',
                'page_key' => 'Gambio',
                'description' => '',
                'translation_key' => 'GAMBIO',
                'plugin_key' => 'Gambio',
                'integration_logo' => 'Gambio-Gambio.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            30 => 
            array (
                'id' => 20,
                'title' => 'Oxid',
                'page_key' => 'oxid',
                'description' => '',
                'translation_key' => 'Oxid',
                'plugin_key' => 'oxid',
                'integration_logo' => 'Oxid-oxid.jpg',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            31 => 
            array (
                'id' => 21,
                'title' => 'Smart Track',
                'page_key' => 'smarttrack',
                'description' => NULL,
                'translation_key' => 'SMART TRACK',
                'plugin_key' => 'smarttrack',
                'integration_logo' => 'inner-logo.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            32 => 
            array (
                'id' => 1,
                'title' => 'Magento 1.0',
                'page_key' => 'magento',
                'description' => '<center><img src="../images/magento.jpg" /></center>
<div class="row">
<div class="col-md-9 col-sm-9 col-xs-9">
<div class="tab-content">
<div class="tab-pane active" id="tab_7_1">
<h3>Integrate Magento with Oneworld courier management system, to access discounted rates from multiple UK and international carriers.</h3>
<p>Oneworld\'s multi-carrier shipping system integrates seamlessly with Magento, granting you access to a range of UK and international parcel and packet carriers, including Yodel, Hermes, UK Mail, DHL etc.</p>
</div>
<div class="tab-pane fade" id="tab_7_2">
<p>Sending over 350,000 parcels per month on behalf of 500 eCommerce, eBay and Amazon sellers. OneWorld is unique in that not only is our system free of charge, 
our high volumes lead to highly competitive shipping rates. Not only do you get access to discounted rates, our in-house customer services and software development 
teams ensure maximum service as backed up by our customer feedback. Since its conception in 2010, OneWorld has provided Magento sellers with a bespoke courier 
integration via CSV upload and the new OneWorld Magento Shipping and Label Printing Plugin. As Magento is used by thousands of online retailers throughout the UK,
OneWorld has experienced strong growth from users of this eCommerce platform. OneWorld\'s innovative multi-carrier shipping software provides seamless 
integration with a wide range of UK and international parcel, packet and mail service providers, including:</p>
<ul>
<li>YODEL</li>
<li>DHL Express</li>
<li>UK Mail</li>
<li>TNT Express</li>
<li>DX Freight</li>
<li>Whistl</li>
<li>Asendia</li>
<li>OneWorldorce</li>
<li>Hermes / MyHermes</li>
<li>Collect Plus</li>
</ul>
</div>
<div class="tab-pane fade" id="tab_7_3">
<ul>
<li>Integrates multiple carriers, reducing time and paperwork.</li>
<li>Access OneWorld\'s \'pooled volume\' discounted rates and save up to 80% on your existing rates.</li>
<li>Multi-carrier web based tracking.</li>
<li>Free Magento integration provided by our team of in-house software developers.</li>
<li>Single collection, regardless of carrier choice, both on and off-site (performed by OneWorld when close to one of our depots, or by the carrier when outside of our collection zones).</li>
<li>Dedicated in-house customer services team.</li>
</ul>
</div>
<div class="tab-pane fade" id="tab_7_4">
<ul>
<li>1. In Magento admin browse to System -&gt; Magento Connect -&gt; Magento Connect Manager</li>
<li>2. Log in using your Magento admin username and password</li>
<li>3. If you have previously installed a copy of this module, locate it in the list and change the &ldquo;Actions&rdquo; dropdown to &ldquo;Uninstall&rdquo; and click &ldquo;Commit Changes&rdquo;</li>
<li>4. In the &ldquo;Direct package file upload&rdquo; section, part 2, browse and select the zip file attached. Then click &ldquo;Upload&rdquo;.</li>
</ul>
</div>
<div class="tab-pane fade" id="tab_7_5">
<h3>System Requirements</h3>
<ul>
<li>Magento 1.9 or higher</li>
<li>OneWorld shipping account</li>
<li>At least one OneWorld Service Preference List configured</li>
<li>OneWorld tracking API Key (optional)</li>
</ul>
<h3>Setup</h3>
After installing the extension you will need to ensure that your store configuration is correctly configured. The extension uses some of the existing configuration fields but also adds some new ones of its own. In your store&rsquo;s admin panel go to System -&gt; configuration. On General -&gt; General -&gt; Store Information, ensure that at least the following fields are filled in.</div>
</div>
</div>
<div class="col-md-3 col-sm-3 col-xs-3">
<ul class="nav nav-tabs tabs-right">
<li class="active"><a href="#tab_7_1" data-toggle="tab"> Integration </a></li>
<li><a href="#tab_7_2" data-toggle="tab"> Save time and money </a></li>
<li><a href="#tab_7_3" data-toggle="tab"> Features and Benefits</a></li>
<li><a href="#tab_7_4" data-toggle="tab"> Instructions for installing </a></li>
<li><a href="#tab_7_5" data-toggle="tab"> Setup Guide </a></li>
</ul>
</div>
</div>',
                'translation_key' => 'MAGENTO 1.0',
                'plugin_key' => 'magento',
                'integration_logo' => 'Magento 1.0-magento.jpg',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            33 => 
            array (
                'id' => 2,
                'title' => 'Linnworks',
                'page_key' => 'linnworks',
                'description' => '<center><img src="../images/linoworks.jpg" /></center>
<p>Integrate Linnworks with free OneWorld courier management software, to access discounted rates from multiple UK and international carriers. OneWorld\'s free multi-carrier shipping software integrates seamlessly with Linnworks, granting you access to a range of UK and international parcel and packet carriers, including DHL, UK Mail, TNT, Yodel, CollectPlus, MyHermes, Parcelforce, DX and Whistl.</p>
<h3>Save time and money on your eCommerce parcel delivery</h3>
<p>Distributing over 350,000 parcels per month on behalf of over 500 eCommerce, eBay and Amazon retailers, OneWorld a flexible and scalable eCommerce shipping platform.</p>
<center><a class="btn btn-primary" href="shopping_platform/OneWorldExpress-Linnworks-courier-API-integrationl.pdf" target="_blank">Linnworks Courier Integration Guide</a></center>',
                'translation_key' => 'LINNWORKS',
                'plugin_key' => 'linnworks',
                'integration_logo' => 'Linnworks-linnworks.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            34 => 
            array (
                'id' => 8,
                'title' => 'Vinculum',
                'page_key' => 'vinculum',
                'description' => '<center>
<p><img src="../images/vinculum.jpg" /></p>
</center>
<p>Integrate Vinculum with free OneWorld courier management software, to access discounted rates from multiple UK and international carriers. OneWorld\'s free multi-carrier shipping software integrates seamlessly with Vinculum, granting you access to a range of UK and international parcel and packet carriers, including DHL, UK Mail, TNT, Yodel, CollectPlus, MyHermes, Parcelforce, DX and Whistl.</p>
<center><a href="http://wmsuat.vineretail.com/eRetailWMS/doLogin" target="_blank" title="Vinculum Login" class="btn btn-primary"> Vinculum Login </a> <a href="shopping_platform/one-worldexpress-vinculum-integration-documentation.pdf" target="_blank" title="Vinculum Login" class="btn btn-success"> Vinculum Courier Integration Guide</a></center>',
                'translation_key' => 'VINCULUM',
                'plugin_key' => 'vinculum',
                'integration_logo' => 'Vinculum-vinculum.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 0,
            ),
            35 => 
            array (
                'id' => 9,
                'title' => 'Magento 2.0',
                'page_key' => 'magento 2.0',
                'description' => '<center><img src="../images/magento.jpg" /></center>',
                'translation_key' => 'MAGENTO 2.0',
                'plugin_key' => 'Magento 2',
                'integration_logo' => 'Magento 2.0-magento 2.0.jpg',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            36 => 
            array (
                'id' => 10,
                'title' => 'wordpress',
                'page_key' => 'wordpress',
                'description' => NULL,
                'translation_key' => 'woocommerce',
                'plugin_key' => 'woocommerce',
                'integration_logo' => 'wordpress-wordpress.jpg',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 0,
            ),
            37 => 
            array (
                'id' => 11,
                'title' => 'Plentymarkets',
                'page_key' => 'Plentymarkets',
                'description' => '',
                'translation_key' => 'PLENTYMARKETS',
                'plugin_key' => 'plentymarkets',
                'integration_logo' => 'Plentymarkets-Plentymarkets.jpg',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 0,
            ),
            38 => 
            array (
                'id' => 12,
                'title' => 'Prestashop 1.5',
                'page_key' => 'Prestashop 1.5',
                'description' => '',
                'translation_key' => 'PRESTASHOP 1.5',
                'plugin_key' => 'Prestashop 1.5',
                'integration_logo' => 'Prestashop 1.5-Prestashop 1.5.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 0,
            ),
            39 => 
            array (
                'id' => 13,
                'title' => 'Prestashop',
                'page_key' => 'Prestashop',
                'description' => NULL,
                'translation_key' => 'PRESTASHOP',
                'plugin_key' => 'Prestashop',
                'integration_logo' => 'Prestashop 1.6-Prestashop 1.6.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            40 => 
            array (
                'id' => 14,
                'title' => 'Opencart 2',
                'page_key' => 'Opencart 2',
                'description' => NULL,
                'translation_key' => 'OPENCART 2',
                'plugin_key' => 'Opencart 2',
                'integration_logo' => 'Opencart 2-Opencart 2.jpg',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 0,
            ),
            41 => 
            array (
                'id' => 15,
                'title' => 'Opencart 3',
                'page_key' => 'Opencart 3',
                'description' => NULL,
                'translation_key' => 'OPENCART 3',
                'plugin_key' => 'Opencart 3',
                'integration_logo' => 'Opencart 3-Opencart 3.jpg',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 0,
            ),
            42 => 
            array (
                'id' => 16,
                'title' => 'Jtl commerce',
                'page_key' => 'Jtl commerce',
                'description' => NULL,
                'translation_key' => 'JTLCOMMERCE',
                'plugin_key' => 'Jtl commerce',
                'integration_logo' => 'Jtl commerce-Jtl commerce.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 0,
            ),
            43 => 
            array (
                'id' => 17,
                'title' => 'Shopware',
                'page_key' => 'Shopware',
                'description' => NULL,
                'translation_key' => 'SHOPWARE',
                'plugin_key' => 'Shopware',
                'integration_logo' => 'Shopware-Shopware.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            44 => 
            array (
                'id' => 18,
                'title' => 'Mofified',
                'page_key' => 'Modified',
                'description' => '',
                'translation_key' => 'MODIFIED',
                'plugin_key' => 'Modified',
                'integration_logo' => 'Mofified-Modified.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            45 => 
            array (
                'id' => 19,
                'title' => 'Gambio',
                'page_key' => 'Gambio',
                'description' => '',
                'translation_key' => 'GAMBIO',
                'plugin_key' => 'Gambio',
                'integration_logo' => 'Gambio-Gambio.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            46 => 
            array (
                'id' => 20,
                'title' => 'Oxid',
                'page_key' => 'oxid',
                'description' => '',
                'translation_key' => 'Oxid',
                'plugin_key' => 'oxid',
                'integration_logo' => 'Oxid-oxid.jpg',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            47 => 
            array (
                'id' => 21,
                'title' => 'Smart Track',
                'page_key' => 'smarttrack',
                'description' => NULL,
                'translation_key' => 'SMART TRACK',
                'plugin_key' => 'smarttrack',
                'integration_logo' => 'inner-logo.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            48 => 
            array (
                'id' => 1,
                'title' => 'Magento 1.0',
                'page_key' => 'magento',
                'description' => '<center><img src="../images/magento.jpg" /></center>
<div class="row">
<div class="col-md-9 col-sm-9 col-xs-9">
<div class="tab-content">
<div class="tab-pane active" id="tab_7_1">
<h3>Integrate Magento with Oneworld courier management system, to access discounted rates from multiple UK and international carriers.</h3>
<p>Oneworld\'s multi-carrier shipping system integrates seamlessly with Magento, granting you access to a range of UK and international parcel and packet carriers, including Yodel, Hermes, UK Mail, DHL etc.</p>
</div>
<div class="tab-pane fade" id="tab_7_2">
<p>Sending over 350,000 parcels per month on behalf of 500 eCommerce, eBay and Amazon sellers. OneWorld is unique in that not only is our system free of charge, 
our high volumes lead to highly competitive shipping rates. Not only do you get access to discounted rates, our in-house customer services and software development 
teams ensure maximum service as backed up by our customer feedback. Since its conception in 2010, OneWorld has provided Magento sellers with a bespoke courier 
integration via CSV upload and the new OneWorld Magento Shipping and Label Printing Plugin. As Magento is used by thousands of online retailers throughout the UK,
OneWorld has experienced strong growth from users of this eCommerce platform. OneWorld\'s innovative multi-carrier shipping software provides seamless 
integration with a wide range of UK and international parcel, packet and mail service providers, including:</p>
<ul>
<li>YODEL</li>
<li>DHL Express</li>
<li>UK Mail</li>
<li>TNT Express</li>
<li>DX Freight</li>
<li>Whistl</li>
<li>Asendia</li>
<li>OneWorldorce</li>
<li>Hermes / MyHermes</li>
<li>Collect Plus</li>
</ul>
</div>
<div class="tab-pane fade" id="tab_7_3">
<ul>
<li>Integrates multiple carriers, reducing time and paperwork.</li>
<li>Access OneWorld\'s \'pooled volume\' discounted rates and save up to 80% on your existing rates.</li>
<li>Multi-carrier web based tracking.</li>
<li>Free Magento integration provided by our team of in-house software developers.</li>
<li>Single collection, regardless of carrier choice, both on and off-site (performed by OneWorld when close to one of our depots, or by the carrier when outside of our collection zones).</li>
<li>Dedicated in-house customer services team.</li>
</ul>
</div>
<div class="tab-pane fade" id="tab_7_4">
<ul>
<li>1. In Magento admin browse to System -&gt; Magento Connect -&gt; Magento Connect Manager</li>
<li>2. Log in using your Magento admin username and password</li>
<li>3. If you have previously installed a copy of this module, locate it in the list and change the &ldquo;Actions&rdquo; dropdown to &ldquo;Uninstall&rdquo; and click &ldquo;Commit Changes&rdquo;</li>
<li>4. In the &ldquo;Direct package file upload&rdquo; section, part 2, browse and select the zip file attached. Then click &ldquo;Upload&rdquo;.</li>
</ul>
</div>
<div class="tab-pane fade" id="tab_7_5">
<h3>System Requirements</h3>
<ul>
<li>Magento 1.9 or higher</li>
<li>OneWorld shipping account</li>
<li>At least one OneWorld Service Preference List configured</li>
<li>OneWorld tracking API Key (optional)</li>
</ul>
<h3>Setup</h3>
After installing the extension you will need to ensure that your store configuration is correctly configured. The extension uses some of the existing configuration fields but also adds some new ones of its own. In your store&rsquo;s admin panel go to System -&gt; configuration. On General -&gt; General -&gt; Store Information, ensure that at least the following fields are filled in.</div>
</div>
</div>
<div class="col-md-3 col-sm-3 col-xs-3">
<ul class="nav nav-tabs tabs-right">
<li class="active"><a href="#tab_7_1" data-toggle="tab"> Integration </a></li>
<li><a href="#tab_7_2" data-toggle="tab"> Save time and money </a></li>
<li><a href="#tab_7_3" data-toggle="tab"> Features and Benefits</a></li>
<li><a href="#tab_7_4" data-toggle="tab"> Instructions for installing </a></li>
<li><a href="#tab_7_5" data-toggle="tab"> Setup Guide </a></li>
</ul>
</div>
</div>',
                'translation_key' => 'MAGENTO 1.0',
                'plugin_key' => 'magento',
                'integration_logo' => 'Magento 1.0-magento.jpg',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            49 => 
            array (
                'id' => 2,
                'title' => 'Linnworks',
                'page_key' => 'linnworks',
                'description' => '<center><img src="../images/linoworks.jpg" /></center>
<p>Integrate Linnworks with free OneWorld courier management software, to access discounted rates from multiple UK and international carriers. OneWorld\'s free multi-carrier shipping software integrates seamlessly with Linnworks, granting you access to a range of UK and international parcel and packet carriers, including DHL, UK Mail, TNT, Yodel, CollectPlus, MyHermes, Parcelforce, DX and Whistl.</p>
<h3>Save time and money on your eCommerce parcel delivery</h3>
<p>Distributing over 350,000 parcels per month on behalf of over 500 eCommerce, eBay and Amazon retailers, OneWorld a flexible and scalable eCommerce shipping platform.</p>
<center><a class="btn btn-primary" href="shopping_platform/OneWorldExpress-Linnworks-courier-API-integrationl.pdf" target="_blank">Linnworks Courier Integration Guide</a></center>',
                'translation_key' => 'LINNWORKS',
                'plugin_key' => 'linnworks',
                'integration_logo' => 'Linnworks-linnworks.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            50 => 
            array (
                'id' => 8,
                'title' => 'Vinculum',
                'page_key' => 'vinculum',
                'description' => '<center>
<p><img src="../images/vinculum.jpg" /></p>
</center>
<p>Integrate Vinculum with free OneWorld courier management software, to access discounted rates from multiple UK and international carriers. OneWorld\'s free multi-carrier shipping software integrates seamlessly with Vinculum, granting you access to a range of UK and international parcel and packet carriers, including DHL, UK Mail, TNT, Yodel, CollectPlus, MyHermes, Parcelforce, DX and Whistl.</p>
<center><a href="http://wmsuat.vineretail.com/eRetailWMS/doLogin" target="_blank" title="Vinculum Login" class="btn btn-primary"> Vinculum Login </a> <a href="shopping_platform/one-worldexpress-vinculum-integration-documentation.pdf" target="_blank" title="Vinculum Login" class="btn btn-success"> Vinculum Courier Integration Guide</a></center>',
                'translation_key' => 'VINCULUM',
                'plugin_key' => 'vinculum',
                'integration_logo' => 'Vinculum-vinculum.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 0,
            ),
            51 => 
            array (
                'id' => 9,
                'title' => 'Magento 2.0',
                'page_key' => 'magento 2.0',
                'description' => '<center><img src="../images/magento.jpg" /></center>',
                'translation_key' => 'MAGENTO 2.0',
                'plugin_key' => 'Magento 2',
                'integration_logo' => 'Magento 2.0-magento 2.0.jpg',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            52 => 
            array (
                'id' => 10,
                'title' => 'wordpress',
                'page_key' => 'wordpress',
                'description' => NULL,
                'translation_key' => 'woocommerce',
                'plugin_key' => 'woocommerce',
                'integration_logo' => 'wordpress-wordpress.jpg',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 0,
            ),
            53 => 
            array (
                'id' => 11,
                'title' => 'Plentymarkets',
                'page_key' => 'Plentymarkets',
                'description' => '',
                'translation_key' => 'PLENTYMARKETS',
                'plugin_key' => 'plentymarkets',
                'integration_logo' => 'Plentymarkets-Plentymarkets.jpg',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 0,
            ),
            54 => 
            array (
                'id' => 12,
                'title' => 'Prestashop 1.5',
                'page_key' => 'Prestashop 1.5',
                'description' => '',
                'translation_key' => 'PRESTASHOP 1.5',
                'plugin_key' => 'Prestashop 1.5',
                'integration_logo' => 'Prestashop 1.5-Prestashop 1.5.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 0,
            ),
            55 => 
            array (
                'id' => 13,
                'title' => 'Prestashop',
                'page_key' => 'Prestashop',
                'description' => NULL,
                'translation_key' => 'PRESTASHOP',
                'plugin_key' => 'Prestashop',
                'integration_logo' => 'Prestashop 1.6-Prestashop 1.6.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            56 => 
            array (
                'id' => 14,
                'title' => 'Opencart 2',
                'page_key' => 'Opencart 2',
                'description' => NULL,
                'translation_key' => 'OPENCART 2',
                'plugin_key' => 'Opencart 2',
                'integration_logo' => 'Opencart 2-Opencart 2.jpg',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 0,
            ),
            57 => 
            array (
                'id' => 15,
                'title' => 'Opencart 3',
                'page_key' => 'Opencart 3',
                'description' => NULL,
                'translation_key' => 'OPENCART 3',
                'plugin_key' => 'Opencart 3',
                'integration_logo' => 'Opencart 3-Opencart 3.jpg',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 0,
            ),
            58 => 
            array (
                'id' => 16,
                'title' => 'Jtl commerce',
                'page_key' => 'Jtl commerce',
                'description' => NULL,
                'translation_key' => 'JTLCOMMERCE',
                'plugin_key' => 'Jtl commerce',
                'integration_logo' => 'Jtl commerce-Jtl commerce.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 0,
            ),
            59 => 
            array (
                'id' => 17,
                'title' => 'Shopware',
                'page_key' => 'Shopware',
                'description' => NULL,
                'translation_key' => 'SHOPWARE',
                'plugin_key' => 'Shopware',
                'integration_logo' => 'Shopware-Shopware.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            60 => 
            array (
                'id' => 18,
                'title' => 'Mofified',
                'page_key' => 'Modified',
                'description' => '',
                'translation_key' => 'MODIFIED',
                'plugin_key' => 'Modified',
                'integration_logo' => 'Mofified-Modified.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            61 => 
            array (
                'id' => 19,
                'title' => 'Gambio',
                'page_key' => 'Gambio',
                'description' => '',
                'translation_key' => 'GAMBIO',
                'plugin_key' => 'Gambio',
                'integration_logo' => 'Gambio-Gambio.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            62 => 
            array (
                'id' => 20,
                'title' => 'Oxid',
                'page_key' => 'oxid',
                'description' => '',
                'translation_key' => 'Oxid',
                'plugin_key' => 'oxid',
                'integration_logo' => 'Oxid-oxid.jpg',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            63 => 
            array (
                'id' => 21,
                'title' => 'Smart Track',
                'page_key' => 'smarttrack',
                'description' => NULL,
                'translation_key' => 'SMART TRACK',
                'plugin_key' => 'smarttrack',
                'integration_logo' => 'inner-logo.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            64 => 
            array (
                'id' => 1,
                'title' => 'Magento 1.0',
                'page_key' => 'magento',
                'description' => '<center><img src="../images/magento.jpg" /></center>
<div class="row">
<div class="col-md-9 col-sm-9 col-xs-9">
<div class="tab-content">
<div class="tab-pane active" id="tab_7_1">
<h3>Integrate Magento with Oneworld courier management system, to access discounted rates from multiple UK and international carriers.</h3>
<p>Oneworld\'s multi-carrier shipping system integrates seamlessly with Magento, granting you access to a range of UK and international parcel and packet carriers, including Yodel, Hermes, UK Mail, DHL etc.</p>
</div>
<div class="tab-pane fade" id="tab_7_2">
<p>Sending over 350,000 parcels per month on behalf of 500 eCommerce, eBay and Amazon sellers. OneWorld is unique in that not only is our system free of charge, 
our high volumes lead to highly competitive shipping rates. Not only do you get access to discounted rates, our in-house customer services and software development 
teams ensure maximum service as backed up by our customer feedback. Since its conception in 2010, OneWorld has provided Magento sellers with a bespoke courier 
integration via CSV upload and the new OneWorld Magento Shipping and Label Printing Plugin. As Magento is used by thousands of online retailers throughout the UK,
OneWorld has experienced strong growth from users of this eCommerce platform. OneWorld\'s innovative multi-carrier shipping software provides seamless 
integration with a wide range of UK and international parcel, packet and mail service providers, including:</p>
<ul>
<li>YODEL</li>
<li>DHL Express</li>
<li>UK Mail</li>
<li>TNT Express</li>
<li>DX Freight</li>
<li>Whistl</li>
<li>Asendia</li>
<li>OneWorldorce</li>
<li>Hermes / MyHermes</li>
<li>Collect Plus</li>
</ul>
</div>
<div class="tab-pane fade" id="tab_7_3">
<ul>
<li>Integrates multiple carriers, reducing time and paperwork.</li>
<li>Access OneWorld\'s \'pooled volume\' discounted rates and save up to 80% on your existing rates.</li>
<li>Multi-carrier web based tracking.</li>
<li>Free Magento integration provided by our team of in-house software developers.</li>
<li>Single collection, regardless of carrier choice, both on and off-site (performed by OneWorld when close to one of our depots, or by the carrier when outside of our collection zones).</li>
<li>Dedicated in-house customer services team.</li>
</ul>
</div>
<div class="tab-pane fade" id="tab_7_4">
<ul>
<li>1. In Magento admin browse to System -&gt; Magento Connect -&gt; Magento Connect Manager</li>
<li>2. Log in using your Magento admin username and password</li>
<li>3. If you have previously installed a copy of this module, locate it in the list and change the &ldquo;Actions&rdquo; dropdown to &ldquo;Uninstall&rdquo; and click &ldquo;Commit Changes&rdquo;</li>
<li>4. In the &ldquo;Direct package file upload&rdquo; section, part 2, browse and select the zip file attached. Then click &ldquo;Upload&rdquo;.</li>
</ul>
</div>
<div class="tab-pane fade" id="tab_7_5">
<h3>System Requirements</h3>
<ul>
<li>Magento 1.9 or higher</li>
<li>OneWorld shipping account</li>
<li>At least one OneWorld Service Preference List configured</li>
<li>OneWorld tracking API Key (optional)</li>
</ul>
<h3>Setup</h3>
After installing the extension you will need to ensure that your store configuration is correctly configured. The extension uses some of the existing configuration fields but also adds some new ones of its own. In your store&rsquo;s admin panel go to System -&gt; configuration. On General -&gt; General -&gt; Store Information, ensure that at least the following fields are filled in.</div>
</div>
</div>
<div class="col-md-3 col-sm-3 col-xs-3">
<ul class="nav nav-tabs tabs-right">
<li class="active"><a href="#tab_7_1" data-toggle="tab"> Integration </a></li>
<li><a href="#tab_7_2" data-toggle="tab"> Save time and money </a></li>
<li><a href="#tab_7_3" data-toggle="tab"> Features and Benefits</a></li>
<li><a href="#tab_7_4" data-toggle="tab"> Instructions for installing </a></li>
<li><a href="#tab_7_5" data-toggle="tab"> Setup Guide </a></li>
</ul>
</div>
</div>',
                'translation_key' => 'MAGENTO 1.0',
                'plugin_key' => 'magento',
                'integration_logo' => 'Magento 1.0-magento.jpg',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            65 => 
            array (
                'id' => 2,
                'title' => 'Linnworks',
                'page_key' => 'linnworks',
                'description' => '<center><img src="../images/linoworks.jpg" /></center>
<p>Integrate Linnworks with free OneWorld courier management software, to access discounted rates from multiple UK and international carriers. OneWorld\'s free multi-carrier shipping software integrates seamlessly with Linnworks, granting you access to a range of UK and international parcel and packet carriers, including DHL, UK Mail, TNT, Yodel, CollectPlus, MyHermes, Parcelforce, DX and Whistl.</p>
<h3>Save time and money on your eCommerce parcel delivery</h3>
<p>Distributing over 350,000 parcels per month on behalf of over 500 eCommerce, eBay and Amazon retailers, OneWorld a flexible and scalable eCommerce shipping platform.</p>
<center><a class="btn btn-primary" href="shopping_platform/OneWorldExpress-Linnworks-courier-API-integrationl.pdf" target="_blank">Linnworks Courier Integration Guide</a></center>',
                'translation_key' => 'LINNWORKS',
                'plugin_key' => 'linnworks',
                'integration_logo' => 'Linnworks-linnworks.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            66 => 
            array (
                'id' => 8,
                'title' => 'Vinculum',
                'page_key' => 'vinculum',
                'description' => '<center>
<p><img src="../images/vinculum.jpg" /></p>
</center>
<p>Integrate Vinculum with free OneWorld courier management software, to access discounted rates from multiple UK and international carriers. OneWorld\'s free multi-carrier shipping software integrates seamlessly with Vinculum, granting you access to a range of UK and international parcel and packet carriers, including DHL, UK Mail, TNT, Yodel, CollectPlus, MyHermes, Parcelforce, DX and Whistl.</p>
<center><a href="http://wmsuat.vineretail.com/eRetailWMS/doLogin" target="_blank" title="Vinculum Login" class="btn btn-primary"> Vinculum Login </a> <a href="shopping_platform/one-worldexpress-vinculum-integration-documentation.pdf" target="_blank" title="Vinculum Login" class="btn btn-success"> Vinculum Courier Integration Guide</a></center>',
                'translation_key' => 'VINCULUM',
                'plugin_key' => 'vinculum',
                'integration_logo' => 'Vinculum-vinculum.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 0,
            ),
            67 => 
            array (
                'id' => 9,
                'title' => 'Magento 2.0',
                'page_key' => 'magento 2.0',
                'description' => '<center><img src="../images/magento.jpg" /></center>',
                'translation_key' => 'MAGENTO 2.0',
                'plugin_key' => 'Magento 2',
                'integration_logo' => 'Magento 2.0-magento 2.0.jpg',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            68 => 
            array (
                'id' => 10,
                'title' => 'wordpress',
                'page_key' => 'wordpress',
                'description' => NULL,
                'translation_key' => 'woocommerce',
                'plugin_key' => 'woocommerce',
                'integration_logo' => 'wordpress-wordpress.jpg',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 0,
            ),
            69 => 
            array (
                'id' => 11,
                'title' => 'Plentymarkets',
                'page_key' => 'Plentymarkets',
                'description' => '',
                'translation_key' => 'PLENTYMARKETS',
                'plugin_key' => 'plentymarkets',
                'integration_logo' => 'Plentymarkets-Plentymarkets.jpg',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 0,
            ),
            70 => 
            array (
                'id' => 12,
                'title' => 'Prestashop 1.5',
                'page_key' => 'Prestashop 1.5',
                'description' => '',
                'translation_key' => 'PRESTASHOP 1.5',
                'plugin_key' => 'Prestashop 1.5',
                'integration_logo' => 'Prestashop 1.5-Prestashop 1.5.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 0,
            ),
            71 => 
            array (
                'id' => 13,
                'title' => 'Prestashop',
                'page_key' => 'Prestashop',
                'description' => NULL,
                'translation_key' => 'PRESTASHOP',
                'plugin_key' => 'Prestashop',
                'integration_logo' => 'Prestashop 1.6-Prestashop 1.6.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            72 => 
            array (
                'id' => 14,
                'title' => 'Opencart 2',
                'page_key' => 'Opencart 2',
                'description' => NULL,
                'translation_key' => 'OPENCART 2',
                'plugin_key' => 'Opencart 2',
                'integration_logo' => 'Opencart 2-Opencart 2.jpg',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 0,
            ),
            73 => 
            array (
                'id' => 15,
                'title' => 'Opencart 3',
                'page_key' => 'Opencart 3',
                'description' => NULL,
                'translation_key' => 'OPENCART 3',
                'plugin_key' => 'Opencart 3',
                'integration_logo' => 'Opencart 3-Opencart 3.jpg',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 0,
            ),
            74 => 
            array (
                'id' => 16,
                'title' => 'Jtl commerce',
                'page_key' => 'Jtl commerce',
                'description' => NULL,
                'translation_key' => 'JTLCOMMERCE',
                'plugin_key' => 'Jtl commerce',
                'integration_logo' => 'Jtl commerce-Jtl commerce.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 0,
            ),
            75 => 
            array (
                'id' => 17,
                'title' => 'Shopware',
                'page_key' => 'Shopware',
                'description' => NULL,
                'translation_key' => 'SHOPWARE',
                'plugin_key' => 'Shopware',
                'integration_logo' => 'Shopware-Shopware.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            76 => 
            array (
                'id' => 18,
                'title' => 'Mofified',
                'page_key' => 'Modified',
                'description' => '',
                'translation_key' => 'MODIFIED',
                'plugin_key' => 'Modified',
                'integration_logo' => 'Mofified-Modified.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            77 => 
            array (
                'id' => 19,
                'title' => 'Gambio',
                'page_key' => 'Gambio',
                'description' => '',
                'translation_key' => 'GAMBIO',
                'plugin_key' => 'Gambio',
                'integration_logo' => 'Gambio-Gambio.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            78 => 
            array (
                'id' => 20,
                'title' => 'Oxid',
                'page_key' => 'oxid',
                'description' => '',
                'translation_key' => 'Oxid',
                'plugin_key' => 'oxid',
                'integration_logo' => 'Oxid-oxid.jpg',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
            79 => 
            array (
                'id' => 21,
                'title' => 'Smart Track',
                'page_key' => 'smarttrack',
                'description' => NULL,
                'translation_key' => 'SMART TRACK',
                'plugin_key' => 'smarttrack',
                'integration_logo' => 'inner-logo.png',
                'display_option' => 0,
                'connect_url' => NULL,
                'active' => 1,
            ),
        ));
        
        
    }
}