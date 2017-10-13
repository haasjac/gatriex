<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/errorPage.php');

    class myRedirect {
        function redirect($url, $status_code = 302, $replace = true) {
            if (headers_sent() === false)
            {
                header('Location: ' . $url, $replace, $status_code);
            }
            exit();
        }
        
        function requireUser($user) {
            global $errorPage;
            
            if ($user === "") {
                $errorPage->render(403);
                exit();
            }
        }
        
        function requireNoUser($user) {  
            if ($user !== "") {
                $this->redirect("/");
                exit();
            }
        }
    }
    
    $redirect = new myRedirect();
?>
