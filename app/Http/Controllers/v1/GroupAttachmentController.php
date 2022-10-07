<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\GroupAttachment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GroupAttachmentController extends Controller
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
        return $group->attachments()
            ->with('journal')
            ->latest()
            ->offset($request->offset)
            ->limit(15)
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
        $request->validate([
            'journal_code' => 'required|exists:journals,code',
        ]);

        return GroupAttachment::query()->create([
            'group_id' => $group->id,
            'user_id' => $request->user()->id,
            'journal_code' => $request->journal_code,
            'text' => $request->text
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param GroupAttachment $groupChatAttachment
     *
     * @return GroupAttachment
     */
    public function show(GroupAttachment $groupChatAttachment): GroupAttachment
    {
        return $groupChatAttachment;
    }
}
