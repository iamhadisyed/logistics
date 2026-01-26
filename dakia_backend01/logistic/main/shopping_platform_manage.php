<?php
// get settings
require_once("../includes/settings/config.inc.php");
     
        include_classes([  
                    'shoppingplatform.class',
                    'shoppingplatformfilter.class',
                    'usershoppingplatforms.class',
                    'usershoppingplatformsfilter.class',
                    'marketplaces.class',
                    'marketplacesfilter.class']);
class Page extends BasePage {

    public $shopping_data;
    private $user = null;

    protected function init() {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            '' => Translation::GetCaption("SHOPPING_PLATFORM")
        );

        $this->user = SessionManager::getUser();

        if (isset($this->form_vars["func"]) && trim($this->form_vars["func"]) == 'GET_SHOPING_DETAIL') {
            $cid = $this->form_vars['cid'];
            $userShoppingPlatforms = new ShoppingPlatform($cid);
            if ($userShoppingPlatforms->getId() > 0) {
                $description = $userShoppingPlatforms->getDescription();
                if ($description != '' || $description != NULL)
                    echo $description;
                else
                    echo '<div class="alert alert-info">Oops! We are currently working on manuals. It will be available soon. <br /> Sorry for any inconvenience cause. For any other information please email to ITSUPPORT@oneworldexpress.com</div>';
            }
            else {
                //echo '<div class="alert alert-danger">No description found</div>';
				echo '<div class="alert alert-info">Oops! We are currently working on manuals. It will be available soon. <br /> Sorry for any inconvenience cause. For any other information please email to ITSUPPORT@oneworldexpress.com</div>';
            }
            die;
        } else if (isset($this->form_vars["func"]) && trim($this->form_vars["func"]) == 'GET_SHOPING_SUSPENDKEYS') {
            $cid = $this->form_vars['cid'];
            $this->shopping = new UserShoppingPlatforms($cid);
            $userShoppingPlatforms = new UserShoppingPlatforms($cid);
            if ($userShoppingPlatforms->getUserId() == $this->user->getId()) {
                $userShoppingPlatforms->setStatus(2);
                $userShoppingPlatforms->save();
            } else {
                echo '<div class="alert alert-danger">No description found</div>';
            }
            die;
        } else if (isset($this->form_vars["func"]) && trim($this->form_vars["func"]) == 'GET_SHOPING_REMOVEKEYS') {
            $cid = $this->form_vars['cid'];
            $this->shopping = new UserShoppingPlatforms($cid);
            $userShoppingPlatforms = new UserShoppingPlatforms($cid);
            //echo $userShoppingPlatforms->getUserId()." == ".$sessionManager->getId() ;
            if ($userShoppingPlatforms->getUserId() == $this->user->getId()) {
                $userShoppingPlatforms->setStatus(0);
                $userShoppingPlatforms->save();
            } else {
                echo '<div class="alert alert-danger">No description found</div>';
            }
            die;
        } else if (isset($this->form_vars["func"]) && trim($this->form_vars["func"]) == 'GET_SHOPING_ACTIVEKEYS') {
            $cid = $this->form_vars['cid'];
            $this->shopping = new UserShoppingPlatforms($cid);
            $userShoppingPlatforms = new UserShoppingPlatforms($cid);
            if ($userShoppingPlatforms->getUserId() == $this->user->getId()) {
                $userShoppingPlatforms->setStatus(1);
                $userShoppingPlatforms->save();
            } else {
                echo '<div class="alert alert-danger">No description found</div>';
            }
            die;
        } else if (isset($this->form_vars["func"]) && trim($this->form_vars["func"]) == 'GET_USER_SHOPING_DETAIL') {
            $cid = $this->form_vars['cid'];
            // common initialisation for ths page
            $this->setTitle("Manage Shopping Platforms");
            $this->shoppingPlateformFilter = new UserShoppingPlatformsFilter();
            $this->shoppingPlateformFilter->addFilter(" status <> 0 ");
            $this->shoppingPlateformFilter->addFilter(" user_id = '" . $this->user->getId() . "' and shopping_platform_id = '" . DbAccess3::escape($cid) . "'");
            $userDetaisl = $this->shoppingPlateformFilter->getColumnList("id, reference, site_url, user_id, status, api_key, api_secrete, date_created ");
            if (count($userDetaisl) > 0) {
                $shoppingKey = new ShoppingPlatform($cid);
                foreach ($userDetaisl as $keyData) {
                    ?>
                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 carrier ng-scope" id='<?= $keyData->getId(); ?>'>
                        <div class="panel panel-default">
                            <div class="panel-body" style=" padding: 5px 0 !important;" id="">
                                <table class="table table-striped table-bordered table-advance table-hover">
                                    <tr >
                                        <th >Site</th><td ><?= $keyData->getSiteUrl(); ?></td>
                                    </tr>
                                    <tr >
                                        <th >Reference</th>
                                        <td ><?= $keyData->getReference(); ?></td>
                                    </tr>
                                    <tr >
                                        <th >Platform Key</th>
                                        <td ><?= $shoppingKey->getPluginKey(); ?></td>
                                    </tr>
                                    <th >Api Key</th>
                                    <td ><?= $keyData->getApiKey(); ?></td>
                                    </tr>
                                    <tr >
                                        <th >Api Secret</th>
                                        <td ><?= $keyData->getApiSecrete(); ?></td>
                                    </tr>
                                    <tr >
                                        <th >Date</th>
                                        <td ><?= date('d-m-Y', $keyData->getDateCreated()); ?></td>
                                    </tr>
                                    <tr >
                                        <th >Status</th>
                                        <td ><?= ($keyData->getStatus() == 1) ? 'Active' : 'Inactive'; ?></td>
                                    </tr>
                                </table>  
                            </div>
                            <br clear="all">
                            <br clear="all">
                            <div class="panel-footer clearfix">
                                <a class="btn btn-sm btn-default pull-left" data-removekeys='<?= $keyData->getId(); ?>'>
                                    <span class="fa fa-book"></span> Delete
                                </a>
                                <a class="btn btn-sm btn-success pull-right" id='a-<?= $keyData->getId(); ?>' style=" <?= ($keyData->getStatus() == 1) ? 'display:none;' : ''; ?> " data-activekeys='<?= $keyData->getId(); ?>'>
                                    <span class="fa fa-plug"></span> Activate
                                </a>
                                <a class="btn btn-sm btn-success pull-right" id='s-<?= $keyData->getId(); ?>' style=" <?= ($keyData->getStatus() == 2) ? 'display:none;' : ''; ?> " data-suspendkeys='<?= $keyData->getId(); ?>'>
                                    <span class="fa fa-plug"></span> Suspend
                                </a>
                            </div>
                        </div>
                        <br clear="all">
                        <br clear="all">
                    </div>


                    <?php
                }
            } else {
                echo '<div class="alert alert-danger">No description found</div>';
            }
            die;
        }



