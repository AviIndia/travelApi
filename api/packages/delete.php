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

$id = $data['id'] ?? 0;

if(!$id){
    sendResponse(false,"Package ID required");
}

$sql = $conn->prepare("
DELETE FROM packages
WHERE id=?
");

$result = $sql->execute([$id]);

if(!$result){
    sendResponse(false,"Delete Failed");
}

sendResponse(
    true,
    "Package Deleted Successfully"
);