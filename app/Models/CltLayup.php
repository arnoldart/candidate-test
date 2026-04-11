<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CltLayup extends Model
{
    use HasFactory;

    protected $fillable = [
        "supplier_id",
        "name",
        "species_grade",
        "status",
        "created_by",
    ];

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($layup) {
            $layup->revision = $layup->revision + 1;
        });
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function cltLayers(): HasMany
    {
        return $this->hasMany(CltLayer::class, 'layup_id');
    }
}
