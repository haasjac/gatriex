<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/credentials/error.php');

    class myLog {
        function error($message, $error) {
            global $errorPath;
            ini_set("error_log", $errorPath . "gatriex" . date("-Y-m") . ".log");
            $result = error_log($message . ":\r\n" . $error . "\r\n");
            return $result;
        }
    }
    
    $log = new myLog();
?>