<?php

function sc_timetap_get_token(){
    //if ($cached) return $cached;

    $apiKey = '361484';
    $secret = 'b5053dc7719444fdbb2aa0fd6e44bd16'; // REPLACE with your real secret key


    $timestamp = round(microtime(true));
    $signature = md5($apiKey . $secret);
    $token_url = "https://api.timetap.com/test/sessionToken?apiKey=$apiKey&timestamp=$timestamp&signature=$signature";

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $token_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    $response_std = json_decode($response, true);

    $token = isset($response_std['sessionToken']) ? $response_std['sessionToken'] : '';

    if(empty($token)){
        return [];
    }

    return $token;
}

function sc_timetap_get_slots($token, $locationId, $serviceId){
    $startDate = date('Y-m-d');
    $endDate = date('Y-m-d', strtotime('+7 days'));
    $slots_url = "https://api.timetap.com/test/availability/location/$locationId/service/$serviceId?startDate=$startDate&endDate=$endDate";

    $slots_response = wp_remote_get($slots_url, [
        'headers' => ['Authorization' => "Bearer $token"]
    ]);

    $slots_data = json_decode(wp_remote_retrieve_body($slots_response), true);

    if (!is_array($slots_data)) return [];

    return $slots_data;

}

function sc_timetap_get_staff_list($token) {
    $url = 'https://api.timetap.com/test/staff';

    $response = wp_remote_get($url, [
        'headers' => [
            'Authorization' => "Bearer $token",
            'Content-Type'  => 'application/json',
        ]
    ]);

    if (is_wp_error($response)) {
        return [
            'success' => false,
            'error' => $response->get_error_message(),
        ];
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);

    if (!empty($body['staffList'])) {
        return [
            'success' => true,
            'staff' => $body['staffList'],
        ];
    }

    return [
        'success' => false,
        'error' => 'No staff found.',
    ];
}

function sc_timetap_client_id($token, $email){
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.timetap.com/test/clients/externalUserName/'. $email . '/',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    $response_std = json_decode($response, true);

    if(!empty($response_std) && isset($response_std[0]['clientId'])){
        return $response_std[0]['clientId'];
    }

    return false;


}

function sc_timetap_create_timetap_client($client, $token) {
    $curl = curl_init();

    // Build the client data array properly
    $client_data = [
        "emailAddress"      => $client['email'],
        "firstName"         => $client['first_name'],
        "lastName"          => $client['last_name'],
        "fullName"          => $client['first_name'] . ' ' . $client['last_name'],
        "externalUserName"  => $client['email'],
    ];

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.timetap.com/test/clients',  // Adjust if not correct
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($client_data),  // ✅ Safely encode as JSON
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
        ),
    ));

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        echo 'Curl error: ' . curl_error($curl);
    }

    $response_std = json_decode($response, true);


    if(!empty($response_std) && isset($response_std['clientId'])){
        return $response_std['clientId'];
    }

    return false;

}

function sc_temetap_create_appointment($appointment, $token) {
    $api_url = 'https://api.timetap.com/test/appointments';
    $api_token = 'Bearer ' . $token; // Replace with your actual token

    $response = wp_remote_post($api_url, [
        'method'    => 'POST',
        'headers'   => [
            'Content-Type'  => 'application/json',
            'Authorization' => $api_token,
        ],
        'body'      => json_encode($appointment),
        'timeout'   => 20,
    ]);

    $body = json_decode(wp_remote_retrieve_body($response), true);


    return $body;


}

function sc_convert_military_to_std($time) {
    // Ensure it's always 4 digits (e.g., 830 → 0830)
    $time = str_pad($time, 4, '0', STR_PAD_LEFT);

    // Extract hours and minutes
    $hours = substr($time, 0, 2);
    $minutes = substr($time, 2, 2);

    // Create a DateTime object and format it
    $dateTime = DateTime::createFromFormat('Hi', $hours . $minutes);
    return $dateTime ? $dateTime->format('g:i A') : null;
}