<?php
/* 
 * require_once('phpmailer.class.php');
 * https://github.com/PHPMailer/PHPMailer
  */
class SendEmail extends PHPMailer
{
    private $mail;

    /*
     * Create instance
     */

    public function autoInvoiceEmailSend($to, $subject, $message, $cc = array(), $attachment = array())
    {
        $output = array();
        $this->mail = new PHPMailer(true); // Passing `true` enables exceptions
        try {
            //Recipients
            $this->mail->setFrom('smart@smarttrack.co', 'SmartTrack System');
            if(is_array($to)) {
                foreach($to as $email) {
                    $this->mail->addAddress($email);     // Add a recipient
                }
            } else {
                $this->mail->addAddress($to);     // Add a recipient
            }
            $this->mail->addReplyTo('smart@smarttrack.co', 'SmartTrack System');
            if((is_array($cc)) && (count($cc) > 0)) {
                foreach($cc as $ccEmail) {
                    $this->mail->addCC($ccEmail);
                }
            }
            /* $this->mail->addBCC('bcc@example.com'); */
            if((is_array($attachment)) && (count($attachment) > 0)) {
                //Attachments
                foreach($attachment as $path) {
                    $this->mail->addAttachment($path);         // Add attachments
                }
            }
            //Content
            $this->mail->isHTML(true);                                  // Set email format to HTML
            $this->mail->Subject = $subject;
            $this->mail->Body    = nl2br($message);

            $returnInvoiceEmails    =   $this->mail->send();
            if($returnInvoiceEmails)
            {
                $output['status'] =  'success';
                $output['message'] =  'Email has been send successfully';
            }
            else
            {
                $output['status'] =  'error';
                $output['message'] = 'Message could not be sent. Mailer Error: ' . $this->mail->ErrorInfo;
            }
        } catch (Exception $e) {
            $output['status'] =  'error';
            $output['message'] = 'Message could not be sent. Mailer Error: ' . $this->mail->ErrorInfo;
        }
        return $output;
    }


    public function supplierReconciliationEmail($to, $subject, $message, $cc = array(), $attachment = array())
    {
        $output = array();
        $this->mail = new PHPMailer(true); // Passing `true` enables exceptions
        try {
            //Recipients
            $this->mail->setFrom('smart@smarttrack.co', 'SmartTrack System');
            if(is_array($to)) {
                foreach($to as $email) {
                    $this->mail->addAddress($email);     // Add a recipient
                }
            } else {
                $this->mail->addAddress($to);     // Add a recipient
            }
            $this->mail->addReplyTo('smart@smarttrack.co', 'SmartTrack System');
            if((is_array($cc)) && (count($cc) > 0)) {
                foreach($cc as $ccEmail) {
                    $this->mail->addCC($ccEmail);
                }
            }
            /* $this->mail->addBCC('bcc@example.com'); */
            if((is_array($attachment)) && (count($attachment) > 0)) {
                //Attachments
                foreach($attachment as $path) {
                    $this->mail->addAttachment($path);         // Add attachments
                }
            }
            //Content
            $this->mail->isHTML(true);                                  // Set email format to HTML
            $this->mail->Subject = $subject;
            $this->mail->Body    = nl2br($message);

            $returnReconciliationEmails    =   $this->mail->send();
            if($returnReconciliationEmails)
            {
                $output['status'] =  'success';
                $output['message'] =  'Email has been send successfully';
            }
            else
            {
                $output['status'] =  'error';
                $output['message'] = 'Message could not be sent. Mailer Error: ' . $this->mail->ErrorInfo;
            }
        } catch (Exception $e) {
            $output['status'] =  'error';
            $output['message'] = 'Message could not be sent. Mailer Error: ' . $this->mail->ErrorInfo;
        }
        return $output;
    }

}
