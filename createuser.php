<?php
    include('myFunction.php');
    $user = 'user2';
    $mdp = '$iutinfo';
    $us = getUser('admin');
    print("-----------");
    print_r($us[0]['user']);
?>