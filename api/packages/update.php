<?php

header("Content-Type: application/json");

require_once '../../config/database.php';
require_once '../../helpers/response.php';

$db = new Database();
$conn = $db->connect();

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'] ?? 0;

if(!$id){
    sendResponse(false,"Package ID required");
}

$sql = $conn->prepare("
UPDATE packages
SET
destination_id=?,
package_name=?,
short_description=?,
description=?,
total_days=?,
total_nights=?,
package_cost=?,
start_date=?,
end_date=?,
package_type=?,
max_person=?,
thumbnail=?,
status=?

WHERE id=?
");

$result = $sql->execute([
    $data['destination_id'],
    $data['package_name'],
    $data['short_description'],
    $data['description'],
    $data['total_days'],
    $data['total_nights'],
    $data['package_cost'],
    $data['start_date'],
    $data['end_date'],
    $data['package_type'],
    $data['max_person'],
    $data['thumbnail'],
    $data['status'],
    $id
]);

if(!$result){
    sendResponse(false,"Update Failed");
}

sendResponse(
    true,
    "Package Updated Successfully"
);