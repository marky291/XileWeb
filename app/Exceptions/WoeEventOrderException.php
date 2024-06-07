<?php

namespace App\Exceptions;

use Exception;

class WoeEventOrderException extends Exception
{
    protected $message = 'Events are out of order.';
}
