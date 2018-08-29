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

            $encryptedData = openssl_encrypt( $data, _Authentication::EncryptMethod, _Authentication::SecretKey, 0, $iv);

            $token = bin2hex($iv) . $encryptedData;

            return $token;
        }

        private static function DecryptAuth ($token): string {
            $iv = hex2bin(substr($token, 0, _Authentication::GetIvSize() * 2));
            $data = substr($token, _Authentication::GetIvSize() * 2);

            $decryptedData = openssl_decrypt( $data, _Authentication::EncryptMethod, _Authentication::SecretKey, 0, $iv);

            return $decryptedData;
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

            $hashPassword = password_hash($password, PASSWORD_DEFAULT);

            $encryptUser = Authentication::EncryptAuth($username);
            $encryptToken = Authentication::EncryptAuth($token);

            try {
                Database::Get()->beginTransaction();

                $stmt = Database::Get()->prepare("INSERT INTO User_Auth (Username, Password, Auth_Token) VALUES (?,?,?)");
                $stmt->execute(array($username, $hashPassword, $token));

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

            $result = array( "User" => $encryptUser, "Token" => $encryptToken);
            $response->valid = true;
            $response->data["Result"] = $result;

            return $response;
        }

        private static function RegenerateToken($username): Response {
            $response = new Response();

            $token = Authentication::GenerateToken();

            $encryptUser = Authentication::EncryptAuth($username);
            $encryptToken = Authentication::EncryptAuth($token);

            try {
                $stmt = Database::Get()->prepare("UPDATE User_Auth SET Auth_Token=? WHERE Username = ?");
                $stmt->execute(array($token, $username));
            } catch(PDOException $ex) {
                Log::Error("Database error in Authentication.php regenerateToken", $ex->getMessage());
                $response->data["Error"] = "Error handling request.";
                $response->valid = false;
                return $response;
            }

            $result = array( "User" => $encryptUser, "Token" => $encryptToken);
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

            $hashPassword = $rows[0]["Password"];

            $valid = password_verify($password, $hashPassword);

            if ($valid) {
                Authentication::UpdateHash($username, $password, $hashPassword, "Password");
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
                $decryptUser = Authentication::DecryptAuth($username);
                $decryptToken = Authentication::DecryptAuth($token);
            } catch (Exception $ex) {
                $response->data["Error"] = "Credentials have expired.";
                $response->valid = false;
                return $response;
            }

            try {
                $stmt = Database::Get()->prepare("SELECT Username, Auth_Token FROM User_Auth WHERE Username = ?");
                $stmt->execute(array($decryptUser));
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

            $authToken = $rows[0]["Auth_Token"];

            $valid = hash_equals($authToken, $decryptToken);

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
                $newHash = password_hash($value, PASSWORD_DEFAULT);
                try {
                    $stmt = Database::Get()->prepare("UPDATE User_Auth SET " . $type . " = ? WHERE Username = ?");
                    $stmt->execute(array($newHash, $username));
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

            $hashToken = password_hash($token, PASSWORD_DEFAULT);

            $encryptToken = Authentication::EncryptAuth($token);

            $time = date("Y-m-d H:i:s", time() + 60*60*24); // one day

            try {
                $stmt = Database::Get()->prepare("UPDATE User_Auth SET Forget_Token=?, Forget_Token_Expiry=? WHERE Username = ?");
                $stmt->execute(array($hashToken, $time, $username));
            } catch(PDOException $ex) {
                Log::Error("Database error in Authentication.php generateForgetToken", $ex->getMessage());
                $response->data["Error"] = "Error handling request.";
                $response->valid = false;
                return $response;
            }

            $response->data["Token"] = $encryptToken;
            $response->valid = true;

            return $response;
        }

        public static function ResetPasswordFromForgetToken($username, $password, $token): Response {
            $response = new Response();

            $decryptToken = Authentication::DecryptAuth($token);

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

            $hashToken = $rows[0]["Forget_Token"];

            if ($hashToken === "") {
                $response->data["Error"] = "Token has expired";
                $response->valid = false;
                return $response;
            }

            $currentTime = new DateTime(date("Y-m-d H:i:s"));
            $expiryTime = new DateTime($rows[0]["Forget_Token_Expiry"]);

            $valid = password_verify($decryptToken, $hashToken) && ($currentTime < $expiryTime);

            if ($valid) {
                try {
                    $hashPassword = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = Database::Get()->prepare("UPDATE User_Auth SET Forget_Token = ?, Forget_Token_Expiry = ?, Password = ? WHERE Username = ?");
                    $stmt->execute(array("", "", $hashPassword, $username));
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