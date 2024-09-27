<?php


function genKey(){
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $randstring = null;
    for($i = 0; $i < 32; $i++) {
        $randstring .= $characters[
        rand(0, strlen($characters))
        ];
    }
    return $randstring;
}