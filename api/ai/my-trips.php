<?php

header("Content-Type: application/json");

require_once '../../middleware/auth.php';
require_once '../../config/database.php';
require_once '../../helpers/response.php';

$db = new Database();
$conn = $db->connect();

$sql = $conn->prepare("
SELECT

id,
destination,
start_date,
end_date,
travelers,
budget,
created_at

FROM ai_trip_plans

WHERE user_id=?

ORDER BY id DESC
");

$sql->execute([
    $user->id
]);

$trips = $sql->fetchAll(PDO::FETCH_ASSOC);

sendResponse(
    true,
    "My Trips",
    $trips
);