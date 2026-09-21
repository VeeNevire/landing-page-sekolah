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
];
