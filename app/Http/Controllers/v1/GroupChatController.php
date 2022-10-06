<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\GroupChat;
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
        $request->validate(['offset' => 'sometimes|numeric']);

        return $group->chats()
            ->latest()
            ->limit(15)
            ->offset($request->offset)
            ->get();
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
        $request->validate(['text' => 'required']);

        return GroupChat::query()->create([
            'group_id' => $group->id,
            'user_id' => $request->user()->id,
            'text' => $request->text
        ]);
    }
}
