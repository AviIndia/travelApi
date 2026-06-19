<?php

header("Content-Type: application/json");

require_once '../../middleware/auth.php';
require_once '../../config/database.php';
require_once '../../helpers/response.php';

$db = new Database();
$conn = $db->connect();

$id = $_GET['id'] ?? 0;

$sql = $conn->prepare("
SELECT

b.*,

p.package_name,
p.description,
p.thumbnail

FROM bookings b

LEFT JOIN packages p
ON b.package_id=p.id

WHERE
b.id=?
AND b.user_id=?

LIMIT 1
");

$sql->execute([
    $id,
    $user->id
]);

$data =
$sql->fetch(PDO::FETCH_ASSOC);

if(!$data){

    sendResponse(
        false,
        "Booking not found"
    );

}

sendResponse(
    true,
    "Booking Details",
    $data
);