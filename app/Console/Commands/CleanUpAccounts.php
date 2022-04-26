<?php

namespace App\Console\Commands;

use App\Repositories\Accounts;
use Illuminate\Console\Command;

class CleanUpAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:accounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up accounts that don\'t have city and state.';

    /**
     * @var \App\Repositories\Accounts
     */
    protected $repository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Accounts $accounts)
    {
        parent::__construct();

        $this->repository = $accounts;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        dump($this->repository->delete(function($query) {
            return $query->whereNull('city')
                        ->orWhereNull('state')
                        ->orWhere('city', '')
                        ->orWhere('state', '');
        }));
    }
}
