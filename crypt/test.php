<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/library/authentication.php');
    include_once($_SERVER['DOCUMENT_ROOT'] . '/library/mail.php');
    
    $username = "SpeedOfHeat";
    $email = "haasjac@umich.edu";
    $password = "password";
    $summoner = "SpeedOfHeat";
    
    //var_dump(login($username, $password, true));
    
    $test = regenerateToken($username);
    
    var_dump($test);
?>