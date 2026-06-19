<?php

header("Content-Type: application/json");

require_once '../../middleware/auth.php';
require_once '../../config/database.php';
require_once '../../helpers/response.php';

$db = new Database();
$conn = $db->connect();

$sql = $conn->prepare("
SELECT

b.*,

p.package_name,
p.thumbnail

FROM bookings b

LEFT JOIN packages p
ON b.package_id=p.id

WHERE b.user_id=?

ORDER BY b.id DESC
");

$sql->execute([
    $user->id
]);

$bookings =
$sql->fetchAll(PDO::FETCH_ASSOC);

sendResponse(
    true,
    "Booking List",
    $bookings
);