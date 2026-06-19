<?php

header("Content-Type: application/json");

require_once '../../config/database.php';
require_once '../../helpers/response.php';

$db = new Database();
$conn = $db->connect();

$package_id = $_GET['package_id'] ?? 0;

if(!$package_id){
    sendResponse(false,"Package ID required");
}

$sql = $conn->prepare("
SELECT

r.id,
r.rating,
r.review,
r.review_image,
r.created_at,

u.name

FROM reviews r

LEFT JOIN users u
ON r.user_id=u.id

WHERE
r.package_id=?
AND r.status=1

ORDER BY r.id DESC
");

$sql->execute([$package_id]);

$reviews = $sql->fetchAll(PDO::FETCH_ASSOC);

$avgSql = $conn->prepare("
SELECT
COUNT(*) total_review,
ROUND(AVG(rating),1) avg_rating

FROM reviews

WHERE
package_id=?
AND status=1
");

$avgSql->execute([$package_id]);

$summary = $avgSql->fetch(PDO::FETCH_ASSOC);

sendResponse(
    true,
    "Review List",
    [
        "summary"=>$summary,
        "reviews"=>$reviews
    ]
);