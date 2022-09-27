<?php

namespace App\Http\Middleware;

use App\Models\Journal;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class JournalIsPublished
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

        if ($journal->user->id !== $request->user()?->id && !$journal->is_published) {
            abort(404);
        }

        return $next($request);
    }
}
