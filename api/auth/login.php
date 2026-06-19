<?php

header("Content-Type: application/json");

require_once '../../config/database.php';
require_once '../../helpers/response.php';
require_once '../../helpers/jwt.php';

$db = new Database();
$conn = $db->connect();

$data = json_decode(
    file_get_contents("php://input"),
    true
);

$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

if(
    empty($email) ||
    empty($password)
){
    sendResponse(
        false,
        "Email and Password required"
    );
}

$sql = $conn->prepare(
    "SELECT * FROM users
    WHERE email=? LIMIT 1"
);

$sql->execute([$email]);

$user = $sql->fetch(PDO::FETCH_ASSOC);

if(!$user){

    sendResponse(
        false,
        "Invalid Email"
    );
}

if(
    !password_verify(
        $password,
        $user['password']
    )
){

    sendResponse(
        false,
        "Invalid Password"
    );
}

$token = JwtHelper::generate($user);

sendResponse(
    true,
    "Login Successful",
    [
        "token"=>$token,
        "user"=>[
            "id"=>$user['id'],
            "name"=>$user['name'],
            "email"=>$user['email'],
            "role"=>$user['role']
        ]
    ]
);