<?php

header("Content-Type: application/json");

require_once '../../config/database.php';
require_once '../../helpers/response.php';

$db = new Database();
$conn = $db->connect();

$data = json_decode(file_get_contents("php://input"), true);

$destination_id = $data['destination_id'] ?? '';
$package_name = $data['package_name'] ?? '';

if(empty($destination_id) || empty($package_name)){
    sendResponse(false,"Destination and Package Name required");
}

$sql = $conn->prepare("
INSERT INTO packages(
    destination_id,
    package_name,
    short_description,
    description,
    total_days,
    total_nights,
    package_cost,
    start_date,
    end_date,
    package_type,
    max_person,
    thumbnail
)
VALUES(
    ?,?,?,?,?,?,?,?,?,?,?,?
)
");

$result = $sql->execute([
    $destination_id,
    $package_name,
    $data['short_description'] ?? '',
    $data['description'] ?? '',
    $data['total_days'] ?? 0,
    $data['total_nights'] ?? 0,
    $data['package_cost'] ?? 0,
    $data['start_date'] ?? null,
    $data['end_date'] ?? null,
    $data['package_type'] ?? '',
    $data['max_person'] ?? 0,
    $data['thumbnail'] ?? ''
]);

if(!$result){
    sendResponse(false,"Package creation failed");
}

sendResponse(
    true,
    "Package Created Successfully",
    [
        "package_id"=>$conn->lastInsertId()
    ]
);