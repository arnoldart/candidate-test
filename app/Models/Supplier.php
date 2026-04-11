<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'supplier';

    protected $fillable = [
        'name',
        'primary_contact',
        'location',
        'material_certifications',
        'last_audit_date',
    ];

    public function cltLayups(): HasMany
    {
        return $this->hasMany(CltLayup::class);
    }
}
