<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyProfile extends Model
{
    protected $primaryKey = 'customer_id';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'customer_id',
        'company_id',
        'is_agency',
        'commission_rate',
        'account_manager',
    ];

    protected $casts = [
        'is_agency' => 'boolean',
        'commission_rate' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
