<?php

require_once '../../middleware/auth.php';

$data = json_decode(
    file_get_contents("php://input"),
    true
);

$prompt = "...";

$aiResponse = callOpenAI($prompt);

sendResponse(
    true,
    "Trip Generated",
    $aiResponse
);