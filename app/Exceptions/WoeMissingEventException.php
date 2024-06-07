<?php

namespace App\Exceptions;

use Exception;

class WoeMissingEventException extends Exception
{
    public function __construct($eventType)
    {
        $this->message = "No {$eventType} event found.";
    }
}
