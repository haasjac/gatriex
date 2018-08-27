<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/credentials/riot.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    function api_call($url) {
        global $api_token, $log;
        
        $response = new Response();
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_HTTPHEADER => array('X-Riot-Token: ' . $api_token)
        ));
        
        $result = curl_exec($curl);
        
        if (!$result) {
            $log->error("Curl error in Call.php", curl_error($curl));
            $response->data["Error"] = "Error handling request.";
            $response->valid = false;
        } else {
            $response->data["Response"] = json_decode($result);
            $response->valid = true;
        }
        
        curl_close($curl);
        
        if (isset($response->data["Response"])) {
            if (
                    isset($response->data["Response"]->status) && 
                    isset ($response->data["Response"]->status->message) &&
                    isset ($response->data["Response"]->status->status_code)
                ) {
                $response->data["Error"] = $response->data["Response"]->status->message;
                $response->valid = false;
            }
        }
        
        return $response;
    }

?>