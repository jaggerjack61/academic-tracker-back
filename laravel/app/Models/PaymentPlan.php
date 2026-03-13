<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'term_id',
        'fee_type_id',
        'total_amount',
        'installments',
        'description',
        'is_active',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'student_id');
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function feeType(): BelongsTo
    {
        return $this->belongsTo(FeeType::class);
    }

    public function planInstallments(): HasMany
    {
        return $this->hasMany(PaymentPlanInstallment::class, 'plan_id')->orderBy('installment_number');
    }
}
