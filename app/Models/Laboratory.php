<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
class Laboratory extends Model
{
    use HasFactory;
    use SoftDeletes;
     public function laboratorySection():HasMany
     {
        return $this->hasMany(LaboratorySection::class,'laboratory_id');
     }
}