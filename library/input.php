<?php

    class Input {

        public static function GetInput ($input, $var, $filter = FILTER_UNSAFE_RAW) {
            $result = filter_input($input, $var, $filter);
            if ($result === NULL) {
                $result = "";
            }
            return $result;
        }
        
        public static function GetCookie($cookie) {
            return Input::GetInput(INPUT_COOKIE, $cookie, FILTER_SANITIZE_STRING);
        }
        
        public static function GetPost($param) {
            return Input::GetInput(INPUT_POST, $param);
        }
        
        public static function GetGet($param) {
            return Input::GetInput(INPUT_GET, $param);
        }
        
        public static function GetEnv($param) {
            return Input::GetInput(INPUT_ENV, $param, FILTER_SANITIZE_NUMBER_INT);
        }

    }
    
?>