        if (isset($this->form_vars["form_action"]) && trim($this->form_vars["form_action"]) == 'SAVE') {
            if ($this->validateUrl($this->form_vars["site_url"])) {
                $userShoppingPlatforms = new UserShoppingPlatforms();
                $userShoppingPlatforms->setShoppingPlatformId($this->form_vars["shopping_platforms"]);
                $userShoppingPlatforms->setReference($this->form_vars["reference"]);


                $userShoppingPlatforms->setSiteUrl($this->form_vars["site_url"]);
                $userShoppingPlatforms->setUserId($this->user->getId());
                $api_key = md5($_SESSION['admin']['id'] . $_SESSION['admin']['firstname'] . $_SESSION['admin']['surname'] . date('H:i:s'));
                $api_secert = md5($_SESSION['user_name'] . $_SESSION['user_type'] . $_SESSION['user_account'] . date('H:i:s'));
                $userShoppingPlatforms->setApiKey($api_key);
                $userShoppingPlatforms->setApiSecrete($api_secert);
                $userShoppingPlatforms->setStatus(1);
                $userShoppingPlatforms->setDateCreated(time());
                $userShoppingPlatforms->save();
                $output["status"] = "success";
                $output["message"] = formatMessages(SUCCESS_KEY_GENERATION);
                //util_redirect("../main/shopping_platform_manage.php");
            } else {
                $output["status"] = "fail";
                $output["message"] = formatMessages(ERROR_URL_INVALID);
                // echo '<div class="alert alert-danger"></div>';
            }
            echo json_encode($output);
            die;
        }

