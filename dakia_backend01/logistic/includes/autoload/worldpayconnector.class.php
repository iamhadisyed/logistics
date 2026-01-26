<?php

class WorldpayConnector {
	public  $errors     = array();
	private $testMode   = false;
	private $futurePay  = false;
	private $formFields = array();
	private $secret;

	/**
	* @param Basket  basket		If set, generate formFields from given Basket
	* @param boolean test_mode	If true, use worldpay's test mode
	* @param boolean futurePay_enabled	If true, add fields to take an optional extra payment.
	*/
	public function __construct($basket=null, $test_mode=false, $futurePay_enabled=false  ) {
		$this->secret    = CONFIG_WORLDPAY_MD5_SECRET;
		$this->testMode  = $test_mode;
		$this->futurePAy = $futurePay_enabled;
		if ($basket) { $this->setFromBasket($basket); }
	}

	public function setFromBasket($basket) {
		$base_payment_amount = $basket->getBasketTotalAmountPlusVat();
		$extra_payment_limit = $base_payment_amount;

		// Basic WorldPay fields
		$this->formFields['instId']   = CONFIG_WORLDPAY_MERCHANT_ID;
		$this->formFields['cartId']   = $basket->getId();
		$this->formFields['amount']   = $base_payment_amount;
		$this->formFields['currency'] = 'GBP';
		$this->formFields['desc']     = $basket->getMiniBasketSummary();

		// Contact details
		$this->formFields['name']    = $basket->getCustomerName();
		$this->formFields['tel']     = $basket->getPhoneNumber();
		$this->formFields['email']   = $basket->getEmailAddress();

		// Not easy to figure out a postal address!
		// If one item in basket, it could be either collection or delivery
		// address. If multiple items, then lots to choose between.

		// If "fixContact" is set then contact fields will be readonly on the
		// Worldpay form.
		//DISABLED$this->formFields['fixContact'] = '';

		if ($futurePay) {
			$this->addFuturePayFields(1, $extra_payment_limit);
		}

		if ($this->testMode) {
			$this->formFields['testMode'] = 100;
			$this->formFields['name']     = 'AUTHORISED';	// Default test result
		}

		$this->addSecurityFields();
	}

	public function formFieldsForWorldpay() {
		return $this->formFields;
	}

	public function urlForWorldPay() {
		if ($this->testMode) {
			return CONFIG_WORLDPAY_TEST_URL;
		}
		return CONFIG_WORLDPAY_LIVE_URL;
	}


	public function hasErrors() {
		return sizeof($this->errors) > 0;
	}

	protected function err($msg) {
		$this->errors[] = $msg;
		EmailSend::EmailError($msg, 'WorldPay Connector Error');
		//TEMP error_log($msg);
	}

