<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Journal;
use App\Models\JournalCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
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
     * @return Collection
     */
    public function search(Request $request): Collection
    {
        $request->validate([
            'q' => 'required',
            'limit' => 'sometimes|numeric',
            'page' => 'sometimes|numeric',
        ]);

        $searchQuery = Str::of($request->q)->explode(' ');
        $query = Journal::query()
            ->with('categories')
            ->where('is_published', true);

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
     *
     * @return Model|Builder
     */
    public function store(Request $request): Model|Builder
    {
        $request->validate([
            'title' => 'required',
            'short_desc' => 'required',
            'categories' => 'required|array',
            'file' => 'required|file|mimes:pdf'
        ]);

        $randomCodes = [Str::random(4), Str::random(4), Str::random(4)];
        $code = Str::lower(Arr::join($randomCodes, '-'));
        $slug = Str::slug($request->title . ' ' . Arr::last($randomCodes));
        $path = Storage::disk('journals')->put('', $request->file('file'));

        DB::beginTransaction();

        $journal = Journal::query()->create([
            'code' => $code,
            'slug' => $slug,
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'short_desc' => $request->short_desc,
            'path' => $path
        ]);

        foreach ($request->categories as $category) {
            if (!Category::query()->find($category)) {
                abort(401, 'Category not found');
            }

            JournalCategory::query()->create([
                'journal_code' => $journal->code,
                'category_id' => $category
            ]);
        }

        DB::commit();

        return $journal;
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
        return $journal->load('user');
    }

    /**
     * @param Journal $journal
     * @param Request $request
     *
     * @return StreamedResponse
     */
    public function showPdf(Journal $journal, Request $request): StreamedResponse
    {
        $request->validate(['is_download' => 'sometimes|boolean']);

        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => $request->boolean(
                'is_download'
            ) ? 'attachment; filename="' . $journal->title . '.pdf"'
                : 'inline; filename="' . $journal->title . '.pdf"',
        ];

        return Storage::disk('journals')->download(
            $journal->path,
            $journal->title,
            $headers
        );
    }

    /**
     * @param Request $request
     * @param Journal $journal
     */
    public function publish(Request $request, Journal $journal)
    {
        $request->validate(['publish' => 'required | boolean']);
        $journal->update(['is_published' => $request->publish]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Journal $journal
     *
     * @return Journal
     */
    public function update(Request $request, Journal $journal): Journal
    {
        $request->validate([
            'title' => 'required | max:100',
            'short_desc' => 'required | max:250',
            'categories' => 'required | array',
            'file' => 'sometimes | required | file | mimes:pdf',
        ]);

        $lastCode = Str::of($journal->code)->explode(' - ')->last();
        $slug = Str::slug($request->title . ' ' . $lastCode);

        DB::beginTransaction();

        $journal->update([
            'slug' => $slug,
            'title' => $request->title,
            'short_desc' => $request->short_desc,
        ]);

        $journal->categoriesTunnel()->delete();

        foreach ($request->categories as $category) {
            if (!Category::query()->find($category)) {
                abort(401, 'Category not found');
            }

            JournalCategory::query()->create([
                'journal_code' => $journal->code,
                'category_id' => $category
            ]);
        }

        DB::commit();

        if ($request->hasFile('file')) {
            Storage::disk('journals')->delete($journal->path);

            $path = Storage::disk('journals')->put('', $request->file('file'));

            $journal->update(['path' => $path]);
        }

        return $journal;
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
