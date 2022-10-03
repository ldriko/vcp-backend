<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class GroupMemberController extends Controller
{
    /**
     * @param Group $group
     *
     * @return Collection
     */
    public function index(Group $group): Collection
    {
        return $group->members()
            ->with('user')
            ->get();
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
