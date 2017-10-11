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
        
        function getPOST($param) {
            return $this->get_input(INPUT_POST, $param);
        }
    }
    
    $input = new myInput();
?>

