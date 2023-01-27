#!/usr/bin/php -q
<?php

// Get the AGI instance
require_once '/var/lib/asterisk/agi-bin/vendor/autoload.php';
require_once "/var/lib/asterisk/agi-bin/phpagi-2.20/phpagi.php";
$agi = new AGI();

// Get the question from the dialplan variable "question1"
//$question = $agi->get_variable("question")['data'];
$question = "Hola, cual es el costo del ingles intensivo";

// Set the Dialogflow project ID 
$project_id = "ags-test-onjk";

// Set the path to the JSON service account credentials
putenv('GOOGLE_APPLICATION_CREDENTIALS=/var/lib/asterisk/agi-bin/ags.json');

// Get the access token from Google
$client = new Google_Client();
$client->setApplicationName("My Application");
$client->useApplicationDefaultCredentials();
$client->addScope("https://www.googleapis.com/auth/dialogflow");
$access_token = $client->fetchAccessTokenWithAssertion()["access_token"];

// Set

// Set the Dialogflow API endpoint
$url = "https://dialogflow.googleapis.com/v2/projects/{$project_id}/agent/sessions/1234:detectIntent";

// Set the cURL options
$headers = array(
    'Content-Type: application/json',
    'Authorization: Bearer ' . $access_token
);
$data = array(
    "query_input" => array(
        "text" => array(
            "text" => $question,
            "language_code" => "es"
        )
    )
);
$options = array(
    CURLOPT_URL => $url,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_POSTFIELDS => json_encode($data),
    CURLOPT_RETURNTRANSFER => true
);

// Send the cURL request
$ch = curl_init();
curl_setopt_array($ch, $options);
$response = curl_exec($ch);
curl_close($ch);

// Decode the JSON response
$response = json_decode($response);

// Print the response from Dialogflow
$agi->set_variable("responsedialogflow", $response->queryResult->fulfillmentText);
$agi->verbose("Response from Dialogflow: ".$response->queryResult->fulfillmentText);

// Sleep for a few seconds to allow the user to hear the response
sleep(3);
