<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/library/authentication.php');
    include_once($_SERVER['DOCUMENT_ROOT'] . '/library/mail.php');
    include_once($_SERVER['DOCUMENT_ROOT'] . '/library/validation.php');
    
    $username = "SpeedOfHeat";
    $email = "haasjac@umich.edu";
    $password = "password";
    $summoner = "SpeedOfHeat";
    
    $test = validateUsername("Gatriex");
    
    //var_dump(sizeof(false));
    
    var_dump($test);
?>