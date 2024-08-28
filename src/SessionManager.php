<?php

namespace Pemto\SessionManager;

use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SessionManager
{
    /**
     * Get the current sessions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Support\Collection
     */
    public static function getSessions(Request $request, $guard)
    {
        if (config('session.driver') !== 'database') {
            return collect();
        }

        return collect(
            DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
                    ->where('guard', $guard)
                    ->where('user_id', Auth::guard($guard)->user()->id())
                    ->orderBy('last_activity', 'desc')
                    ->get()
        )->map(function ($session) use ($request) {
            $agent = $this->createAgent($session);

            return (object) [
                'agent' => [
                    'is_desktop' => $agent->isDesktop(),
                    'platform' => $agent->platform(),
                    'browser' => $agent->browser(),
                ],
                'guard' => $session->guard,
                'ip_address' => $session->ip_address,
                'is_current_device' => $session->id === $request->session()->getId(),
                'last_active' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
            ];
        });

    }

    /**
     * Destroy all sessions from user
     * @param $user_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroySession($user_id, $guard)
    {
        DB::beginTransaction();
        try {
            DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
                ->where('guard', $guard)
                ->where('user_id', $user_id)
                ->delete();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }

    }

    public static function testFunction()
    {
        dd('test');
    }
}
