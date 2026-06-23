<?php

function sendResponse($status,$message,$data=[]){

    echo json_encode([
        "status"=>$status,
        "message"=>$message,
        "data"=>$data
    ]);

    exit;
}