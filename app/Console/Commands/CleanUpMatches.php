<?php

namespace App\Console\Commands;

use App\Repositories\Matches;
use Illuminate\Console\Command;

class CleanUpMatches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:matches';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up matches form database that their account does\'nt exist.';

    /**
     * @var \App\Repositories\Matches
     */
    protected $repository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Matches $matches)
    {
        parent::__construct();

        $this->repository = $matches;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $matches = $this->repository->all();
        $numberOfCleanedUp = 0;
        //
        foreach ($matches as $match) {
            if (!isset($match->account)) {
                $match->delete();
                $numberOfCleanedUp++;
            }
        }

        $this->info(sprintf('Cleaned up %s matches.', $numberOfCleanedUp));
    }
}
