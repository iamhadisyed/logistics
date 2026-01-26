<?php

// get settings
require_once("../includes/settings/config.inc.php");
@ session_start();
class Page extends BasePage
{
	private $error_list 		=	array();
	private $msg 			=	"";
	public $labelSaveOption 	=	false;
	private $labelFile 		=	null;
	private $percentageBar 		=	0;
	private $pass 			=	'';
	private $account 		=	'';
	public $AccountArray;
	public $userAccountJson		=	'';
	public $creditNumber		=	'';
    public $creditType		=	'';
	protected function init()
	{
		//echo "<pre>";
		$creditNoteId		=	$_GET['id'];
		$action				=	$_GET['action'];
		
		$creditNoteFilter           =	new InvoicesManual($creditNoteId);
		$users_account[]            =	$creditNoteFilter->getAccountNumber();
        $this->userAccountJson      =	json_encode($users_account);

		$this->InvoiceNumber         =	$creditNoteFilter->getInvoiceNo();
		
		// check admin user is authenticated
		if (!isset($_SESSION['admin']['id']) OR  is_null($_SESSION['admin']['id'])) {util_redirect("login.php");}

		$left_to_print = 0;
		$this->setTitle("Generating Labels");
		$this->msg = '<div class="clear" ></div>
  		<div class="main_formpage">
    	<h1 class="heading">Labels generation</h1><div class="form_container">
        <div class="gray_container">';
		$this->msg .= "Generating Invoice for <span id='newAccounts'>" . count($consignment_array) . " </span> accounts.";
		
	}// end of function

	/**
	 * Override to show the menu
	 *
	 */
	public function renderMenu()
	{
		$menu = new Adminmenu(Adminmenu::COURIERS);
		$menu->render();
	}
	/***
	 * Gets the full page to the folder
	 */
	public function getFullPath($fileName)
	{
		$path = "../_assets/pdf/" . date("Y_m_d", time()) . "/";

		if (!file_exists($path)) @mkdir($path, 0775);

		return $path . $fileName;
	}
	/***
	 * Head section
	 */
	protected function renderHead()
	{
		?>
        <style>
        #progress-bar-new{
			width:0px;
			display:block;
		}
        </style>
	<script type="text/javascript">
            var testarr = [];
            var progressBarTotalCount = 0;
            var progressBarCompletion = 0;

            $(function() {
                var accountArray			=	<?php echo  $this->userAccountJson;?>;
				
                var invoiceNumber			=	'<?php echo  $this->InvoiceNumber;?>';
				
                // Full credit note  PDF GENERATION Starts HERE 
                
                    var totalNumberAccount                  =	accountArray.length;
					
                    $('#newAccounts').html(totalNumberAccount);
                    $('#totalInvoice').html(totalNumberAccount);
                    $.each(accountArray, function(index, invoiceAccount) 
                    {
                        
                        $('#currentInvoice').html(index + 1);
                        $('#totalInvoiceConsignment').html('Please Wait, consignments are being loading..');
                        $.ajax({
                                type: "POST",
                                url: "manual_invoices_ajax.php", // your php file name
                                async: false,
                                data: {
									action: 'GET-MANUAL-INVOICE-DATA',
									account: invoiceAccount,
									invoice_number: invoiceNumber
                                },
                                success: function(save) {
									
                                   // alert(save);
                                    obj = JSON.parse(save);
                                    //if(confirm(obj.creditnote.C_NUMBER))
                                    //{
                                    //   return false;
                                    //}
                                    i   =   0
                                        $.ajax({
                                            type: "POST",
                                            url: "manual_invoices_ajax.php", // your php file name
                                            async: false,
                                            data: {
                                                    action: 'PUT-MANUAL-INVOICE-DATA-FULL-ON-FILE',
                                                    id: obj.manual_invoice.MN_NUMBER,
                                            },
                                            success: function(dataResponseSingle) {
												//confirm(totalNumberAccount);
                                                    progressBarCompletion = Math.ceil(((i + 1) /
                                                            progressBarTotalCount) * 100);
                                                    $("#progress-bar-new").attr('style', 'width:' +	progressBarCompletion + '%; display:block');
                                            //	$('.progress-bar').attr('style','width:' +progressBarCompletion + '%');
                                                    $("#progress-bar-completion").html(progressBarCompletion+'%');
                                                    $("#progress-bar-completion").html('Please wait... Merging in progress');
                                                    $.ajax({
                                                            type: "POST",
                                                            url: "manual_invoices_ajax.php", // your php file name
                                                            async: false,
                                                            data: {
                                                                    action: 'MERGE-CREDIT-FILE',
                                                                    invoiceNumber:	invoiceNumber
                                                            },
                                                            success: function(dataResponse) {
                                                                    //alert(dataResponse);
                                                                    $('#error-information').show();
                                                                    $('#error-information').append(
                                                                            "<br>Account Number: " +
                                                                            invoiceAccount + "<br>" + dataResponse +
                                                                            "<br><hr>");
                                                                    $("#progress-bar-completion").html('Completed');

                                                            }
                                                    });
                                            }
                                    });
                                }
                        });
                    });
                    // Full credit note PDF GENERATION END HERE 
                
            });

$("#progress-bar-new").attr('style', 'width:0px; display:block');

function process(data) {
	var fileLink = data.split('||');

	//alert(data);
	if (fileLink[0] == 'Link') {
		if (fileLink[1] != 'equal') {
			testarr.push(fileLink[1]);
		} else if (fileLink[1] == 'equal') {
			mergeLabelArray = testarr.join('||');
			labelId = fileLink[2];
			$.ajax({
				type: "POST",
				url: "bulkAjaxLabel_test.php", // your php file name
				data: {
					action: 'Merge',
					mergeLabelArray: mergeLabelArray,
					labelId: labelId
				},
				success: function(save) {
					window.location.replace(
						"http://oneworldexpress.co.uk/remote/main/label_list.php");
				}
			});
			testarr = new Array;
		}
	}
}



$(function() {
	$(".meter > span").each(function() {
		$(this)
			.data("origWidth", $(this).width())
			.width(0)
			.animate({
				width: $(this).data("origWidth")
			}, 1200);
	});
});

function creditNoteForFullInvoice()
{
   
}

	</script>
		<?php
	}

	/***
	* Content
	*/
	protected function renderBody()
	{
		?>
		<p><?php echo $this->msg; ?></p>

		<h2>please wait...</h2>
        <p>Current Invoice Generating  <span id="currentInvoice"></span> of <span id="totalInvoice"></span></p>
        <p>Current Invoice Shipment Data  <span id="totalInvoiceConsignment"></span></p>
        
        <div class="progress">
           <div class="progress-bar" id="progress-bar-new" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
              <span id="merging-status">Invoice Processing... (<span id="progress-bar-completion">0%</span>)</span>
           </div>
        </div>
		<div style="color:#F00; background:#F99; display:none; padding:10px;" id="error-information"></div>
            <input type="hidden" name="consignment_total" value="<?php echo @$consignment_total; ?>" />
            <input type="hidden" name="labelSaveOption" value="<?php echo @$labelSaveOption; ?>" />
            <input type="hidden" name="pass" value="<?php echo @$pass; ?>" />
            <input type="hidden" name="account" value="<?php echo @$account; ?>" />
            <input type="submit" name="btnNext" id="btnNext" style="display:none" />
		
        </div>
        
        

		
		<?php
		
	}

}// end class
/*------------------------------------------------------------------------------*/
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();