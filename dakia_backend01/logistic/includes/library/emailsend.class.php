<?php
/**
 * Email Send
 *
 * The purpose of this class is to provide a simple
 * interface for sending emails without having to be concerned
 * about setting from addresses etc.
 * This class is purely concerned with the mechanism of sending
 * emails, not on their content.
 *
 * Basic Usage:
 * 	$email = new EmailSend("The Subject", "The Body");
 *  $email->send("email@address.com", "To Name);
 * 	- that's it!
 */
class EmailSend
{
	private $subject = "";
	private $body = "";
	private $to_array = array();
	private $attachment_array = array();

	/**
	 * Constructor
	 *
	 * @param string $theSubject
	 * @param string $theBody
	 */
	public function __construct ($theSubject = "", $theBody = "")
	{
		$this->subject = $theSubject;
		$this->body = $theBody;
	}

	/**
	 * Send the email in HTML format.
	 *
	 * @param string $theEmailAddress
	 * @param string $theAddresseeName
	 * @return bool - indicates if send.
	 */
	public function send ($theEmailAddress = "", $theAddresseeName = "")
	{
		// add recipient.
		$this->addRecipient($theEmailAddress, $theAddresseeName);

		return $this->sendEmail(true);
	}


	/**
	 * Send email in plain text
	 *
	 * @param string $theEmailAddress
	 * @param string $theAddresseeName
	 * @return bool - send was successful
	 */
	public function sendAsPlainText($theEmailAddress = "", $theAddresseeName = "")
	{
		// add recipient.
		$this->addRecipient($theEmailAddress, $theAddresseeName);

		return $this->sendEmail(false);
	}

	/**
	 * Add user to the list or recipients
	 *
	 * @param string $email_address
	 * @param string $user_name
	 */
	public function addRecipient ($theEmailAddress, $theAddresseeName = "")
	{
		if ($theEmailAddress != "") $this->to_array[$theEmailAddress] = $theAddresseeName;
	}

	/***
	 * Add an attachment to the email
	 *
	 * @param Path to attachment file
	 */
	public function addAttachment($file_path)
	{
		$this->attachment_array[] = $file_path;
	}

	/**
	 * Actually send email in plain text or html
	 *
	 * @param bool - htmlFlag
	 * @return bool - successful
	 */
	private function sendEmail ($htmlFlag = true)
	{
		$phpMailer = new PHPMailer();

		// From
		$phpMailer->From     = CONFIG_SITE_EMAIL;
		$phpMailer->FromName = CONFIG_SITE_EMAIL_NAME;

		// settings
		$phpMailer->CharSet	= "utf-8";
		$phpMailer->IsHTML($htmlFlag);

		$extra = "";
		$lineBreak = ($htmlFlag ? "<br>" : "\n");

		// If we are capturing email, we want to know who the email was initially intended for.
		foreach ($this->to_array as $address => $name)
		{
			if (SETTING_EMAIL_CAPTURE == "")
			{
				$phpMailer->AddAddress($address, $name);
			}
			else
			{
				$extra .= "INTERCEPTED. To: $address ($name)$lineBreak";
			}
		}
		// capture details
		if (SETTING_EMAIL_CAPTURE != "")
		{
			$emailAddressArray = split(";", SETTING_EMAIL_CAPTURE);
			foreach ($emailAddressArray as $emailAddress)
			{
				if ($emailAddress != "") $phpMailer->AddAddress($emailAddress);
			}
		}
		else if (SETTING_EMAIL_BCC != "")
		{
			$emailAddressArray = split(";", SETTING_EMAIL_BCC);
			foreach ($emailAddressArray as $emailAddress)
			{
				if ($emailAddress != "") $phpMailer->AddBCC($emailAddress);
			}
		}

		// Add extra information (capture info) to the email
		if ($extra != "")
		{
			// If body contains a <body> tag, then insert extra after this tag
			if (strpos($this->body, "</body>") > 0)
			{
				$extra = "<hr />$extra";
				$this->body = str_replace("</body>", "$extra</body>", $this->body);
			}
			else
			{
				$extra .= "--------------------------------------------------------------$lineBreak$lineBreak";
				// insert extra at start of email
				$this->body = $extra . $this->body;
			}
		}

		// subject and body
		$phpMailer->Subject = $this->subject;
		$phpMailer->Body = $this->body;
		//echo "<p><textarea rows=10>" . $this->body . "</textarea></p>";

		// Add attachments
		foreach ($this->attachment_array as $file)
		{
			$phpMailer->AddAttachment($file);
		}

		// Just always use an SMTP server
		$phpMailer->IsSendmail();
	    $phpMailer->IsSMTP();
	    $phpMailer->Host = SETTING_SMTP_SERVER;
		// has the smtp server authentication been setup?
	    if (SETTING_SMTP_USER != "") {
	    	$phpMailer->SMTPAuth = true;
	    	$phpMailer->Username = SETTING_SMTP_USER;
	    	$phpMailer->Password = SETTING_SMTP_PASSWORD;
	    }

		// Send successful?
		if (!$phpMailer->Send())
		{
			echo "<p>EMAIL FAILED: " . $phpMailer->ErrorInfo . "<p>"; die;

			return false;
		}
		//echo "<p>Sent Ok.</p>";
		return true;
	}

	/**
	 * Allow sending of a general error email
	 *
	 * @param string $subject
	 * @param string $body
	 */
	public static function EmailError ($msg, $subject = "")
	{
		// generic error suject
		if ($subject == "")
		{
			$subject = "";
			if (isset($_SERVER["SERVER_NAME"])) $subject = $_SERVER["SERVER_NAME"] . " ";
			//
			$subject .= "Website Message";
		}
		// send email
		$email = new EmailSend($subject, $msg);
		return $email->sendAsPlainText(SETTING_EMAIL_ERROR);
	}

	/**
	 * Allow sending of a general error email
	 *
	 * @param string $subject
	 * @param string $body
	 */
	public static function EmailAdmin ($msg, $subject = "")
	{
		// generic error suject
		if ($subject == "") $subject = $_SERVER["SERVER_NAME"] . " Website Message";
		// send email
		$email = new EmailSend($subject, $msg);
		return $email->sendAsPlainText(SETTING_EMAIL_ADMIN);
	}
}