<?php

    require_once ($_SERVER['DOCUMENT_ROOT'] . '/credentials/error.php');

    class Log {

        public static function Error($message, $error) {
            ini_set("error_log", _Error::ErrorPath . "gatriex" . date("-Y-m") . ".log");
            $result = error_log($message . ":\r\n" . $error . "\r\n");
            return $result;
        }

    }
    
?>