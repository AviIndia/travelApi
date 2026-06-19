<?php

header("Content-Type: application/json");

require_once '../../config/database.php';
require_once '../../helpers/response.php';

$db = new Database();
$conn = $db->connect();

$data = json_decode(file_get_contents("php://input"),true);

$name     = $data['name'] ?? '';
$email    = $data['email'] ?? '';
$phone    = $data['phone'] ?? '';
$password = $data['password'] ?? '';

if(
    empty($name) ||
    empty($email) ||
    empty($password)
){
    sendResponse(false,"All fields required");
}

$check = $conn->prepare(
    "SELECT id FROM users WHERE email=?"
);

$check->execute([$email]);

if($check->rowCount()>0){

    sendResponse(false,"Email already exists");

}

$hashPassword = password_hash(
    $password,
    PASSWORD_DEFAULT
);

$sql = $conn->prepare(
    "INSERT INTO users
    (name,email,phone,password)
    VALUES(?,?,?,?)"
);

$result = $sql->execute([
    $name,
    $email,
    $phone,
    $hashPassword
]);

if($result){

    sendResponse(
        true,
        "Registration Successful"
    );

}

sendResponse(
    false,
    "Registration Failed"
);