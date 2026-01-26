<?php
require_once("../includes/settings/config.inc.php");
if (isset($_POST['action']))
{
				$user = SessionManager::getUser();
				//$to = 'ops@oneworldexpress.com'; 
				$to = 'ops@oneworldexpress.com'; 
				//define the subject of the email 
				$subject = 'PRE ALERT ' .date('d-m-Y'). ' '.$user->getUsername(); 
				
				//define the headers we want passed. Note that they are separated with \r\n 
				$headers = "From: itsupport@oneworldexpress.com\r\nReply-To: itsupport@oneworldexpress.com"; 
				//add boundary string and mime type specification 
				$message = 'PRE ALERT FILE UPLOAD: http://oneworldexpress.co.uk/remote/_assets/preadvice/'.$user->getUsername(). '/'. $_POST['action'].' Please do not share this link with anyone without permission!';
				//send the email 
				$mail_sent = @mail( $to, $subject, $message, $headers ); 
				
			 	echo("Successfully Emailed Warehouse File: '". $_POST['action']."'");	

	
}

?>