<?php

header("Content-Type: application/json");

require_once '../../config/database.php';
require_once '../../helpers/response.php';

$db = new Database();
$conn = $db->connect();

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;

$page = max($page,1);
$limit = max($limit,1);

$offset = ($page - 1) * $limit;

$search = $_GET['search'] ?? '';

$where = " WHERE status = 1 ";
$params = [];

if(!empty($search)){

    $where .= " AND (
        destination_name LIKE ?
        OR country LIKE ?
        OR state LIKE ?
        OR city LIKE ?
    )";

    $searchTerm = "%{$search}%";

    $params = [
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $searchTerm
    ];
}

$countSql = "SELECT COUNT(*) as total
             FROM destinations
             {$where}";

$countStmt = $conn->prepare($countSql);
$countStmt->execute($params);

$total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

$sql = "SELECT
            id,
            destination_name,
            country,
            state,
            city,
            best_time_to_visit,
            thumbnail
        FROM destinations
        {$where}
        ORDER BY id DESC
        LIMIT {$offset},{$limit}";

$stmt = $conn->prepare($sql);
$stmt->execute($params);

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

sendResponse(
    true,
    "Destination List",
    [
        "current_page"=>$page,
        "limit"=>$limit,
        "total_records"=>$total,
        "destinations"=>$data
    ]
);