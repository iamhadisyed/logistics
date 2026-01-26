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
        <div class="portlet light">
        <div class="portlet-title">
            <div class="caption"><i class="fa fa-upload"></i>
                Legal Disclosure
            </div>
        </div>
        <div class="portlet-body">






<div class="panel-group accordion" id="accordion3">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_1"> Privacy and data protection policy </a>
                                                </h4>
                                            </div>
                                            <div id="collapse_3_1" class="panel-collapse in">
                                                <div class="panel-body">
                                                    <p> OWE is committed to protecting you and your family’s personal information when you are using OWE services. We want our services to be safe and enjoyable environments for everyone. This Privacy Policy relates to our use of any personal information you provide to us through any of the OWE websites.

 </p>
                                                    <p> As set out above, OWE is committed to safeguarding your personal information. Whenever you provide such information, we are legally obliged to use your information in line with all laws concerning the protection of personal information, including the Data Protection Act 1998 (these laws are referred to collectively in this Privacy Policy as the “data protection laws”).
                                                        </p>
<a class="fusion-button button-flat fusion-button-square button-large button-custom button-1 fusion-animated" data-animationtype="fadeInLeft" data-animationduration="0.3" data-animationoffset="100%" target="_self" href="https://www.oneworldexpress.com/wp-content/uploads/OWE-GDPR-DOC-04-2-Data-Protection-Policy.pdf" style="visibility: visible; animation-duration: 0.3s;"><span class="fusion-button-text">OWE’s Data Protection Policy</span></a>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_2"> Our cookie policy

 </a>
                                                </h4>
                                            </div>
                                            <div id="collapse_3_2" class="panel-collapse collapse">
                                                <div class="panel-body">


                                                   <p> What are cookies?</p>

<p>To be able you to use the services of OWE, you will need to have cookies enabled. If you don’t wish to use cookies, you will be able to browse the site but you will not be able to checkout. The cookies that we utilise will not harm your computer. Our cookies do not store personal information, they are primarily used to resolve errors and ensure you have an easy experience when using the site.</p>
<p>We have relationships with carefully selected advertising aggregators that will set cookies during your visit which may be used by us to show you marketing messages when you are visiting certain 3rd party websites. These cookies are “Google Double Click” and “Google Adsense” and you can control these cookies by visiting the Your Online Choices website.</p>
Our cookies do not store sensitive or personal information, such as your name, address or card details. They simply help us know that you are the person that logged in. However, if you would like to restrict the level of cookies that are stored on your computer, you can use your browser to do this. All browsers and mobile phones are different, so check the ‘Help’ section to learn how to change your preferences.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_3"> Google Analytics

 </a>
                                                </h4>
                                            </div>
                                            <div id="collapse_3_3" class="panel-collapse collapse">
                                                <div class="panel-body">
                                                    <p> This website uses Google Analytics, a web analytics service provided by Google, Inc. (“Google”). Google Analytics uses “cookies”, which are text files placed on your computer to help the website analyse how visitors use the site. The information generated by the cookie about your use of the website (including your IP address) will be transmitted to and stored by Google on servers in the United States . Google will use this information for the purpose of evaluating your use of the website, compiling reports on website activity for website operators and providing other services relating to website activity and internet usage. Google may also transfer this information to third parties where required to do so by law, or where such third parties process the information on Google’s behalf. Google will not associate your IP address with any other data held by Google. You may refuse the use of cookies by selecting the appropriate settings on your browser, however please note that if you do this you may not be able to use the full functionality of this website. By using this website, you consent to the processing of data about you by Google in the manner and for the purposes set out above.
                                                    </p>


  <p>You can prevent Google’s collection and use of data (cookies and IP address) by downloading and installing the browser plug-in available under https://tools.google.com/dlpage/gaoptout?hl=en.  </p>

  <p>Deactivate Google Analytics via cookie  </p>

  <p>Please note that this website initializes Google Analytics with the setting “anonymizeIp”. This guarantees anonymized data collection by masking the last part of your IP address.  </p>

  <p>Further information concerning the terms and conditions of use and data privacy can be found at http://www.google.com/analytics/terms/gb.html or at http://www.google.com/intl/en_uk/analytics/privacyoverview.html  </p>



                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_3_4"> Visual References

 </a>
                                                </h4>
                                            </div>
                                            <div id="collapse_3_4" class="panel-collapse collapse">
                                                <div class="panel-body">
                                                    <p> All texts, pictures and further information published here are subject to the copyright of One World Express Inc. Ltd. United Kingdom. Reproduction, distribution or public reproduction is permitted only in the case of a revocable and non-transferable consent of One World Express Inc. Ltd..
                                                    </p>
  <p>
API.jpg: Businessman Api Data Product Ideas Concept © Rawpixel.com – Fotolia.com
collaboration.jpg: Businessman Network Helping Other Concept © Rawpixel.com – Fotolia.com
cross border eCommerce.jpg: Social network concept. © fgnopporn – Fotolia.com
customer.jpg: Social network concept. Concept for website and mobil © PureSolution – Fotolia.com
digital transformation.jpg: Evolution of Man © Matej – Fotolia.com
eCommerce concept.jpg: Logistics chain © Jakub Jirsák – Fotolia.com
eCommerce shopping.jpg: Internet shopping. Man’s hands typing on computer key © PureSolution – Fotolia.com
Fulfillment.jpg: Manager standing with arms crossed in warehouse © vectorfusionart – Fotolia.com
global delivery.jpg: International package delivery concept, global purchase © Cybrain – Fotolia.com
laptop.jpg: businessman making credit card purchase online © everythingpossible – Fotolia.com
packets & parcels.jpg: Dream come true! Portrait of young pretty woman © deagreez – Fotolia.com
search engine optimization.jpg: Search engine optimization. © PureSolution – Fotolia.com
smartphone eCommerce.jpg: businessman working with smart phone and laptop © everythingpossible – Fotolia.com
tablet eCommerce.jpg: businessman working with digital tablet and book © everythingpossible – Fotolia.com
Technology strategy.jpg: net © vege – Fotolia.com
Technology.jpg: Orange and green technology background © spainter_vfx – Fotolia.com
transport.jpg: Truck Delivery Express © assetseller – Fotolia.com
warehousing.jpg: Smiling man carrying box © WavebreakMediaMicro – Fotolia.com
Web optimization concept design.jpg: Web optimization concept design. © PureSolution – Fotolia.com
working man.jpg: Young man working on computer in street cafe © mooshny – Fotolia.com
working woman.jpg: business woman hand working laptop computer © undrey – Fotolia.com
worldwide connected.jpg: World map connected, social network, globalization © fgnopporn – Fotolia.com

</p>





                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


















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