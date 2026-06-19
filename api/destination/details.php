<?php

header("Content-Type: application/json");

require_once '../../config/database.php';
require_once '../../helpers/response.php';

$db = new Database();
$conn = $db->connect();

$id = $_GET['id'] ?? 0;

if(!$id){
    sendResponse(false,"Destination ID required");
}

$sql = $conn->prepare("
    SELECT *
    FROM destinations
    WHERE id = ?
    LIMIT 1
");

$sql->execute([$id]);

$destination = $sql->fetch(PDO::FETCH_ASSOC);

if(!$destination){
    sendResponse(false,"Destination not found");
}

$packageSql = $conn->prepare("
    SELECT
        id,
        package_name,
        package_cost,
        total_days,
        total_nights,
        thumbnail
    FROM packages
    WHERE destination_id = ?
    AND status = 1
");

$packageSql->execute([$id]);

$packages = $packageSql->fetchAll(PDO::FETCH_ASSOC);

$hotelSql = $conn->prepare("
    SELECT
        id,
        hotel_name,
        rating,
        image,
        phone
    FROM hotels
    WHERE destination_id = ?
");

$hotelSql->execute([$id]);

$hotels = $hotelSql->fetchAll(PDO::FETCH_ASSOC);

sendResponse(
    true,
    "Destination Details",
    [
        "destination"=>$destination,
        "packages"=>$packages,
        "hotels"=>$hotels
    ]
);