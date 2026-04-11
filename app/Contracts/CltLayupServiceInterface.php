<?php

namespace App\Contracts;

use App\Models\CltLayup;

interface CltLayupServiceInterface
{
    /**
     * Duplicate a layup and its associated layers.
     */
    public function duplicate(CltLayup $layup): CltLayup;
}
