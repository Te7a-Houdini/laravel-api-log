<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Faker\Generator as Faker;

class RandomizeUserAttrsWithId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:randomize {userId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sets random attribute values for specified user id';

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
    public function handle(Faker $faker)
    {
        try {
            $user = User::findOrFail($this->argument('userId'));

            $this->warn('attrs before changing');
            $this->line(implode(' , ', $user->only(['name','email', 'time_zone'])));

            $this->info('attrs after change');
            $user->update([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'time_zone' => $faker->timezone,
            ]);
            $this->line(implode(' , ', $user->only(['name','email', 'time_zone'])));
        } catch (ModelNotFoundException $e) {
            $this->error('Sorry no user is found with id [' . $this->argument('userId') .']');
        }
    }
}