	/**
	* Verifies the response from worldpay and upates status codes accordingly
	*
	* @param array	$r	response $_POST from WorldPay
	* @return string	One of AUTHORISED|REFUSED|ERROR
	*/
	public function processResponseFromWorldpay($r) {
		if ( !isset($r['transStatus'])
			|| !in_array($r['transStatus'], array('Y','C')) ) {
			$this->err('Unknown transStatus code: '.$r['transStatus']);
			return 'ERROR';
		}
		$trans_status = $r['transStatus'];

		if ($trans_status == 'C') {
			// If the order is cancelled, we just want to exit.
			return 'CANCELLED';
		}

		if (!isset($r['transId'])) {
			$this->err('No transId returned from worldpay');
			return 'ERROR';
		}
		$trans_id = $r['transId'];

		if (!isset($r['instId']) || $r['instId'] != CONFIG_WORLDPAY_MERCHANT_ID) {
			$this->err('Unknown merchant ID in response: '.$r['instId']);
			return 'ERROR';
		}

		if (!isset($r['cartId']) || !is_numeric($r['cartId'])) {
			$this->err('Invalid cartId in response: '.$r['cartId']);
			return 'ERROR';
		}

		$b = $this->mkBasket($r['cartId']);
		if (!is_object($b)) {
			$this->err('Unable to create Basket from response cartId: '.$r['cartId']);
			return 'ERROR';
		}

		if (!isset($r['currency']) || $r['currency'] != 'GBP') {
			$this->err('Unexpected currency in response: '.$r['currency']);
			return 'ERROR';
		}

		// Note that the amount in the response will be formatted as a decimal
		$expected_amount = sprintf("%.2f", $b->getBasketTotalAmountPlusVat());
		if (!isset($r['amount']) || $r['amount'] != $expected_amount) {
			$this->err('Unexpected amount in response: '.$r['amount'].' vs '.$expected_amount);
			return 'ERROR';
		}

		if (!isset($r['name']) || $r['name']=='') {
			$this->err('No name for shopper in response');
			return 'ERROR';
		}

		if (!isset($r['address']) || $r['address']=='') {
			$this->err('No address for shopper in response');
			return 'ERROR';
		}

		if (!isset($r['country']) || $r['country']=='') {
			$this->err('No country for shopper in response');
			return 'ERROR';
		}

		// also receive: transTime, authAmount,
		// authCurrency, authCost, ipAddress
		// futurePayId, futurePayStatusChange
		// If using "verified by visa" or similar, also get: authentication

		// Assuming we reach this point then all is well and payment received.
		// WP return address in a single field, except for country and
		// postcode
		$addr_list = explode("\n", $r['address']);
		$addr_list[3] = $r['country'];
		$addr_list[4] = $r['postcode'];
		$this->markOrderComplete($trans_id, $b->getId(), $r['name'], $addr_list);
		return 'PROCESSED';
	}

	protected function mkBasket($id) {
		return new Basket($id);
	}

	/**
	* @param integer	$trans_id	Transaction ID (from Worldpay)
	* @param integer	$basket_id	Basket->getId()
	* @param string		$shopper_name
	* @param array		$address (including country, postcode)
	*/
	protected function markOrderComplete($trans_id, $basket_id, $shopper_name, $address) {
		$orderComplete = new OrderCompletion();
		$orderComplete->setBillAddress($shopper_name, $address[0], $address[1], $address[2], $address[3], $address[4]);
		$orderComplete->complete($basket_id, $trans_id);
	}

	/**
	* If futurePay is enabled, we can claim one or more extra payments.
	* Note that you'll want different parameters if option != 0
	* See WorldPay docs for details.
	*/
	private function addFuturePayFields($num_payments = 1, $extra_payment_limit = 0) {
		// Option 0 on the limited agreement
		$this->formFields['futurePayType'] = 'limited';
		$this->formFields['option']        = 0;
		// Can only take money between these dates
		$this->formFields['startDate']     = date('Y-m-d');
		$this->formFields['endDate']       = '';
		// Only one payment taken with a max. amount as shown
		$this->formFields['noOfPayments']  = $num_payments;
		$this->formFields['amountLimit']   = sprintf('%.2f', $extra_payment_limit);
		// These should be unset in our case.
		$this->formFields['intervalUnit']  = '';
		$this->formFields['intervalMult']  = '';
	}

	/**
	* We can add an MD5 hash over some joined fields and a shared secret.
	*/
	private function addSecurityFields() {
		$fields_to_hash = array('amount', 'currency', 'cartId');
		$vals_to_hash   = array( $this->secret );
		foreach ($fields_to_hash AS $f) {
			$vals_to_hash[] = $this->formFields[$f];
		}

		$this->formFields['signatureFields'] = join(':', $fields_to_hash);
		$val_to_hash = join(':', $vals_to_hash);
		$this->formFields['signature'] = md5( join(':', $vals_to_hash) );
		//DISABLED $this->formFields['authValidTo'] = time() + 15*60;	// 15 min timeout
	}
}

?>
