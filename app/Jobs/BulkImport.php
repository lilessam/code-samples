<?php

namespace App\Jobs;

use Exception;
use App\Events\ImportDone;
use Illuminate\Bus\Queueable;
use App\Events\OperationFailed;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class BulkImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var array
     */
    protected $input_path;

    /**
     * @var int
     */
    protected $user_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($input_path, $user_id)
    {
        $this->input_path = $input_path;
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            //
            Log::info(sprintf('A bulk import process has been fired! User ID => %s', $this->user_id));

            // Running the import:conversions command
            // @uses App\Console\Commands\ImportConversions::class
            Artisan::call('import:conversions', [
                'path' => $this->input_path['path'],
            ]);

            // Cleaning up the imported data using merge:accounts command
            // @uses App\Console\Commands\MergeAccounts::class
            Artisan::call('merge:accounts');

            // If all commands above are executed successfully
            // We'll broadcast and event to SocketIO to
            // Let the user know.
            broadcast(new ImportDone($this->user_id));
        } catch (Exception $e) {
            broadcast(new OperationFailed($e->getMessage(), $this->user_id));
        }
    }
}
