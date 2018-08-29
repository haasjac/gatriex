<?php

    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/database.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/response.php');
    
    class Mail {

        public static function SendEmail($mailTo, $mailSubject, $mailMessage): bool {
            $fromName = "Gatriex";
            $fromMail = "DoNotReply@Gatriex.com";

            $encoding = "utf-8";

            // Mail header
            $header = "Content-type: text/html; charset=".$encoding." \r\n";
            $header .= "From: ".$fromName." <".$fromMail."> \r\n";
            $header .= "MIME-Version: 1.0 \r\n";
            $header .= "Content-Transfer-Encoding: 8bit \r\n";
            $header .= "Date: ".date("r (T)")." \r\n";

            // Send mail
            return mail($mailTo, $mailSubject, $mailMessage, $header);
        }
        
        public static function SendContactEmail($mailSubject, $mailMessage, $fromName, $fromMail): Response {
            $response = new Response();
            
            $mailTo = "Contact@gatriex.com";
            
            $encoding = "utf-8";

            // Mail header
            $header = "Content-type: text/html; charset=".$encoding." \r\n";
            $header .= "From: ".$fromName." <".$fromMail."> \r\n";
            $header .= "Reply-To: ".$fromName." <".$fromMail."> \r\n";
            $header .= "MIME-Version: 1.0 \r\n";
            $header .= "Content-Transfer-Encoding: 8bit \r\n";
            $header .= "Date: ".date("r (T)")." \r\n";

            // Send mail
            $response->valid = mail($mailTo, $mailSubject, $mailMessage, $header);
            
            if (!$response->valid) {
                $response->data["Error"] = "Email failed to send.";
            }

            return $response;
        }

        public static function SendForgetUsernameEmail($email): Response {
            $response = new Response();

            try {
                $stmt = Database::Get()->prepare("SELECT Username FROM User_Info WHERE Email = ?");
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

            $mailTo = $email;
            $mailSubject = "Account Username";
            $mailMessage = "A username reminder has been requested for the account associated with this email address. If you did not request this username reminder, please ignore this email.<br><br>";

            $mailMessage .= "Your Username is: <b>" . $username . "</b>";

            $response->valid = Mail::SendEmail($mailTo, $mailSubject, $mailMessage);

            if (!$response->valid) {
                $response->data["Error"] = "Email failed to send.";
            }

            return $response;
        }

        public static function SendForgetPasswordEmail($username, $token): Response {
            $response = new Response();

            try {
                $stmt = Database::Get()->prepare("SELECT Email FROM User_Info  WHERE Username = ?");
                $stmt->execute(array($username));
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch(PDOException $ex) {
                Log::Error("Database error in mail.php", $ex->getMessage());
                $response->data["Error"] = "Error handling request.";
                $response->valid = false;
                return $response;
            }

            if (sizeof($rows) <= 0) {
                $response->data["Error"] = "User not Found.";
                $response->valid = false;
                return $response;
            }

            $mailTo = $rows[0]["Email"];

            $url = "https://gatriex.com/account/resetpassword.php?token=" . urlencode($token);

            $mailSubject = "Reset Password";
            $mailMessage = "A password reset has been requested for the account associated with this email address. If you did not request this password reset, please ignore this email.<br><br>";

            $mailMessage .= "If you did request a password reset, please <a href='" . $url ."'>click here</a> to create a new password. This link will only be active for 24 hours.<br><br>";

            $mailMessage .= "If clicking on the link does not work, please copy and paste this link into your browser:<br><br>";

            $mailMessage .= $url;

            $response->valid = Mail::SendEmail($mailTo, $mailSubject, $mailMessage);

            if (!$response->valid) {
                $response->data["Error"] = "Email failed to send.";
            }

            return $response;
        }

    }

?>