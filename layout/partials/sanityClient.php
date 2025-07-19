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

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return ['error' => $error];
    }

    $decoded = json_decode($response, true);

    curl_close($ch);

    return $decoded;
}
