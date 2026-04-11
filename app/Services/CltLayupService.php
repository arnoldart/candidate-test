<?php

namespace App\Services;

use App\Contracts\CltLayupServiceInterface;
use App\Models\CltLayup;

class CltLayupService implements CltLayupServiceInterface
{
    public function duplicate(CltLayup $layup): CltLayup
    {
        $newLayup = $layup->replicate();
        $newLayup->name = $layup->name . ' (Copy)';
        $newLayup->save();

        foreach ($layup->cltLayers as $layer) {
            $newLayer = $layer->replicate();
            $newLayer->layup_id = $newLayup->id;
            $newLayer->save();
        }

        return $newLayup;
    }
}
