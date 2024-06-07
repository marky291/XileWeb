<?php

namespace App\Exceptions;

use Exception;

class WoeNotCompletedException extends Exception
{
    protected $message = 'WOE not completed. Missing STARTED or ENDED event.';
}
