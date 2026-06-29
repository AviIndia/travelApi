<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json");


function sendResponse($status,$message,$data=[]){

    echo json_encode([
        "status"=>$status,
        "message"=>$message,
        "data"=>$data
    ]);

    exit;
}