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

$booking_id =
$data['booking_id'] ?? 0;

$sql = $conn->prepare("
UPDATE bookings
SET booking_status='cancelled'
WHERE
id=?
AND user_id=?
");

$result = $sql->execute([
    $booking_id,
    $user->id
]);

if(!$result){

    sendResponse(
        false,
        "Cancel failed"
    );

}

sendResponse(
    true,
    "Booking Cancelled"
);