<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\GroupChat;
use App\Models\GroupMember;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $user = User::query()->latest()->first();

        $group = Group::query()->create([
            'user_id' => $user->id,
            'title' => 'Kelompok Diskusi A',
            'description' => 'Group kelompok diskusi A!',
        ]);

        $users = User::query()->get();

        foreach ($users as $user) {
            GroupMember::query()->create(['group_id' => $group->id, 'user_id' => $user->id]);
        }

        GroupChat::query()->create([
            'group_id' => $group->id,
            'user_id' => $user->id,
            'text' => 'Halo semuanya!'
        ]);
    }
}
