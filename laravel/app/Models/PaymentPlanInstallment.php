<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentPlanInstallment extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_id',
        'installment_number',
        'amount',
        'due_date',
        'payment_id',
        'is_paid',
        'paid_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_paid' => 'boolean',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(PaymentPlan::class, 'plan_id');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
