<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RfidService;
use Illuminate\Http\Request;

class RfidAttendanceController extends Controller
{
    public function store(Request $request, RfidService $rfid)
    {
        $token = (string) config('rfid.token');
        abort_if($token === '' || ! hash_equals($token, (string) $request->header('X-RFID-Token')), 401, 'Token perangkat tidak valid.');
        $data = $request->validate(['uid' => ['required', 'string', 'max:29', 'regex:/\A[0-9a-fA-F]{2}(?::[0-9a-fA-F]{2})+\z/']]);
        $uid = strtoupper($data['uid']);
        abort_unless(in_array(strlen($uid), [11, 20, 29], true), 422, 'Panjang UID tidak valid.');
        $result = $rfid->scan($uid);
        $status = $result['status'];
        unset($result['status']);
        return response()->json($result, $status);
    }
}
