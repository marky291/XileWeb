<?php

namespace App\XileRetro;

use Illuminate\Database\Eloquent\Model;

abstract class XileRetro_Model extends Model
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
