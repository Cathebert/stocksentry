<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class IssueDetails extends Model
{
    use HasFactory;
   protected $table="issue_details";

   public function details(): BelongsTo
    {
        return $this->belongsTo(Issue::class,'issue_id');
    } 

      public function IssueOwner(): HasOneThrough
    {
        return $this->hasOneThrough(Laboratory::class,Issue::class, 
        'from_lab_id', // Foreign key on the cars table...
            'id', // Foreign key on the owners table...
            'issue_id', // Local key on the mechanics table...
            'id'
    );
    }
}
