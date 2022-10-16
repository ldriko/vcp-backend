<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\GroupChat;
use App\Models\GroupMember;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $admin = User::query()->latest()->first();

        $group = Group::query()->create([
            'code' => Str::random(9),
            'user_id' => $admin->id,
            'title' => 'Kelompok Diskusi A',
            'description' => 'Group kelompok diskusi A!',
        ]);

        $users = User::query()->get();

        foreach ($users as $user) {
            GroupMember::query()->create(['group_id' => $group->id, 'user_id' => $user->id]);
        }

        GroupChat::query()->create([
            'group_id' => $group->id,
            'user_id' => $admin->id,
            'text' => 'Halo semuanya!'
        ]);
    }
}
