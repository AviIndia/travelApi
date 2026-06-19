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