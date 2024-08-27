<?php

namespace Pemto\SessionManager;

use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;

class SessionManager
{
    /**
     * Get the current sessions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Support\Collection
     */
    public function getSessions(Request $request, $guard)
    {
        if (config('session.driver') !== 'database') {
            return collect();
        }
2023_11_29_191739_
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
}
