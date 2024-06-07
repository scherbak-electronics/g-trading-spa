<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ListUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:users:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all users';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $users = User::all();
        $tableData = $users->map(function ($user) {
            return [
                'ID' => $user->id,
                'Email' => $user->email,
            ];
        })->toArray();

        $this->table(
            [
                'ID',
                'Email',
            ],
            $tableData
        );
    }
}
