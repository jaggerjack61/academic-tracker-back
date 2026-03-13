<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class PaymentLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'payment_id',
        'payment_id_ref',
        'student_id_ref',
        'term_id_ref',
        'fee_type_id_ref',
        'action',
        'actor_user_id',
        'actor_email',
        'student_name',
        'term_name',
        'fee_type_name',
        'amount',
        'payment_date',
        'method',
        'reference',
        'note',
        'changes',
        'snapshot',
        'created_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'changes' => 'array',
        'snapshot' => 'array',
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (PaymentLog $paymentLog): void {
            if (! $paymentLog->created_at) {
                $paymentLog->created_at = now();
            }
        });

        static::updating(function (): void {
            throw ValidationException::withMessages(['error' => 'Payment logs are immutable.']);
        });

        static::deleting(function (): void {
            throw ValidationException::withMessages(['error' => 'Payment logs cannot be deleted.']);
        });
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }
}
