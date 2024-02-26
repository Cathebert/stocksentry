<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    
    
     protected $fillable = [
        'supplier_name',
        'contact_person',
        'address',
        'email',
        'phone_number',
        'contract_expiry',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'contract_expiry' => 'date',
    ];
}
