<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class LaboratorySection extends Model
{
    use HasFactory;
    public function section(): BelongsTo
    {
        return $this->belongsTo(Laboratory::class,'laboratory_id');
    }
}
