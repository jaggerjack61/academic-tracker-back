<?php

namespace App\Support\Finance;

use App\Models\Payment;
use App\Models\Profile;
use App\Models\User;

class PaymentMutationService
{
    public static function create(
        Profile $student,
        int $termId,
        string|float|int $amount,
        string $paymentDate,
        string $method,
        string $reference,
        string $note,
        int|string|null $feeTypeId,
        ?User $actor
    ): Payment {
        $payment = Payment::query()->create([
            'student_id' => $student->id,
            'term_id' => $termId,
            'fee_type_id' => $feeTypeId ?: null,
            'amount' => $amount,
            'payment_date' => $paymentDate,
            'method' => $method,
            'reference' => $reference,
            'note' => $note,
            'created_by_user_id' => $actor?->id,
        ]);

        $payment->load(['student.user', 'term', 'feeType', 'createdBy.profile']);
        PaymentAuditService::log($payment, 'create', $actor);

        return $payment;
    }

    public static function delete(Payment $payment, ?User $actor): void
    {
        $payment->load(['planInstallments', 'student.user', 'term', 'feeType', 'createdBy.profile']);
        $snapshot = PaymentAuditService::snapshot($payment);
        PaymentAuditService::log($payment, 'delete', $actor, $snapshot);

        foreach ($payment->planInstallments as $installment) {
            $installment->update([
                'payment_id' => null,
                'is_paid' => false,
                'paid_date' => '',
            ]);
        }

        $payment->delete();
    }
}