        if (isset($_GET["action"])) {
            $action = base64_decode($_GET['action']);
            $spid = (int) base64_decode($_GET['spid']);

            $userShoppingPlatforms = new UserShoppingPlatforms($spid);

            if ($userShoppingPlatforms->getUserId() == $this->user->getId()) {
                if ($action == 'delete')
                    $userShoppingPlatforms->setStatus(0);
                else if ($action == 'suspend')
                    $userShoppingPlatforms->setStatus(2);
                else if ($action == 'activate')
                    $userShoppingPlatforms->setStatus(1);

                $userShoppingPlatforms->save();
            }
            util_redirect("../main/shopping_platform_manage.php");

            die;
        }
        // common initialisation for ths page
        $this->setTitle("Manage Shopping Platforms");
        $this->shoppingPlateformFilter = new UserShoppingPlatformsFilter();
        $this->shoppingPlateformFilter->addFilter(" status <> 0 ");
        $this->shoppingPlateformFilter->addFilter(" user_id = '" . $this->user->getId() . "'");
    }

    private function validateUrl($url) {
        $file = $url;
        $file_headers = @get_headers($file);

        if (!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') {
            $exists = false;
        } else {
            $exists = true;
        }

        return $exists;
    }

    /*     * *
     * Insert content into HEAD section of html page.
     */

    public function renderHead() {
        
    }

    public function renderFooter() {
        ?>
        <script src="../js/validator.min.js" type="text/javascript"></script> 

         <script>
            $(document).ready(function(e){
                $('#errorDiv').hide();
                $('input').tooltip();
                $('select').tooltip();
                $("#btnSaveConsignment").click(function()
                {
                    $("#form_action").val("SAVE");
                    $('#adminForm').validator().on('submit',function (e) 
                    {
                        if (e.isDefaultPrevented()) 
                        {
                            var	msgError	=	'';

                            var siteUrl     =   $('#site_url').val();
                            if(siteUrl != '')
                            {
                                if(!isUrlValid(siteUrl))
                                {
                                    msgError    += "Please enter correct url.<br>";
                                    $('#errorDiv').show();
                                    $('#errorDiv').html(msgError);
                                    return false;
                                }
                            }
                            return false;
                        }  
                        else
                        {
                            var form_data = $("#adminForm").serialize();
                            $.ajax({
                            url: "shopping_platform_manage.php",
                            data: form_data,
                            type: "POST",
                            dataType : "json",
                            success: function(response) {
                                if(response.status == 'success'){
                                   $('#errorDiv').show(); 
                                   $('#errorDiv').removeClass('alert-danger').addClass('alert-success');     
                                   $('#errorDiv').html(response.message);
                                   $('*[data-pload]').click();
                                }else{
                                    $('#errorDiv').show();
                                    $('#errorDiv').removeClass('alert-success').addClass('alert-danger');     
                                    $('#errorDiv').html(response.message);
                                }
                                }
                             }); 
                             return false;
                        }
                       
                    });
                    $("#adminForm").submit();
            //alert($("#form_action").val());
            //$("#form_action").val("SAVE");
               
        }); 
                $('*[data-pload]').click(function () 
                {
                    var e = $(this);
                    var id = e.data('pload');
                    $("#shopping_platforms").val(id);
                    $.post('shopping_platform_manage.php', {func: 'GET_USER_SHOPING_DETAIL', cid: id}, function (d) {
                        $("#shopping-list-div").html(d);
                        $('*[data-activekeys]').click(function () {
                            var e = $(this);
                            var id = e.data('activekeys');
                            $.post('shopping_platform_manage.php', {func: 'GET_SHOPING_ACTIVEKEYS', cid: id}, function (d) {
                                $("#a-"+id).hide();
                                $("#s-"+id).show();
                            });
                        });
                        $('*[data-suspendkeys]').click(function () 
                        {
                            var e = $(this);
                            var id = e.data('suspendkeys');
                            $.post('shopping_platform_manage.php', {func: 'GET_SHOPING_SUSPENDKEYS', cid: id}, function (d) {
                             $("#s-"+id).hide();
                             $("#a-"+id).show();
                         });
                        });
                        $('*[data-removekeys]').click(function () 
                        {
                            var e = $(this);
                            var id = e.data('removekeys');
                            $.post('shopping_platform_manage.php', {func: 'GET_SHOPING_REMOVEKEYS', cid: id}, function (d) {
                                $("#"+id).remove();
                                $('#errorDiv').show();
                            });
                        });
                    });
                });
                $('*[data-pdeload]').click(function () 
                {
                    var e = $(this);
                    var id = e.data('pdeload');
                    $.post('shopping_platform_manage.php', {func: 'GET_SHOPING_DETAIL', cid: id}, function (d) {
                        $("#shoppingplateform-details").html(d);
                    });
                });
            });
function clearurl(){
    window.location = 'shopping_platform_manage.php';
        //$('#reference').val(''); $('#site_url').val('');
        //$('#errorDiv').attr('style','display:none;');
        //$('#errorDiv').hide();
    }
    function isUrlValid(url) {
        return /^(https?|s?ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(url);
    }
</script>
        <style>
            .carrier .panel-body {
                padding: 50px 0;
				min-height: 155px;
            }   
            .panel {
                background-color: #fff;
                /*border: 1px solid transparent;**/
                border-radius: 4px !important;
                box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05) !important;
                margin-bottom: 20px !important;
            }
            .panel-body .img-wrapper .img-responsive {
                max-height: initial;
            }
        </style> 
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        $shopping_data = @$this->shopping_data;
        $shopping_data = $shopping_data[0];
        ?>

        <div class="main_formpage">
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption"> <i class="fa fa-plug"></i>
                         <?php echo Translation::GetCaption("SHOPPING_PLATFORMS"); ?>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 carrier ng-scope">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="img-wrapper">
                                        <img class="img-responsive center-block" src="../_assets/images/integration/amazon_com.png" alt="AMAZON" uib-popover="amazon" popover-trigger="mouseenter" >
                                    </div>
                                </div>
                                <div class="panel-footer clearfix">
                                    <a class="btn btn-sm dark btn-outline pull-left" data-pdeload="-1" data-target="#manual-shopping" data-toggle="modal">
                                        <span class="fa fa-book"></span> Manual
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>marketplace.php?marketplaceid=1" target="_blank" class="btn btn-sm blue btn-outline pull-right ng-scope"  data-pload="1" ">
                                        <span class="fa fa-plug"></span> Connect
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 carrier ng-scope">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="img-wrapper">
                                        <img class="img-responsive center-block" src="../_assets/images/integration/Ebay-ebay.png" alt="EBAY" uib-popover="ebay" popover-trigger="mouseenter" >
                                    </div>
                                </div>
                                <div class="panel-footer clearfix">
                                    <a class="btn btn-sm dark btn-outline pull-left" data-pdeload="-1" data-target="#manual-shopping" data-toggle="modal">
                                        <span class="fa fa-book"></span> Manual
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>marketplace.php?marketplaceid=2" target="_blank" class="btn btn-sm blue btn-outline pull-right ng-scope"  data-pload="2" >
                                        <span class="fa fa-plug"></span> Connect
                                    </a>
                                </div>
                            </div>
                        </div>
        <?php
        $ShoppingFilter = new ShoppingPlatformFilter();
        $ShoppingFilter->addFieldEqualFilter("active", "=", "1");
        $ShoppingFilter->addFieldEqualFilter("plugin_key", "!=", "smarttrack");
        $ShoppingData = $ShoppingFilter->getColumnList('integration_logo, title, translation_key,display_option, connect_url');
        if (count($ShoppingData) > 0) {
            foreach ($ShoppingData as $shipping_integration) {
                ?>
                                <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 carrier ng-scope">
                                    <div class="panel panel-default">
                                        <div class="panel-body">
                                            <div class="img-wrapper">
                                                <img class="img-responsive center-block" src="../_assets/images/integration/<?= $shipping_integration->getIntegrationLogo(); ?>" alt="<?= $shipping_integration->getTitle(); ?>" uib-popover="<?= $shipping_integration->getTitle(); ?>" popover-trigger="mouseenter" >
                                            </div>
                                        </div>
                                        <div class="panel-footer clearfix">
                <?php if ($shipping_integration->getDisplayOption() == 0 || $shipping_integration->getDisplayOption() == 1) { ?>
                                                <a class="btn btn-sm dark btn-outline pull-left" data-pdeload="<?= $shipping_integration->getId(); ?>" data-target="#manual-shopping" data-toggle="modal">
                                                    <span class="fa fa-book"></span> Manual
                                                </a>
                    <?php
                }
                if ($shipping_integration->getDisplayOption() == 0 || $shipping_integration->getDisplayOption() == 2) {
                    ?>
                                                <button class="btn btn-sm blue btn-outline pull-right ng-scope"  data-pload="<?= $shipping_integration->getId(); ?>" data-target="#email-invoice" data-toggle="modal">
                                                    <span class="fa fa-plug"></span> Connect
                                                </button>
                    <?php
                }
                ?>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            $UserMarketPlacesFilter = new MarketPlacesFilter();
                            $UserMarketPlacesFilter->addFieldFilter("is_active", "1");
                            $UserMarketPlacesFilter->addFieldFilter("integration_type", "2");
                            $UserMarketPlacesFilter->addFilter("id not in (select distinct parent_id from market_places)");
                            $UserMarketPlacesFilter->addFilter(" (id IN (SELECT market_places_id FROM user_market_places_mapping WHERE user_id = '" . $this->user->getId() . "' ) or parent_id IN (SELECT market_places_id FROM user_market_places_mapping WHERE user_id = '" . $this->user->getId() . "'))");
                            $UserMarketPlacesPlatformData = $UserMarketPlacesFilter->getList();
                            $ShoppingPlatformFilter = new ShoppingPlatformFilter();
                            $UserShippingPlatformData = $ShoppingPlatformFilter->getList();
                            if (count($UserMarketPlacesPlatformData) > 0) {
                                foreach ($UserMarketPlacesPlatformData as $shoping_platform) {
                                    ?>
                                    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 carrier ng-scope">
                                        <div class="panel panel-default">
                                            <div class="panel-body">
                                                <div class="img-wrapper">
                                                    <img class="img-responsive center-block" src="../_assets/images/integration/<?= $shoping_platform->getIntegrationLogo(); ?>" alt="<?= $shoping_platform->getTitle(); ?>" uib-popover="<?= $shoping_platform->getTitle(); ?>" popover-trigger="mouseenter" >
                                                </div>
                                            </div>
                                            <div class="panel-footer clearfix">
                                                <a class="btn btn-sm dark btn-outline pull-left" data-pdeload="<?= $shoping_platform->getManualLink(); ?>">
                                                    <span class="fa fa-book"></span> Manual
                                                </a>
                                                <a class="btn btn-sm blue btn-outline pull-right ng-scope" href="<?= $shoping_platform->getPageLink() . "?id=" . md5($shoping_platform->getId()); ?>" >
                                                    <span class="fa fa-plug"></span> Connect
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade bs-modal-lg" tabindex="-1" role="dialog" id="manual-shopping" >
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Add Integration</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12" id="shoppingplateform-details">                            
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content --> 
            </div>
            <!-- /.modal-dialog --> 
        </div>
        <form method="post"  id="adminForm" name="adminForm"  role="form">
            <div class="modal fade bs-modal-lg" tabindex="-1" role="dialog" id="email-invoice" >
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Add Manage Shopping Platforms</h4>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-danger" id="errorDiv"></div>
                            <div class="row">
                                <div class="col-md-12">                            
                                    <input type="hidden" class="form-control" name="shopping_platforms" id="shopping_platforms" value="<?php echo @$shopping_platforms; ?>" placeholder="shopping_platforms" />
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <div class="input-group"> 
                                                <span class="input-group-addon"> <i class="fa fa-user"></i> </span>
                                                <div class="input-icon right">
                                                    <i class="fa tooltips font-red" data-original-title="Reference is mandatory">*</i>
                                                    <input name="reference" id="reference" value="<?php echo @$reference; ?>" class="form-control"   placeholder="<?php echo ucfirst(Translation::GetCaption('REFERENCE')); ?>" rel="tooltip" data-original-title="<?php echo ucfirst(Translation::GetCaption('REFERENCE')); ?>" type="text" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div> 
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <div class="input-group"> 
                                                <span class="input-group-addon"> <i class="fa fa-link"></i> </span>
                                                <div class="input-icon right">
                                                    <i class="fa tooltips font-red" data-original-title="Site URL is mandatory">*</i>
                                                    <input name="site_url" id="site_url" value="<?php echo $site_url; ?>" class="form-control"  placeholder="Site URL" rel="tooltip" data-original-title="Site URL" type="text" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div> 
                                </div>
                            </div>
                            <br clear="all">
                            <div class="row">
                                <div class="col-md-12">                            
                                    <div class="col-sm-3 center"></div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <div class="input-group"> 
                                                <button type="button" class="btn btn-default btn-close-model" data-dismiss="modal" onclick="clearurl();">Close</button>
                                                <button class="btn btn-primary" type="button" name="btnSaveConsignment" id="btnSaveConsignment">Add & Generate Token</button>
                                            </div>
                                        </div>
                                    </div> 
                                    <div class="col-sm-3"></div>
                                </div>
                            </div>
                            <br clear="all">
                            <br clear="all">
                            <div class="row">
                                <div class="col-md-12" id="shopping-list-div">                            
                                </div>
                            </div>
                            <input type="hidden" name="form_action" id="form_action" value=""  />
                        </div>
                        <!--<div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>-->
                    </div>
                    <!-- /.modal-content --> 
                </div>
                <!-- /.modal-dialog --> 
            </div>
        </form>
        <?php
    }

    /**
     * Return to source page
     * @param $filter_set
     */
    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::COURIERS);
        $menu->render();
    }

}

// class
/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
