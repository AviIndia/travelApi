<?php

header("Content-Type: application/json");

require_once '../../config/database.php';
require_once '../../helpers/response.php';

$db = new Database();
$conn = $db->connect();

$page = $_GET['page'] ?? 1;
$limit = $_GET['limit'] ?? 10;

$page = max(1,(int)$page);
$limit = max(1,(int)$limit);

$offset = ($page - 1) * $limit;

$where = " WHERE p.status = 1 ";
$params = [];

if(!empty($_GET['destination_id'])){

    $where .= " AND p.destination_id = ?";
    $params[] = $_GET['destination_id'];

}

if(!empty($_GET['search'])){

    $where .= " AND p.package_name LIKE ?";
    $params[] = "%".$_GET['search']."%";

}

$countSql = "
SELECT COUNT(*) total
FROM packages p
{$where}
";

$countStmt = $conn->prepare($countSql);
$countStmt->execute($params);

$total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

$sql = "
SELECT
    p.id,
    p.package_name,
    p.short_description,
    p.package_cost,
    p.total_days,
    p.total_nights,
    p.thumbnail,

    d.destination_name,
    d.country,
    d.state

FROM packages p

LEFT JOIN destinations d
ON p.destination_id = d.id

{$where}

ORDER BY p.id DESC

LIMIT {$offset},{$limit}
";

$stmt = $conn->prepare($sql);
$stmt->execute($params);

$packages = $stmt->fetchAll(PDO::FETCH_ASSOC);

sendResponse(
    true,
    "Package List",
    [
        "current_page"=>$page,
        "limit"=>$limit,
        "total_records"=>$total,
        "packages"=>$packages
    ]
);