<?php

return [
    /*
    | The value sent by the ESP32 in the X-RFID-Token request header.
    | Generate a long random value before exposing this endpoint on a network.
    */
    'token' => env('RFID_DEVICE_TOKEN'),

    /*
    | The user recorded as the source of an RFID attendance record. Leave this
    | empty to use the first active administrator or principal.
    */
    'recorded_by_user_id' => env('RFID_RECORDED_BY_USER_ID'),

    /*
    | Server-Sent Events for the admin pages. Each open tab holds one PHP
    | worker, so disable this on servers with very few workers.
    */
    'live_stream_enabled' => env('RFID_LIVE_STREAM', true),

    /*
    | The reader is shown as offline when it has not polled the status
    | endpoint for this many seconds.
    */
    'device_offline_after' => (int) env('RFID_DEVICE_OFFLINE_AFTER', 30),
];
