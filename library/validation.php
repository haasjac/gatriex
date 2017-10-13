<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/response.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/database.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/log.php');
    
    class myValidation {
        function validateUsername($username): Response {
            global $db, $log;
            $response = new Response();

            $response->valid = strlen($username) >= 3;
            if (!$response->valid) {
                $response->data["Error"] = "Username must be at least 3 characters.";
                return $response;
            }

            try {
                $stmt = $db->prepare("SELECT 1 FROM User_Auth WHERE Username = ?");
                $stmt->execute(array($username));
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch(PDOException $ex) {
                $log->error("Database error in validataion.php validateUsername", $ex->getMessage());
                $response->data["Error"] = "Error validating username.";
                $response->valid = false;

                return $response;
            }

            if (sizeof($rows) > 0) {
                $response->valid = false;
                $response->data["Error"] = "Username already in use.";
                return $response;
            }

            return $response;
        }

        function validatePassword($password): Response {
            $response = new Response();

            $response->valid = strlen($password) >= 8;
            if (!$response->valid) {
                $response->data["Error"] = "Password must be at least 8 characters.";
            }

            return $response;
        }


        function confirmPassword($password, $confirmPassword, $field = "Passwords"): Response {
            $response = new Response();

            $response->valid = $password === $confirmPassword;
            if (!$response->valid) {
                $response->data["Error"] = $field . " do not match.";
            }

            return $response;
        }

        function validateEmail($email): Response {
            global $db, $log;
            $response = new Response();

            $response->valid = filter_var($email, FILTER_VALIDATE_EMAIL);
            if (!$response->valid) {
                $response->data["Error"] = "Invalid Email.";
                return $response;
            }
            
            try {
                $stmt = $db->prepare("SELECT 1 FROM User_Info WHERE Email = ?");
                $stmt->execute(array($email));
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch(PDOException $ex) {
                $log->error("Database error in validataion.php validateEmail", $ex->getMessage());
                $response->data["Error"] = "Error validating email.";
                $response->valid = false;

                return $response;
            }

            if (sizeof($rows) > 0) {
                $response->valid = false;
                $response->data["Error"] = "Email already in use.";
                return $response;
            }

            return $response;
        }
    }
    
    $validation = new myValidation();
?>