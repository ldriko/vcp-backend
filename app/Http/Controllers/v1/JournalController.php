<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJournalRequest;
use App\Http\Requests\UpdateJournalRequest;
use App\Models\Journal;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class JournalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Builder[]|Collection
     */
    public function index(Request $request): Collection|array
    {
        $request->validate([
            'q' => ['required'],
            'limit' => ['sometimes', 'numeric'],
            'page' => ['sometimes', 'numeric'],
        ]);

        $searchQuery = Str::of($request->q)->explode(' ');

        $query = Journal::query()
            ->where('is_published', true);

        $query->where(function ($query) use ($searchQuery) {
            foreach ($searchQuery as $key => $q) {
                if ($key === 0) {
                    $query->where('title', 'like', "%{$q}%");
                    $query->where('short_desc', 'like', "%{$q}%");
                } else {
                    $query->orWhere('title', 'like', "%{$q}%");
                    $query->orWhere('short_desc', 'like', "%{$q}%");
                }
            }
        });

        $query->limit($request->limit ?? 15);

        if ($request->has('page')) {
            $query->offset($request->page * $request->limit);
        }

        return $query->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreJournalRequest $request
     *
     * @return Builder|Model
     */
    public function store(StoreJournalRequest $request): Model|Builder
    {
        $randomCodes = [Str::random(4), Str::random(4), Str::random(4)];
        $code = Str::lower(Arr::join($randomCodes, '-'));
        $slug = Str::slug($request->title . ' ' . Arr::last($randomCodes));
        $path = Storage::disk('journals')->put('', $request->file('file'));

        return Journal::query()->create([
            'code' => $code,
            'slug' => $slug,
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'short_desc' => $request->short_desc,
            'path' => $path
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Journal $journal
     *
     * @return Journal|bool
     */
    public function show(Request $request, Journal $journal)
    {
        if ($journal->user->id !== $request->user()->id && !$journal->is_published) {
            return abort(404);
        }

        return $journal;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateJournalRequest $request
     * @param Journal $journal
     *
     * @return bool|Response
     */
    public function update(UpdateJournalRequest $request, Journal $journal)
    {
        if ($journal->user->id !== $request->user()->id) {
            return abort(404);
        }

        $lastCode = Str::of($journal->code)->explode('-')->last();
        $slug = Str::slug($request->title . ' ' . $lastCode);

        $journal->update([
            'slug' => $slug,
            'title' => $request->title,
            'short_desc' => $request->short_desc,
        ]);

        if ($request->hasFile('file')) {
            Storage::disk('journals')->delete($journal->path);
            $path = Storage::disk('journals')->put('', $request->file('file'));
            $journal->update(['path' => $path]);
        }

        return response()->noContent();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Journal $journal
     *
     * @return Response
     */
    public function destroy(Request $request, Journal $journal): Response|bool
    {
        if ($journal->user->id !== $request->user()->id) {
            return abort(404);
        }

        $journal->delete();

        return response()->noContent();
    }
}
