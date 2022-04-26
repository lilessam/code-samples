<?php
namespace App\Converter\Exceptions;

use Exception;

class FormattingException extends Exception
{
    /**
     * @param string $message
     */
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
