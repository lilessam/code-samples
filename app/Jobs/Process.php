<?php

namespace App\Jobs;

use Exception;
use App\Events\OperationDone;
use Illuminate\Bus\Queueable;
use App\Events\OperationFailed;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Converter\Exceptions\FormattingException;

class Process implements ShouldQueue
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
            $converter = converter()->guessDriver($this->input_path['name'])
                                    ->setInputFileName($this->input_path['name'])
                                    ->setUser($this->user_id)
                                    ->input($this->input_path['path'])
                                    ->process();
            $operation = $converter->log();

            broadcast(new OperationDone($operation));
        } catch (FormattingException $e) {
            Log::error($e);
            broadcast(new OperationFailed($e->getMessage(), $this->user_id));
        } catch (Exception $e) {
            Log::error($e);
            broadcast(new OperationFailed($e->getMessage(), $this->user_id));
        }
    }

    /**
    * The job failed to process.
    *
    * @param  \Exception  $e
    * @return void
    */
    public function failed(Exception $e)
    {
        Log::error($e);
        broadcast(new OperationFailed($e->getMessage(), $this->user_id));
    }
}
