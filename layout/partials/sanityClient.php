<?php
// sanityClient.php

$SANITY_PROJECT_ID = 'r8m6dk3s';
$SANITY_DATASET = 'production';
$SANITY_TOKEN = 'skNVfFxeZDJn4kl9lV7pF2mfaErt8hkaasyh6CfEk3A8kHJDJ9HzQfYU2f0xLRfip3fRHRJx28mgIcyqjh8mjVT30p9x8s2d3cqA8CwPmkxXpQkclThBVWZZbIZgZtdA8h51Egpoat656SfEXsxBHUY8WttOPwzAjQaSPSnQMdUC4IuwFSFM';
$SANITY_API_VERSION = '2025-07-14';

function fetchFromSanity($query)
{
    global $SANITY_PROJECT_ID, $SANITY_DATASET, $SANITY_TOKEN, $SANITY_API_VERSION;

    $url = "https://{$SANITY_PROJECT_ID}.api.sanity.io/v{$SANITY_API_VERSION}/data/query/{$SANITY_DATASET}?query=" . urlencode($query);

    $headers = [
        "Accept: application/json"
    ];

    if (!empty($SANITY_TOKEN)) {
        $headers[] = "Authorization: Bearer {$SANITY_TOKEN}";
    }

    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers
    ]);

    $response = curl_exec($ch);

    // Log ke file
    $logFile = __DIR__ . '/sanity_log.txt';
    $logData = "=== SANITY API REQUEST ===\n";
    $logData .= "URL: $url\n";
    $logData .= "Headers: " . print_r($headers, true) . "\n";

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        $logData .= "cURL ERROR: $error\n";
        file_put_contents($logFile, $logData, FILE_APPEND);
        curl_close($ch);
        return ['error' => $error];
    }

    $logData .= "Raw Response:\n$response\n";

    $decoded = json_decode($response, true);
    $logData .= "Decoded Response:\n" . print_r($decoded, true) . "\n\n";

    file_put_contents($logFile, $logData, FILE_APPEND);

    curl_close($ch);

    return $decoded;
}
