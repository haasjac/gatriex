<?php
    declare(strict_types=1);
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/database.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/response.php');
    
    function sendEmail($mail_to, $mail_subject, $mail_message): bool {
        $from_name = "Gatriex";
        $from_mail = "DoNotReply@Gatriex.com";
        $mail_subject .= " - " . date("r (T)");
        
        $encoding = "utf-8";
    
        // Preferences for Subject field
        $subject_preferences = array(
            "input-charset" => $encoding,
            "output-charset" => $encoding,
            "line-length" => 76,
            "line-break-chars" => "\r\n"
        );
    
        // Mail header
        $header = "Content-type: text/html; charset=".$encoding." \r\n";
        $header .= "From: ".$from_name." <".$from_mail."> \r\n";
        $header .= "MIME-Version: 1.0 \r\n";
        $header .= "Content-Transfer-Encoding: 8bit \r\n";
        $header .= "Date: ".date("r (T)")." \r\n";
        $header .= iconv_mime_encode("Subject", $mail_subject, $subject_preferences);
    
        // Send mail
        return mail($mail_to, $mail_subject, $mail_message, $header);
    }
    
    function sendForgetUsernameEmail($email): Response {
        global $db;
        $response = new Response();
        
        try {
            $stmt = $db->prepare("SELECT Username FROM User_Info WHERE Email = ?");
            $stmt->execute(array($email));
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $ex) {
            http_response_code(500);
            $response->data["Error"] = $ex->getMessage();
            $response->valid = false;
            return $response;
        }
        
        if (sizeof($rows) <= 0) {
            $response->data["Error"] = "User not Found.";
            $response->valid = false;
            return $response;
        }
        
        $username = $rows[0]["Username"];
        
        $mail_to = $email;
        $mail_subject = "Account Username";
        $mail_message = "A username reminder has been requested for the account associated with this email address. If you did not request this username reminder, please ignore this email.<br><br>";
        
        $mail_message .= "Your Username is: <b>" . $username . "</b>";
        
        $response->valid = sendEmail($mail_to, $mail_subject, $mail_message);
        
        if (!$response->valid) {
            $response->data["Error"] = "Email failed to send.";
        }
        
        return $response;
    }

    function sendForgetPasswordEmail($username, $token): Response {
        global $db;
        $response = new Response();
        
        try {
            $stmt = $db->prepare("SELECT Email FROM User_Info  WHERE Username = ?");
            $stmt->execute(array($username));
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $ex) {
            http_response_code(500);
            $response->data["Error"] = $ex->getMessage();
            $response->valid = false;
            return $response;
        }
        
        if (sizeof($rows) <= 0) {
            $response->data["Error"] = "User not Found.";
            $response->valid = false;
            return $response;
        }
        
        $mail_to = $rows[0]["Email"];
        
        $url = "https://gatriex.com/account/resetpassword.php?token=" . urlencode($token);
        
        $mail_subject = "Reset Password";
        $mail_message = "A password reset has been requested for the account associated with this email address. If you did not request this password reset, please ignore this email.<br><br>";
        
        $mail_message .= "If you did request a password reset, please <a href='" . $url ."'>click here</a> to create a new password. This link will only be active for 24 hours.<br><br>";
        
        $mail_message .= "If clicking on the link does not work, please copy and paste this link into your browser:<br><br>";
        
        $mail_message .= $url;
        
        $response->valid = sendEmail($mail_to, $mail_subject, $mail_message);
        
        if (!$response->valid) {
            $response->data["Error"] = "Email failed to send.";
        }
        
        return $response;
    }
?>