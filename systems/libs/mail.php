<?php

require_once "PHPMailer/PHPMailerAutoload.php";

class Mail extends PHPMailer
{
    var $priority = 1;
    var $From = null;
    var $FromName = null;
    var $Sender = null;
  
    function setMail()
    {
      global $settings;
      
        $this->From = $settings["email_noreply"];
        $this->FromName = $settings["name_responder"];
        $this->Sender = $settings["email_noreply"];

      $this->Priority = $this->priority;
    }
}


function mailer($to, $subject, $html_mail) {
    global $settings;
     if($settings["variant_send_mail"] == 2){
        
        smtpmail($to, $subject, $html_mail, $attach=false);

     }else{

		    $mail = new Mail();
        $mail->CharSet = "utf-8";
        $mail->Subject = $subject;        
        $mail->MsgHTML($html_mail);
        
        if($to){
          $arrays_to = explode(",", $to);
          foreach ($arrays_to as $key => $value) {
            $mail->AddAddress($value);
          }
        }
        
        $mail->setMail();
        if($mail->Send()){return true;}else{return false;}
        $mail->ClearAddresses();
        $mail->ClearAttachments();   

     }   
}

function smtpmail($to, $subject, $content, $attach=false)
{
    global $settings;
    $mail = new Mail(); 
    $mail->IsSMTP();
    try {
      $mail->Host       = $settings["smtp_host"];
      $mail->SMTPDebug  = $settings["SMTPDebug"];
      $mail->SMTPAuth   = $settings["smtp_auth"];
      $mail->SMTPSecure = $settings["smtp_secure"];
      $mail->Port       = $settings["smtp_port"];
      $mail->Username   = $settings["smtp_username"];
      $mail->Password   = $settings["smtp_password"];
      $mail->CharSet    = "utf-8";
      $mail->AddReplyTo($settings["email_noreply"], $settings["name_responder"]);
      
      if($to){
        $arrays_to = explode(",", $to);
        foreach ($arrays_to as $key => $value) {
          $mail->AddAddress($value);
        }
      }

      $mail->SetFrom($settings["email_noreply"], $settings["name_responder"]);
      $mail->Subject = htmlspecialchars($subject);
      if(!empty($settings["dkim_domain"])) $mail->DKIM_domain = $settings["dkim_domain"];
      if(!empty($settings["dkim_private"])) $mail->DKIM_private = $settings["dkim_private"];
      if(!empty($settings["dkim_selector"])) $mail->DKIM_selector = $settings["dkim_selector"];
      if(!empty($settings["dkim_passphrase"])) $mail->DKIM_passphrase = $settings["dkim_passphrase"];
      $mail->DKIM_identity = $mail->From;      
      $mail->MsgHTML($content);
      if($attach)  $mail->AddAttachment($attach);
      $mail->Send();
    } catch (phpmailerException $e) {
      echo $e->errorMessage();
    } catch (Exception $e) {
      echo $e->getMessage();
    }
}

?>