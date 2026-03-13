<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Term extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'start', 'end', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function feeStructures(): HasMany
    {
        return $this->hasMany(FeeStructure::class);
    }

    public function specialFees(): HasMany
    {
        return $this->hasMany(SpecialFee::class);
    }

    public function paymentPlans(): HasMany
    {
        return $this->hasMany(PaymentPlan::class);
    }

    public function feePayments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
