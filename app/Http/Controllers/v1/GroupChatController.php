<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\GroupChat;
use App\Models\Journal;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class GroupChatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Group $group
     * @param Request $request
     *
     * @return Collection
     */
    public function index(Group $group, Request $request): Collection
    {
        $request->validate(['offset_id' => 'sometimes|numeric']);

        $query = $group->chats()->with('user', 'journal.user');

        if ($request->offset_id) {
            $query->where('id', '>', $request->offset_id);
        }

        return $query->limit(15)->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Group $group
     * @param Request $request
     *
     * @return Builder|Model
     */
    public function store(Group $group, Request $request): Model|Builder
    {
        $request->validate([
            'text' => 'required_without:journal_code',
            'journal_code' => 'sometimes'
        ]);

        $journalExists = false;

        if ($request->journal_code) {
            $journalExists = Journal::query()
                ->where('code', $request->journal_code)
                ->where('is_published', true)
                ->exists();
        }

        $chat = GroupChat::query()->create([
            'group_id' => $group->id,
            'user_id' => $request->user()->id,
            'journal_code' => $journalExists ? $request->journal_code : null,
            'text' => $request->text
        ]);

        $group->touch();

        return $chat->load('journal');
    }
}
