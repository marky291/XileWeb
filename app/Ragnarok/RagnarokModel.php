<?php

namespace App\Ragnarok;

use Illuminate\Database\Eloquent\Model;

abstract class RagnarokModel extends Model
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
