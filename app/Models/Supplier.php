<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
class Supplier extends Model
{
    use HasFactory;
     use SoftDeletes;
    
     protected $fillable = [
        'supplier_name',
        'contact_person',
        'address',
        'email',
        'phone_number',
        'contract_expiry',
    ];
    
    protected static function booted()
    {
        static::saving(function() {
            Cache::forget('supplier');
        });
        
        
        static::deleted(function() {
    Cache::forget('supplier');
});
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'contract_expiry' => 'date',
    ];
}