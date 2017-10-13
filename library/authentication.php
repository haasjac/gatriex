<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/database.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/session.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/response.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/input.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/log.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/credentials/authentication.php');

    class myAuthentication {
        function encrypt_auth ($data): string {
            global $secret_key, $iv_size, $encrypt_method;

            $iv = openssl_random_pseudo_bytes($iv_size);

            $encrypted_data = openssl_encrypt( $data, $encrypt_method, $secret_key, 0, $iv);

            $token = bin2hex($iv) . $encrypted_data;

            return $token;
        }

        function decrypt_auth ($token): string {
            global $secret_key, $iv_size, $encrypt_method;

            $iv = hex2bin(substr($token, 0, $iv_size * 2));
            $data = substr($token, $iv_size * 2);

            $decrypted_data = openssl_decrypt( $data, $encrypt_method, $secret_key, 0, $iv);

            return $decrypted_data;
        }

        function generate_token(): string {
            return bin2hex(openssl_random_pseudo_bytes(32));
        }


        //User Functions

        function createUser($username, $password, $email, $summoner): Response {
            global $db, $log;
            $response = new Response();

            $token = $this->generate_token();

            $hash_password = password_hash($password, PASSWORD_DEFAULT);

            $encrypt_user = $this->encrypt_auth($username);
            $encrypt_token = $this->encrypt_auth($token);

            try {
                $db->beginTransaction();

                $stmt = $db->prepare("INSERT INTO User_Auth (Username, Password, Auth_Token) VALUES (?,?,?)");
                $stmt->execute(array($username, $hash_password, $token));

                $stmt = $db->prepare("INSERT INTO User_Info (Username, Email, Summoner_Name) VALUES (?,?,?)");
                $stmt->execute(array($username, $email, $summoner));
                
                $stmt = $db->prepare("INSERT INTO Links (Username, Text, Link, Header) (SELECT ?, Text, Link, Header FROM Links WHERE Username = 'admin')");
                $stmt->execute(array($username));

                $db->commit();
            } catch(PDOException $ex) {
                $log->error("Database error in Authentication.php createUser", $ex->getMessage());
                $response->data["Error"] = "Error handling request.";
                $response->valid = false;
                $db->rollBack();

                return $response;
            }

            $result = array( "User" => $encrypt_user, "Token" => $encrypt_token);
            $response->valid = true;
            $response->data["Result"] = $result;

            return $response;
        }

        function regenerateToken($username): Response {
            global $db, $log;
            $response = new Response();

            $token = $this->generate_token();

            $encrypt_user = $this->encrypt_auth($username);
            $encrypt_token = $this->encrypt_auth($token);

            try {
                $stmt = $db->prepare("UPDATE User_Auth SET Auth_Token=? WHERE Username = ?");
                $stmt->execute(array($token, $username));
            } catch(PDOException $ex) {
                $log->error("Database error in Authentication.php regenerateToken", $ex->getMessage());
                $response->data["Error"] = "Error handling request.";
                $response->valid = false;
                return $response;
            }

            $result = array( "User" => $encrypt_user, "Token" => $encrypt_token);
            $response->valid = true;
            $response->data["Result"] = $result;

            return $response;
        }

        function validateUserFromPassword($username, $password): Response {
            global $db, $log;
            $response = new Response();

            try {
                $stmt = $db->prepare("SELECT Username, Password, Auth_Token FROM User_Auth WHERE Username = ?");
                $stmt->execute(array($username));
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch(PDOException $ex) {
                $log->error("Database error in Authentication.php validateUserFromPassword", $ex->getMessage());
                $response->data["Error"] = "Error handling request.";
                $response->valid = false;
                return $response;
            }

            if (sizeof($rows) <= 0) {
                $response->data["Error"] = "User not Found.";
                $response->valid = false;
                return $response;
            }

            $hash_password = $rows[0]["Password"];

            $valid = password_verify($password, $hash_password);

            if ($valid) {
                $this->updateHash($username, $password, $hash_password, "Password");
                $response->data["Username"] = $rows[0]["Username"];
                $response->data["Auth_Token"] = $rows[0]["Auth_Token"];
            }

            $response->valid = $valid;

            return $response;
        }

        function validateUserFromToken($username, $token): Response {
            global $db, $log;
            $response = new Response();

            try {
                $decrypt_user = $this->decrypt_auth($username);
                $decrypt_token = $this->decrypt_auth($token);
            } catch (Exception $ex) {
                $response->data["Error"] = "Credentials have expired.";
                $response->valid = false;
                return $response;
            }

            try {
                $stmt = $db->prepare("SELECT Username, Auth_Token FROM User_Auth WHERE Username = ?");
                $stmt->execute(array($decrypt_user));
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch(PDOException $ex) {
                $log->error("Database error in Authentication.php validateUserFromToken", $ex->getMessage());
                $response->data["Error"] = "Error handling request.";
                $response->valid = false;
                return $response;
            }

            if (sizeof($rows) <= 0) {
                $response->data["Error"] = "User not Found.";
                $response->valid = false;
                return $response;
            }

            $auth_token = $rows[0]["Auth_Token"];

            $valid = hash_equals($auth_token, $decrypt_token);

            if ($valid) {
                $response->data["Username"] = $rows[0]["Username"];
            } else {
                $response->data["Error"] = "Credentials have expired.";
            }

            $response->valid = $valid;

            return $response;
        }

        function updateHash($username, $value, $hash, $type) {
            global $db, $log;

            if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
                $new_hash = password_hash($value, PASSWORD_DEFAULT);
                try {
                    $stmt = $db->prepare("UPDATE User_Auth SET " . $type . " = ? WHERE Username = ?");
                    $stmt->execute(array($new_hash, $username));
                } catch(PDOException $ex) {
                    $log->error("Database error in Authentication.php updateHash", $ex->getMessage());
                }
            }
        }

        function getCurrentUser(): String {
            global $input, $session;
            
            $session->startSession();
            $user = "";
            
            $result = $this->validateUserFromToken($input->getCookie("Auth_Id"), $input->getCookie("Auth_Token"));
            if ($result->valid) {
                $user = $result->data["Username"];
            }

            return $user;
        }


        // Forgotten Password

        function generateForgetToken($username): Response {
            global $db, $log;
            $response = new Response();

            $token = $this->generate_token();

            $hash_token = password_hash($token, PASSWORD_DEFAULT);

            $encrypt_token = $this->encrypt_auth($token);

            $time = date("Y-m-d H:i:s", time() + 60*60*24); // one day

            try {
                $stmt = $db->prepare("UPDATE User_Auth SET Forget_Token=?, Forget_Token_Expiry=? WHERE Username = ?");
                $stmt->execute(array($hash_token, $time, $username));
            } catch(PDOException $ex) {
                $log->error("Database error in Authentication.php generateForgetToken", $ex->getMessage());
                $response->data["Error"] = "Error handling request.";
                $response->valid = false;
                return $response;
            }

            $response->data["Token"] = $encrypt_token;
            $response->valid = true;

            return $response;
        }

        function resetPasswordFromForgetToken($username, $password, $token): Response {
            global $db, $log;
            $response = new Response();

            $decrypt_token = $this->decrypt_auth($token);

            try {
                $stmt = $db->prepare("SELECT Forget_Token, Forget_Token_Expiry FROM User_Auth WHERE Username = ?");
                $stmt->execute(array($username));
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch(PDOException $ex) {
                $log->error("Database error in Authentication.php resetPasswordFromForgetToken", $ex->getMessage());
                $response->data["Error"] = "Error handling request.";
                $response->valid = false;
                return $response;
            }

            if (sizeof($rows) <= 0) {
                $response->data["Error"] = "User not Found.";
                $response->valid = false;
                return $response;
            }

            $hash_token = $rows[0]["Forget_Token"];

            if ($hash_token === "") {
                $response->data["Error"] = "Token has expired";
                $response->valid = false;
                return $response;
            }

            $current_time = new DateTime(date("Y-m-d H:i:s"));
            $expiry_time = new DateTime($rows[0]["Forget_Token_Expiry"]);

            $valid = password_verify($decrypt_token, $hash_token) && ($current_time < $expiry_time);

            if ($valid) {
                try {
                    $hash_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $db->prepare("UPDATE User_Auth SET Forget_Token = ?, Forget_Token_Expiry = ?, Password = ? WHERE Username = ?");
                    $stmt->execute(array("", "", $hash_password, $username));
                } catch(PDOException $ex) {
                    $log->error("Database error in Authentication.php resetPasswordFromForgetToken", $ex->getMessage());
                    $response->data["Error"] = "Error handling request.";
                    $response->valid = false;
                    return $response;
                }

                $result = $this->regenerateToken($username);
                if(!$result->valid) {
                    //log error
                }
                $response->valid = true;
                return $response;
            } else {
                $response->data["Error"] = "Token has expired";
                $response->valid = false;
                return $response;
            }
        }

        //Front End

        function login($username, $password, $remember): Response {
            global $session;
            
            $response = new Response();

            $user = "";
            $result = $this->validateUserFromPassword($username, $password);

            if ($result->valid) {
                $user = $result->data["Username"];
                $token = $result->data["Auth_Token"];

                $time = ($remember ? time() + 60*60*24*365 : 0); // 1 year or session
                setcookie("Auth_Id", $this->encrypt_auth($user), $time, "/", "gatriex.com", true, true);
                setcookie("Auth_Token", $this->encrypt_auth($token), $time, "/", "gatriex.com", true, true);
                $session->startSession();
            }

            $response->data["Username"] = $user;
            $response->valid = $result->valid;
            return $response;
        }

        function logout() {
            global $session;
            
            $time = time() - 3600;
            setrawcookie("Auth_Id", "", $time, "/", "gatriex.com", true, true);
            setrawcookie("Auth_Token", "", $time, "/", "gatriex.com", true, true);
            $session->endSession();
        }
    }
    
    $authentication = new myAuthentication();
    
?>