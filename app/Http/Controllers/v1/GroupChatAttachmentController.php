<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\GroupChatAttachment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GroupChatAttachmentController extends Controller
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

        return GroupChatAttachment::query()->create([
            'group_id' => $group->id,
            'user_id' => $request->user()->id,
            'journal_code' => $request->journal_code,
            'text' => $request->text
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param GroupChatAttachment $groupChatAttachment
     *
     * @return GroupChatAttachment
     */
    public function show(GroupChatAttachment $groupChatAttachment): GroupChatAttachment
    {
        return $groupChatAttachment;
    }
}
