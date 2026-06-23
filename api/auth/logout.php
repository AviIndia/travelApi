<?php

header("Content-Type: application/json");

require_once __DIR__ . '/../../helpers/response.php';

// Future e token blacklist korle ekhane logic add hobe

sendResponse(
    true,
    "Logout Successful"
);