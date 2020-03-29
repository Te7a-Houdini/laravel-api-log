<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;

class SyncUserAttrs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:user-attrs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync user attributes with our third party api';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $now = now();

        User::whereRaw('updated_at > last_synced_at')
            ->orWhereNull('last_synced_at')
            ->chunkById(1000, function ($users) use ($now) {
                $users->each(function ($user) {
                    info("[{$user->id}] name: {$user->name}, timezone: '{$user->time_zone}'");
                });

                User::whereIn('id', $users->pluck('id'))->update([
                    'last_synced_at' => $now,
                ]);
            });
    }
}
