<?php

header("Content-Type: application/json");

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/constant.php';
require_once __DIR__ . '/../../helpers/response.php';

$db = new Database();
$conn = $db->connect();

$page = $_GET['page'] ?? 1;
$limit = $_GET['limit'] ?? 10;

$page = max(1, (int)$page);
$limit = max(1, (int)$limit);

$offset = ($page - 1) * $limit;

$where = " WHERE p.status = 1 ";
$params = [];

/*
|--------------------------------------------------------------------------
| Existing Filter
|--------------------------------------------------------------------------
*/

if (!empty($_GET['destination_id'])) {

    $where .= " AND p.destination_id = ?";
    $params[] = $_GET['destination_id'];
}

if (!empty($_GET['search'])) {

    $where .= " AND p.package_name LIKE ?";
    $params[] = "%" . $_GET['search'] . "%";
}

/*
|--------------------------------------------------------------------------
| New Filter For Homepage Search
|--------------------------------------------------------------------------
*/

if (!empty($_GET['destination'])) {

    $where .= " AND d.destination_name LIKE ?";
    $params[] = "%" . $_GET['destination'] . "%";
}

if (!empty($_GET['start_date'])) {

    $where .= " AND p.start_date >= ?";
    $params[] = $_GET['start_date'];
}

/*
|--------------------------------------------------------------------------
| Total Count
|--------------------------------------------------------------------------
*/

$countSql = "
SELECT COUNT(*) total
FROM packages p
LEFT JOIN destinations d
ON p.destination_id = d.id
{$where}
";

$countStmt = $conn->prepare($countSql);
$countStmt->execute($params);

$total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

/*
|--------------------------------------------------------------------------
| Package List
|--------------------------------------------------------------------------
*/

$sql = "
SELECT
    p.id,
    p.package_name,
    p.short_description,
    p.package_cost,
    p.total_days,
    p.total_nights,
    p.thumbnail,
    p.start_date,
    p.end_date,

    d.destination_name,
    d.country,
    d.state

FROM packages p

LEFT JOIN destinations d
ON p.destination_id = d.id

{$where}

ORDER BY p.start_date ASC

LIMIT {$offset},{$limit}
";

$stmt = $conn->prepare($sql);
$stmt->execute($params);

$packages = $stmt->fetchAll(PDO::FETCH_ASSOC);


foreach ($packages as &$package) {

    $package['thumbnail'] =
        UPLOAD_URL . 'tour/' . $package['thumbnail'];

}

/*
|--------------------------------------------------------------------------
| Nearest Package Logic
|--------------------------------------------------------------------------
*/

if (
    empty($packages) &&
    !empty($_GET['destination']) &&
    !empty($_GET['start_date'])
) {

    $nearestSql = "
    SELECT
        p.id,
        p.package_name,
        p.short_description,
        p.package_cost,
        p.total_days,
        p.total_nights,
        p.thumbnail,
        p.start_date,
        p.end_date,

        d.destination_name,
        d.country,
        d.state

    FROM packages p

    LEFT JOIN destinations d
    ON p.destination_id = d.id

    WHERE p.status = 1
    AND d.destination_name LIKE ?

    ORDER BY ABS(DATEDIFF(p.start_date, ?))
    LIMIT 5
    ";

    $nearestStmt = $conn->prepare($nearestSql);

    $nearestStmt->execute([
        "%" . $_GET['destination'] . "%",
        $_GET['start_date']
    ]);

    $nearestPackages =
        $nearestStmt->fetchAll(PDO::FETCH_ASSOC);
    
   $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($packages as &$package) {

    $package['thumbnail'] =
        UPLOAD_URL . 'tour/' . $package['thumbnail'];

}
    sendResponse(
        true,
        "No package found for selected date. Showing nearest available packages.",
        [
            "current_page" => 1,
            "limit" => 5,
            "total_records" => count($nearestPackages),
            "packages" => $nearestPackages
        ]
    );
}

/*
|--------------------------------------------------------------------------
| Final Response
|--------------------------------------------------------------------------
*/

sendResponse(
    true,
    "Package List",
    [
        "current_page" => $page,
        "limit" => $limit,
        "total_records" => $total,
        "packages" => $packages
    ]
);