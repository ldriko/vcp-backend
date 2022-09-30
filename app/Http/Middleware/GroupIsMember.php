<?php

namespace App\Http\Middleware;

use App\Models\Group;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GroupIsMember
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return RedirectResponse|Response|mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $group = $request->route()->parameter('group');

        // TODO: Change collection to query validation
        if (!$group->members->contains('user_id', $request->user()->id)) {
            abort(404);
        }

        return $next($request);
    }
}
