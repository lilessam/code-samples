<?php
namespace App\Converter\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class UsefulException extends Exception
{
    /**
     * @param \Exception $e
     * @param int $line
     * @param array [
     *      'content' => 'NEARBY CONTENT',
     *      'tab' => 'ERROR TAB'
     * ]
     *
     * @return void
     *
     * @throws FormattingException
     */
    public function __construct($e, $line, $details = [])
    {
        if ($line == 0 && str_contains($e->getMessage(), 'Undefined index: ')) {

            $message = "Missing column: " . preg_replace('/([a-z])([A-Z])/s','$1 $2', studly_case(trim(explode('Undefined index: ', $e->getMessage())[1])));
            parent::__construct($message);
        } else {
            if (count($details) == 0) {
                throw new FormattingException(sprintf("There's an issue happening while processing line number %s", $line));
            } else {
                $message = "";
                if (key_exists('content', $details)) {
                    $message .= sprintf("There's a formatting error near the lines contain [%s].", $details['content']);
                }
                $message .= sprintf(" Error near line: [%s]", $line);
                if (key_exists('tab', $details)) {
                    $message .= sprintf(" In tab: [%s]", $details['tab']);
                }

                throw new FormattingException($message);
            }
        }
        Log::error($e);
    }
}
