<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class GroupMemberController extends Controller
{
    /**
     * @param Group $group
     * @param User $user
     *
     * @return Model|Builder
     */
    public function show(Group $group, User $user): Model|Builder
    {
        return GroupMember::query()
            ->where('group_id', $group->id)
            ->where('user_id', $user->id)
            ->firstOrFail();
    }

    /**
     * @param Request $request
     * @param Group $group
     *
     * @return void
     */
    public function store(Request $request, Group $group): void
    {
        GroupMember::query()->create([
            'group_id' => $group->id,
            'user_id' => $request->user()->id
        ]);
    }

    /**
     * @param Group $group
     * @param User $user
     *
     * @return void
     */
    public function accept(Group $group, User $user): void
    {
        $groupMember = GroupMember::query()
            ->where('group_id', $group->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $groupMember->update(['is_accepted', 1]);
    }

    /**
     * @param Group $group
     * @param User $user
     *
     * @return void
     */
    public function destroy(Group $group, User $user): void
    {
        $groupMember = GroupMember::query()
            ->where('group_id', $group->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $groupMember->delete();
    }
}
