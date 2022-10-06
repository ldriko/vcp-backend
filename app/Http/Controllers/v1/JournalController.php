<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class JournalController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Collection
     */
    public function index(Request $request): Collection
    {
        return $request->user()->journals;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return Builder|Collection
     */
    public function search(Request $request): Collection|array
    {
        $request->validate([
            'q' => ['required'],
            'limit' => ['sometimes', 'numeric'],
            'page' => ['sometimes', 'numeric'],
        ]);

        $searchQuery = Str::of($request->q)->explode(' ');
        $query = Journal::query()->where('is_published', true);

        $query->where(function ($query) use ($searchQuery) {
            foreach ($searchQuery as $q) {
                $query->orWhere('title', 'like', "%{$q}%");
                $query->orWhere('short_desc', 'like', "%{$q}%");
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
     * @param Request $request
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'short_desc' => 'required',
            'file' => 'required|file|mimes:pdf'
        ]);

        $randomCodes = [Str::random(4), Str::random(4), Str::random(4)];
        $code = Str::lower(Arr::join($randomCodes, '-'));
        $slug = Str::slug($request->title . ' ' . Arr::last($randomCodes));
        $path = Storage::disk('journals')->put('', $request->file('file'));

        Journal::query()->create([
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
     * @return Journal
     */
    public function show(Journal $journal): Journal
    {
        return $journal;
    }

    /**
     * @param Journal $journal
     *
     * @return StreamedResponse
     */
    public function showPdf(Journal $journal, Request $request): StreamedResponse
    {
        $request->validate([
            'is_download' => 'sometimes|boolean'
        ]);

        return Storage::disk('journals')->download(
            $journal->path,
            $journal->title,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => $request->boolean('is_download') ? 'attachment;' : 'inline;'
            ]
        );
    }

    /**
     * @param Request $request
     * @param Journal $journal
     */
    public function publish(Request $request, Journal $journal)
    {
        $request->validate(['publish' => 'required|boolean']);
        $journal->update(['is_published' => $request->publish]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Journal $journal
     */
    public function update(Request $request, Journal $journal)
    {
        $request->validate([
            'title' => 'required|max:100',
            'short_desc' => 'required|max:250',
            'file' => 'sometimes|required|file|mimes:pdf'
        ]);

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
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Journal $journal
     */
    public function destroy(Journal $journal)
    {
        $journal->delete();
    }
}
