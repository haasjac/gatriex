<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/database.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/session.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/response.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/input.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/log.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/credentials/authentication.php');

    class Authentication {

        private static function EncryptAuth ($data): string {
            $iv = openssl_random_pseudo_bytes(_Authentication::GetIvSize());

            $encrypted_data = openssl_encrypt( $data, _Authentication::EncryptMethod, _Authentication::SecretKey, 0, $iv);

            $token = bin2hex($iv) . $encrypted_data;

            return $token;
        }

        private static function DecryptAuth ($token): string {
            $iv = hex2bin(substr($token, 0, _Authentication::GetIvSize() * 2));
            $data = substr($token, _Authentication::GetIvSize() * 2);

            $decrypted_data = openssl_decrypt( $data, _Authentication::EncryptMethod, _Authentication::SecretKey, 0, $iv);

            return $decrypted_data;
        }

        private static function GenerateToken(): string {
            return bin2hex(openssl_random_pseudo_bytes(32));
        }

        public static function GenerateGuid(): string {
            $hex = bin2hex(openssl_random_pseudo_bytes(16));
            $arr = str_split($hex, 4);
            $result = $arr[0] . $arr[1] . "-" . $arr[2] . "-" . $arr[3] . "-" . $arr[4] . "-" . $arr[5] . $arr[6] . $arr[7];
            return $result;
        }


        //User Functions

        public static function CreateUser($username, $password, $email, $summoner, $region): Response {
            $response = new Response();

            $token = Authentication::GenerateToken();

            $hash_password = password_hash($password, PASSWORD_DEFAULT);

            $encrypt_user = Authentication::EncryptAuth($username);
            $encrypt_token = Authentication::EncryptAuth($token);

            try {
                Database::Get()->beginTransaction();

                $stmt = Database::Get()->prepare("INSERT INTO User_Auth (Username, Password, Auth_Token) VALUES (?,?,?)");
                $stmt->execute(array($username, $hash_password, $token));

                $stmt = Database::Get()->prepare("INSERT INTO User_Info (Username, Email, Summoner_Name, Region) VALUES (?,?,?,?)");
                $stmt->execute(array($username, $email, $summoner, $region));
                
                $stmt = Database::Get()->prepare("INSERT INTO Links (Username, Text, Link, Header) (SELECT ?, Text, Link, Header FROM Links WHERE Username = 'admin')");
                $stmt->execute(array($username));

                Database::Get()->commit();
            } catch(PDOException $ex) {
                Log::Error("Database error in Authentication.php createUser", $ex->getMessage());
                $response->data["Error"] = "Error handling request.";
                $response->valid = false;
                Database::Get()->rollBack();

                return $response;
            }

            $result = array( "User" => $encrypt_user, "Token" => $encrypt_token);
            $response->valid = true;
            $response->data["Result"] = $result;

            return $response;
        }

        private static function RegenerateToken($username): Response {
            $response = new Response();

            $token = Authentication::GenerateToken();

            $encrypt_user = Authentication::EncryptAuth($username);
            $encrypt_token = Authentication::EncryptAuth($token);

            try {
                $stmt = Database::Get()->prepare("UPDATE User_Auth SET Auth_Token=? WHERE Username = ?");
                $stmt->execute(array($token, $username));
            } catch(PDOException $ex) {
                Log::Error("Database error in Authentication.php regenerateToken", $ex->getMessage());
                $response->data["Error"] = "Error handling request.";
                $response->valid = false;
                return $response;
            }

            $result = array( "User" => $encrypt_user, "Token" => $encrypt_token);
            $response->valid = true;
            $response->data["Result"] = $result;

            return $response;
        }

        private static function ValidateUserFromPassword($username, $password): Response {
            $response = new Response();

            try {
                $stmt = Database::Get()->prepare("SELECT Username, Password, Auth_Token FROM User_Auth WHERE Username = ?");
                $stmt->execute(array($username));
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch(PDOException $ex) {
                Log::Error("Database error in Authentication.php validateUserFromPassword", $ex->getMessage());
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
                Authentication::UpdateHash($username, $password, $hash_password, "Password");
                $response->data["Username"] = $rows[0]["Username"];
                $response->data["Auth_Token"] = $rows[0]["Auth_Token"];
            }

            $response->valid = $valid;

            return $response;
        }

        public static function ValidateUserFromToken(): Response {
			$username = Input::GetCookie("Auth_Id");
			$token = Input::GetCookie("Auth_Token");

            $response = new Response();

            try {
                $decrypt_user = Authentication::DecryptAuth($username);
                $decrypt_token = Authentication::DecryptAuth($token);
            } catch (Exception $ex) {
                $response->data["Error"] = "Credentials have expired.";
                $response->valid = false;
                return $response;
            }

            try {
                $stmt = Database::Get()->prepare("SELECT Username, Auth_Token FROM User_Auth WHERE Username = ?");
                $stmt->execute(array($decrypt_user));
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch(PDOException $ex) {
                Log::Error("Database error in Authentication.php validateUserFromToken", $ex->getMessage());
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

        private static function UpdateHash($username, $value, $hash, $type) {
            if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
                $new_hash = password_hash($value, PASSWORD_DEFAULT);
                try {
                    $stmt = Database::Get()->prepare("UPDATE User_Auth SET " . $type . " = ? WHERE Username = ?");
                    $stmt->execute(array($new_hash, $username));
                } catch(PDOException $ex) {
                    Log::Error("Database error in Authentication.php updateHash", $ex->getMessage());
                }
            }
        }

        public static function GetCurrentUser(): String {            
            Session::StartSession();
            $user = "";
            
            $result = Authentication::ValidateUserFromToken();
            if ($result->valid) {
                $user = $result->data["Username"];
            }

            return $user;
        }


        // Forgotten Password

        public static function GenerateForgetToken($username): Response {
            $response = new Response();

            $token = Authentication::GenerateToken();

            $hash_token = password_hash($token, PASSWORD_DEFAULT);

            $encrypt_token = Authentication::EncryptAuth($token);

            $time = date("Y-m-d H:i:s", time() + 60*60*24); // one day

            try {
                $stmt = Database::Get()->prepare("UPDATE User_Auth SET Forget_Token=?, Forget_Token_Expiry=? WHERE Username = ?");
                $stmt->execute(array($hash_token, $time, $username));
            } catch(PDOException $ex) {
                Log::Error("Database error in Authentication.php generateForgetToken", $ex->getMessage());
                $response->data["Error"] = "Error handling request.";
                $response->valid = false;
                return $response;
            }

            $response->data["Token"] = $encrypt_token;
            $response->valid = true;

            return $response;
        }

        public static function ResetPasswordFromForgetToken($username, $password, $token): Response {
            $response = new Response();

            $decrypt_token = Authentication::DecryptAuth($token);

            try {
                $stmt = Database::Get()->prepare("SELECT Forget_Token, Forget_Token_Expiry FROM User_Auth WHERE Username = ?");
                $stmt->execute(array($username));
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch(PDOException $ex) {
                Log::Error("Database error in Authentication.php resetPasswordFromForgetToken", $ex->getMessage());
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
                    $stmt = Database::Get()->prepare("UPDATE User_Auth SET Forget_Token = ?, Forget_Token_Expiry = ?, Password = ? WHERE Username = ?");
                    $stmt->execute(array("", "", $hash_password, $username));
                } catch(PDOException $ex) {
                    Log::Error("Database error in Authentication.php resetPasswordFromForgetToken", $ex->getMessage());
                    $response->data["Error"] = "Error handling request.";
                    $response->valid = false;
                    return $response;
                }

                Authentication::RegenerateToken($username);
                $response->valid = true;
                return $response;
            } else {
                $response->data["Error"] = "Token has expired";
                $response->valid = false;
                return $response;
            }
        }

        //Front End

        public static function Login($username, $password, $remember): Response {            
            $response = new Response();

            $user = "";
            $result = Authentication::ValidateUserFromPassword($username, $password);

            if ($result->valid) {
                $user = $result->data["Username"];
                $token = $result->data["Auth_Token"];

                $time = ($remember ? time() + 60*60*24*365 : 0); // 1 year or session
                setcookie("Auth_Id", Authentication::EncryptAuth($user), $time, "/", _Authentication::AuthDomain, true, true);
                setcookie("Auth_Token", Authentication::EncryptAuth($token), $time, "/", _Authentication::AuthDomain, true, true);
                Session::StartSession();
            }

            $response->data["Username"] = $user;
            $response->valid = $result->valid;
            return $response;
        }

        public static function Logout() {            
            $time = time() - 3600;
            setrawcookie("Auth_Id", "", $time, "/", _Authentication::AuthDomain, true, true);
            setrawcookie("Auth_Token", "", $time, "/", _Authentication::AuthDomain, true, true);
            Session::EndSession();
        }
        
        public static function UpdateField($username, $value, $field): Response {            
            $response = new Response();
            
            try {
                $table = "User_Info";
                if ($field === "Password") {
                    $table = "User_Auth";
                    $value = password_hash($value, PASSWORD_DEFAULT);
                }
                $sql = "UPDATE " . $table . " SET " . $field . " = ? WHERE Username = ?";
                $stmt = Database::Get()->prepare($sql);
                $stmt->execute(array($value, $username));
            } catch(PDOException $ex) {
                Log::Error("Database error in Authentication.php updateField" . $field, $ex->getMessage());
                $response->data["Error"] = "Error handling request.";
                $response->valid = false;
                return $response;
            }
            
            $response->valid = true;
            return $response;
        }
    }
    
?>