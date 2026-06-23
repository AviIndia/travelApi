<?php

require_once __DIR__.'/../config/constant.php';

function callGemini($prompt)
{
    $url =
    "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key="
    . GEMINI_API_KEY;

    $payload = [
        "contents" => [
            [
                "parts" => [
                    [
                        "text" => $prompt
                    ]
                ]
            ]
        ]
    ];

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_POST, true);

    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        ['Content-Type: application/json']
    );

    curl_setopt(
        $ch,
        CURLOPT_POSTFIELDS,
        json_encode($payload)
    );

    $response = curl_exec($ch);

    curl_close($ch);

    return json_decode($response, true);
}