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

$package_id = $data['package_id'] ?? 0;
$rating = $data['rating'] ?? 0;
$review = $data['review'] ?? '';

if(!$package_id){
    sendResponse(false,"Package ID required");
}

if($rating < 1 || $rating > 5){
    sendResponse(false,"Rating must be between 1 and 5");
}

$sql = $conn->prepare("
INSERT INTO reviews(
    package_id,
    user_id,
    rating,
    review,
    review_image
)
VALUES(
    ?,?,?,?,?
)
");

$result = $sql->execute([
    $package_id,
    $user->id,
    $rating,
    $review,
    $data['review_image'] ?? ''
]);

if(!$result){
    sendResponse(false,"Review submission failed");
}

sendResponse(
    true,
    "Review submitted successfully. Waiting for approval."
);