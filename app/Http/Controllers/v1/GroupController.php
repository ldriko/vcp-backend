<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\GroupMember;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function index(Request $request): mixed
    {
        return $request->user()->groups;
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
            'title' => ['required', 'max:50'],
            'description' => ['sometimes', 'max:250'],
        ]);

        $group = Group::query()->create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'description' => $request->description,
        ]);

        GroupMember::query()->create([
            'group_id' => $group->id,
            'user_id' => $request->user()->id,
        ]);

        return $group;
    }

    /**
     * Display the specified resource.
     *
     * @param Group $group
     *
     * @return Group
     */
    public function show(Group $group): Group
    {
        return $group;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Group $group
     *
     * @return Group
     */
    public function update(Request $request, Group $group): Group
    {
        $request->validate([
            'title' => ['required', 'max:50'],
            'description' => ['sometimes', 'max:250'],
        ]);

        $group->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return $group;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Group $group
     */
    public function destroy(Group $group)
    {
        DB::beginTransaction();

        $group->attachments()->delete();
        $group->chats()->delete();
        $group->members()->delete();
        $group->delete();

        DB::commit();
    }

    /**
     * @param Group $group
     *
     * @return string
     */
    public function invite(Group $group): string
    {
        return URL::signedRoute('group.join', ['group' => $group->id], now()->addMinutes(30));
    }

    /**
     * @param Group $group
     * @param Request $request
     */
    public function join(Group $group, Request $request)
    {
        if ($group->members()->where('user_id', $request->user()->id)->exists()) {
            return;
        }

        GroupMember::query()->create([
            'group_id' => $group->id,
            'user_id' => $request->user()->id
        ]);
    }
}
