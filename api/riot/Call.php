<?php
    require_once(filter_input(INPUT_SERVER, "DOCUMENT_ROOT", FILTER_SANITIZE_STRING) . '/credentials/riot.php');
    
    function api_call($url) {
        global $api_token;
        $curl = curl_init();
        curl_setopt_array($curl, array(
        	CURLOPT_URL => $url,
        	CURLOPT_SSL_VERIFYPEER => FALSE,
        	CURLOPT_RETURNTRANSFER => TRUE,
        	CURLOPT_HTTPHEADER => array('X-Riot-Token: ' . $api_token)
        ));
        
        $result = curl_exec($curl);
        
        curl_close($curl);
        return $result;
    }

?>