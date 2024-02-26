<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Laboratory extends Model
{
    use HasFactory;
     public function laboratorySection():HasMany
     {
        return $this->hasMany(LaboratorySection::class,'laboratory_id');
     }
}
