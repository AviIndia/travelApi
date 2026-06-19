<?php

header("Content-Type: application/json");

require_once '../../config/database.php';
require_once '../../helpers/response.php';

$db = new Database();
$conn = $db->connect();

$data = json_decode(
    file_get_contents("php://input"),
    true
);

$sql = $conn->prepare("
INSERT INTO enquiries(
    name,
    email,
    phone,
    subject,
    message
)
VALUES(
    ?,?,?,?,?
)
");

$result = $sql->execute([
    $data['name'],
    $data['email'],
    $data['phone'],
    $data['subject'],
    $data['message']
]);

if(!$result){

    sendResponse(
        false,
        "Enquiry Failed"
    );

}

sendResponse(
    true,
    "Enquiry Submitted Successfully"
);