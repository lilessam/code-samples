<?php

namespace App\Console\Commands;

use App\Repositories\Accounts;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MergeAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'merge:accounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Merge accounts that share the same output.';

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
        Log::info('Merging accounts process has been fired!');

        $accounts = $this->repository->all();
        $similarities = [];
        foreach ($accounts as $account) {
            $similarAccount = $accounts->first(function ($model) use ($account) {
                return $model->name == $account->name && $model->city == $account->city && $model->state == $account->state && $model->id != $account->id;
            });

            if ($similarAccount) {
                $similarities[] = [
                    $account,
                    $similarAccount
                ];
            }
        }

        $this->line(sprintf('Found %d similarities.', count($similarities)));

        foreach ($similarities as $similarity) {
            $master = $this->repository->get($similarity[0]->id);
            $sub = $this->repository->get($similarity[1]->id);
            if ($master && $sub) {
                $this->repository->saveMatches($master, $sub->matches);

                $this->repository->delete($sub->id);
            }
        }

        $this->info('Done!');
    }
}
