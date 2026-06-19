<?php

require_once __DIR__.'/../helpers/jwt.php';
require_once __DIR__.'/../helpers/response.php';

$headers = getallheaders();

if(
    !isset($headers['Authorization'])
){
    sendResponse(
        false,
        "Unauthorized"
    );
}

$token = str_replace(
    'Bearer ',
    '',
    $headers['Authorization']
);

try{

    $user = JwtHelper::verify($token);

}catch(Exception $e){

    sendResponse(
        false,
        "Invalid Token"
    );

}