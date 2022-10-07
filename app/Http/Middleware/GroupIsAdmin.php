<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GroupIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     *
     * @return Response|RedirectResponse|mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $group = $request->route()->parameter('group');

        if ($group->user_id !== $request->user()->id) {
            abort(404);
        }

        return $next($request);
    }
}
