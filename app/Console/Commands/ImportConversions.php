<?php

namespace App\Console\Commands;

use App\Repositories\Accounts;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ImportConversions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:conversions {path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import excel sheet that contains conversions.';

    /**
     * @var \Collection
     */
    protected $accounts;

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
        $this->repository = $accounts;
        $this->accounts = collect([]);
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::info('Importing conversions process has been fired!');

        ini_set('memory_limit', '2048M');

        $path = $this->argument('path');

        Excel::selectSheetsByIndex(0)->load($path, function ($reader) {
            $rows = $reader->get()->toArray();
            foreach ($rows as $row) {
                if ($row['output_company_name']) {

                    $account = $this->accounts->first(function($account) use ($row) {
                        $account = (object) $account;
                        return trim($account->name) == trim($row['output_company_name']) && trim($account->city) == trim($row['output_city']) && trim($account->state) == trim($row['output_state']);
                    });


                    if (!$account) {
                        $new = true;
                        $account = [
                            'name' => trim($row['output_company_name']),
                            'city' => trim($row['output_city']),
                            'state' => trim($row['output_state']),
                            'matches' => []
                        ];
                    } else {
                        $new = false;
                        $account = (array) $account;
                    }

                    foreach (config('converter.drivers') as $driver) {
                        $key = strtolower($driver);

                        if (key_exists($key, $row) && $row[$key] != null && $row['source_company_name'] != null) {
                            $account['matches'][] = [
                                'driver' => $driver,
                                'name' => trim($row['source_company_name']),
                                'city' => trim($row['source_company_city']),
                                'state' => trim($row['source_company_state']),
                                'zipcode' => $row['source_company_zipcode']
                            ];
                        }
                    }

                    if ($new) {
                        $this->accounts[] = $account;
                    } else {
                        $updatedAccount = $account;

                        $this->accounts = $this->accounts->map(function($account) use ($updatedAccount) {
                            $account = (object) $account;

                            if (trim($account->name) == trim($updatedAccount['name']) && trim($account->city) == trim($updatedAccount['city']) && trim($account->state) == trim($updatedAccount['state'])) {
                                $account = $updatedAccount;
                            }
                            return $account;
                        });
                    }
                }
            }
        });

        $this->repository->saveMany($this->accounts->toArray());
    }
}
