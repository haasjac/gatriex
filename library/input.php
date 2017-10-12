<?php
    class myInput {
        function getInput ($input, $var, $filter = FILTER_UNSAFE_RAW) {
            $result = filter_input($input, $var, $filter);
            if ($result === NULL) {
                $result = "";
            }
            return $result;
        }
        
        function getCookie($cookie) {
            return $this->getInput(INPUT_COOKIE, $cookie, FILTER_SANITIZE_STRING);
        }
        
        function getPost($param) {
            return $this->getInput(INPUT_POST, $param);
        }
        
        function getGet($param) {
            return $this->getInput(INPUT_GET, $param);
        }
        
        function getEnv($param) {
            return $this->getInput(INPUT_ENV, $param, FILTER_SANITIZE_NUMBER_INT);
        }
    }
    
    $input = new myInput();
?>

