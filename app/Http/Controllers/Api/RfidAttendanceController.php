<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RfidService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RfidAttendanceController extends Controller
{
    public function store(Request $request, RfidService $rfid)
    {
        $this->authorizeDevice($request);
        $data = $request->validate(['uid' => ['required', 'string', 'max:29', 'regex:/\A[0-9a-fA-F]{2}(?::[0-9a-fA-F]{2})+\z/']]);
        $uid = strtoupper($data['uid']);
        abort_unless(in_array(strlen($uid), [11, 20, 29], true), 422, 'Panjang UID tidak valid.');
        $result = $rfid->scan($uid);
        $status = $result['status'];
        unset($result['status']);
        return response()->json($result, $status);
    }

    public function status(Request $request, RfidService $rfid)
    {
        $this->authorizeDevice($request);
        $state = DB::table('rfid_reader_state')->where('id', 1)->first();
        $active = $state && $rfid->active($state);

        return response()->json([
            'mode' => $active ? 'registration' : 'attendance',
            'uid_captured' => $active && (bool) $state->uid,
            'remaining_seconds' => $active ? max(0, (int) ceil(now()->diffInSeconds(Carbon::parse($state->expires_at), false))) : 0,
        ])->header('Cache-Control', 'no-store');
    }

    private function authorizeDevice(Request $request): void
    {
        $token = (string) config('rfid.token');
        abort_if($token === '' || ! hash_equals($token, (string) $request->header('X-RFID-Token')), 401, 'Token perangkat tidak valid.');
        DB::table('rfid_reader_state')->where('id', 1)->update(['device_seen_at' => now()]);
    }
}
