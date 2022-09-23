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
use Illuminate\Support\Str;

class JournalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Builder[]|Collection
     */
    public function index(): Collection|array
    {
        return Journal::query()
            ->limit(15)
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreJournalRequest $request
     * @return Builder|Model
     */
    public function store(StoreJournalRequest $request): Model|Builder
    {
        $codes = [Str::random(4), Str::random(4), Str::random(4)];
        $journalCode = Str::lower(Arr::join($codes, '-'));
        $slug = Str::slug($request->title . ' ' . Arr::last($codes));

        return Journal::query()->create(
            [
                'code' => $journalCode,
                'slug' => $slug,
                'user_id' => $request->user()->id,
                'title' => $request->title,
                'short_desc' => $request->short_desc
            ]
        );
    }

    /**
     * Display the specified resource.
     *
     * @param Journal $journal
     * @return Journal
     */
    public function show(Journal $journal): Journal
    {
        return $journal;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateJournalRequest $request
     * @param Journal $journal
     * @return Response
     */
    public function update(UpdateJournalRequest $request, Journal $journal): Response
    {
        $lastCode = Str::of($journal->code)->explode('-')->last();
        $slug = Str::slug($request->title . ' ' . $lastCode);

        $journal->update(
            [
                'slug' => $slug,
                'title' => $request->title,
                'short_desc' => $request->short_desc,
            ]
        );

        return response()->noContent();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Journal $journal
     * @return bool|Response
     */
    public function destroy(Request $request, Journal $journal): Response|bool
    {
        if ($journal->user()->id === $request->user()->id) {
            return response()->isNotFound();
        }

        $journal->delete();

        return response()->noContent();
    }
}
