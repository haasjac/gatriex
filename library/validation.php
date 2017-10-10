<?php
    declare(strict_types=1);
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/response.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/database.php');
    
    function validateUsername($username): Response {
        global $db;
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
            http_response_code(500);
            $response->data["Error"] = $ex->getMessage();
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
        $response = new Response();
    
        $response->valid = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (!$response->valid) {
            $response->data["Error"] = "Invalid Email.";
        }
        
        return $response;
    }
    
?>