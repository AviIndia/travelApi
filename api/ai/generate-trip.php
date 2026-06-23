<?php

header("Content-Type: application/json");

require_once '../../middleware/auth.php';
require_once '../../helpers/response.php';
require_once '../../helpers/gemini.php';

$data = json_decode(
    file_get_contents("php://input"),
    true
);

$from_location = $data['from_location'] ?? '';
$destination   = $data['destination'] ?? '';
$start_date    = $data['start_date'] ?? '';
$end_date      = $data['end_date'] ?? '';
$travelers     = $data['travelers'] ?? '';
$budget        = $data['budget'] ?? '';
$trip_type     = $data['trip_type'] ?? '';

$prompt = "

Create a detailed travel itinerary.

From Location: {$from_location}
Destination: {$destination}
Start Date: {$start_date}
End Date: {$end_date}
Travelers: {$travelers}
Budget: {$budget}
Trip Type: {$trip_type}

Return JSON only.

";

$result = callGemini($prompt);

sendResponse(
    true,
    "Trip Generated",
    $result
);