<?php

header("Content-Type: application/json");

require_once '../../middleware/auth.php';
require_once '../../config/database.php';
require_once '../../helpers/response.php';

$db = new Database();
$conn = $db->connect();

$id = $_GET['id'] ?? 0;

if(!$id){

    sendResponse(
        false,
        "Trip ID required"
    );

}

$sql = $conn->prepare("
SELECT *
FROM ai_trip_plans
WHERE id=?
AND user_id=?
LIMIT 1
");

$sql->execute([
    $id,
    $user->id
]);

$trip = $sql->fetch(PDO::FETCH_ASSOC);

if(!$trip){

    sendResponse(
        false,
        "Trip not found"
    );

}

$daySql = $conn->prepare("
SELECT *
FROM ai_trip_days
WHERE trip_id=?
ORDER BY day_no ASC
");

$daySql->execute([$id]);

$days = $daySql->fetchAll(PDO::FETCH_ASSOC);

sendResponse(
    true,
    "Trip Details",
    [
        "trip"=>$trip,
        "days"=>$days
    ]
);