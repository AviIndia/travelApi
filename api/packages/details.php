<?php

header("Content-Type: application/json");

require_once '../../config/database.php';
require_once '../../helpers/response.php';

$db = new Database();
$conn = $db->connect();

$id = $_GET['id'] ?? 0;

if(!$id){
    sendResponse(false,"Package ID required");
}

$sql = $conn->prepare("
SELECT
    p.*,
    d.destination_name,
    d.country,
    d.state,
    d.city

FROM packages p

LEFT JOIN destinations d
ON p.destination_id = d.id

WHERE p.id = ?

LIMIT 1
");

$sql->execute([$id]);

$package = $sql->fetch(PDO::FETCH_ASSOC);

if(!$package){
    sendResponse(false,"Package not found");
}


$itinerarySql = $conn->prepare("
SELECT *
FROM package_itinerary
WHERE package_id = ?
ORDER BY day_no ASC
");

$itinerarySql->execute([$id]);
$itinerary = $itinerarySql->fetchAll(PDO::FETCH_ASSOC);


$includeSql = $conn->prepare("
SELECT *
FROM package_includes
WHERE package_id = ?
");

$includeSql->execute([$id]);
$includes = $includeSql->fetchAll(PDO::FETCH_ASSOC);


$excludeSql = $conn->prepare("
SELECT *
FROM package_excludes
WHERE package_id = ?
");

$excludeSql->execute([$id]);
$excludes = $excludeSql->fetchAll(PDO::FETCH_ASSOC);


$gallerySql = $conn->prepare("
SELECT *
FROM package_gallery
WHERE package_id = ?
");

$gallerySql->execute([$id]);
$gallery = $gallerySql->fetchAll(PDO::FETCH_ASSOC);


sendResponse(
    true,
    "Package Details",
    [
        "package"   => $package,
        "itinerary" => $itinerary,
        "includes"  => $includes,
        "excludes"  => $excludes,
        "gallery"   => $gallery
    ]
);