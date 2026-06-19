<?php

header("Content-Type: application/json");

require_once '../../middleware/auth.php';
require_once '../../config/database.php';
require_once '../../helpers/response.php';

$db = new Database();
$conn = $db->connect();

$data = json_decode(
    file_get_contents("php://input"),
    true
);

$package_id = $data['package_id'] ?? 0;
$travel_date = $data['travel_date'] ?? '';
$adults = $data['adults'] ?? 0;
$children = $data['children'] ?? 0;

if(!$package_id){
    sendResponse(false,"Package ID required");
}

$packageSql = $conn->prepare("
SELECT *
FROM packages
WHERE id=?
LIMIT 1
");

$packageSql->execute([$package_id]);

$package = $packageSql->fetch(PDO::FETCH_ASSOC);

if(!$package){
    sendResponse(false,"Package not found");
}

$totalPersons = $adults + $children;

$totalAmount =
    $package['package_cost']
    * $totalPersons;

$bookingNo =
    "BK"
    . date("YmdHis")
    . rand(100,999);

$sql = $conn->prepare("
INSERT INTO bookings(
    booking_no,
    package_id,
    user_id,
    travel_date,
    adults,
    children,
    total_amount
)
VALUES(
    ?,?,?,?,?,?,?
)
");

$result = $sql->execute([
    $bookingNo,
    $package_id,
    $user->id,
    $travel_date,
    $adults,
    $children,
    $totalAmount
]);

if(!$result){
    sendResponse(false,"Booking failed");
}

sendResponse(
    true,
    "Booking Created Successfully",
    [
        "booking_no"=>$bookingNo
    ]
);