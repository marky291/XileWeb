<?php

namespace App\XileRO;

use Illuminate\Database\Eloquent\Model;

abstract class XileRO_Model extends Model
{
    /**
     * Get the current connection name for the model.
     *
     * @return string|null
     */
    public function getConnectionName()
    {
        if (! app()->runningUnitTests()) {
            return $this->connection;
        }
    }
}
