<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class JournalIsAuthor
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
        $journal = $request->route()->parameter('journal');

        if ($journal->user_id !== $request->user()->id) {
            abort(404);
        }

        return $next($request);
    }
}
