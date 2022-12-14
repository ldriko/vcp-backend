<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\GroupChat;
use App\Models\GroupMember;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return Collection
     */
    public function index(Request $request): Collection
    {
        $request->validate(['limit' => 'sometimes|integer']);

        $query = Group::query()
            ->with('latestChat.user')
            ->whereHas('members', function (Builder $query) use ($request) {
                $query->where('user_id', $request->user()->id);
            });

        if ($request->has('q')) {
            $query->where('title', 'like', "%$request->q%");
        }

        if ($request->has('limit')) {
            $query->limit($request->limit);
        }

        return $query->latest()->get();
    }

    /**
     * Generates random string that does not exist
     *
     * @return string
     */
    public function generateCode(): string
    {
        $found = false;
        $code = null;

        while (!$found || $code === null) {
            $code = Str::random(9);
            $exists = Group::query()->where('code', "BINARY '$code'")->exists();
            $found = !$exists;
        }

        return $code;
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
            'title' => 'required|max:50',
            'description' => 'sometimes|max:250',
            'code' => 'required|min:9|max:9',
            'picture' => 'sometimes|file|image'
        ]);

        $picturePath = null;

        if ($request->hasFile('picture')) {
            $picturePath = Storage::disk('groups')->put('', $request->file('picture'));
        }

        $group = Group::query()->create([
            'code' => $request->code,
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'description' => $request->description,
            'picture_path' => $picturePath
        ]);

        GroupMember::query()->create([
            'group_id' => $group->id,
            'user_id' => $request->user()->id,
        ]);

        GroupChat::query()->create([
            'group_id' => $group->id,
            'user_id' => $request->user()->id,
            'text' => 'Grup telah dibuat'
        ]);

        $group->touch();

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
     * Generates url for the specified group
     *
     * @param Group $group
     * @param Request $request
     *
     * @return Response|StreamedResponse
     */
    public function showPicture(Group $group, Request $request): Response|StreamedResponse
    {
        if (!$group->picture_path) return response()->noContent();

        return Storage::disk('groups')->download(
            $group->picture_path,
            null,
            [
                'Content-Disposition' => $request->boolean('is_download') ? 'attachment;' : 'inline;'
            ]
        );
    }

    /**
     * Show the group members count
     *
     * @param Group $group
     *
     * @return int
     */
    public function showMembersCount(Group $group): int
    {
        return count($group->members);
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
            'title' => 'required|max:50',
            'description' => 'sometimes|max:250',
            'picture' => 'sometimes|file|image'
        ]);

        $group->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        if ($request->hasFile('picture')) {
            $picturePath = Storage::disk('groups')->put('', $request->file('picture'));
            $group->update([
                'picture_path' => $picturePath
            ]);
        }

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
     * @param Request $request
     */
    public function join(Request $request)
    {
        $request->validate(['code' => 'required']);

        $group = Group::query()->where('code', $request->code)->firstOrFail();

        if (!GroupMember::query()->where('group_id', $group->id)->where('user_id', $request->user()->id)->exists()) {
            GroupMember::query()->create([
                'group_id' => $group->id,
                'user_id' => $request->user()->id
            ]);
        }
    }

    /**
     * @param Group $group
     * @param Request $request
     */
    public function exit(Group $group, Request $request)
    {
        GroupMember::query()
            ->where('group_id', $group->id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail()
            ->delete();
    }
}
