<?php
class PaymentOptions implements iVisualComponent
{
	private static $item = null;

	/**
	 * Enter description here...
	 *
	 * @return
	 */
	public static function getItem()
	{
		if (self::$item == null)
		{
			self::$item = new PaymentOptions();
		}
		return self::$item;
	}


	public function init()
	{
	}

	public function render()
	{
		?>
		<div style="clear:both;text-align:center"">
		<?php
            if(!Sessionmanager::isLoggedIn()) { ?>
        <a id="payment_link" href="../payment/paypal.php?action=process" name="payment_link" href="#" style="color:#FFFFFF" class="btn-large">Pay Now &raquo;</a>
			<?php } ?>
         <?php if(Sessionmanager::getCustomerId()=="56") { ?>
        <a id="order_summary" href="../booking/order_complete.php" style="color:#FFFFFF" class="btn-large">&nbsp;&nbsp;Book &raquo;</a>
        <?php } ?>
            <br>
			
            Please tick <input name="chk_terms" id="chk_terms" type="checkbox" value="yes"> to confirm you have read our <a id="tc_show" href="../terms_conditions_wide.php">terms and conditions</a>
		
        
       
         
        
        </div>
		<?php
		return;
	}
}
?>