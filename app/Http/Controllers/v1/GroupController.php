<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request): Response
    {
        return $request->user()->groups();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     */
    public function store(Request $request)
    {
        $request->validate(['title' => ['required', 'max:50']]);

        Group::query()->create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'description' => $request->description,
        ]);
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
     */
    public function update(Request $request, Group $group)
    {
        $request->validate(['title' => ['required', 'max:50']]);

        $group->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Group $group
     */
    public function destroy(Group $group)
    {
        DB::beginTransaction();

        $group->attachments->delete();
        $group->chats->delete();
        $group->members->delete();
        $group->delete();

        DB::commit();
    }
}
