<?php

namespace Database\Seeders;

use Bouncer;
use Illuminate\Database\Seeder;
use App\Models\User;

class AllowAdminEverything extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run($userId = 1): void
    {
        $user = User::find($userId);
        if ($user) {
            Bouncer::assign('admin')->to($user);
            $this->command->getOutput()->writeln('Admin role assigned to user ' . $user->email);
        } else {
            $this->command->getOutput()->writeln('There is no user with id ' . $userId);
        }
    }
}
