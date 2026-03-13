<?php

namespace App\Support\Finance;

use App\Models\Payment;
use App\Models\PaymentLog;
use App\Models\User;

class PaymentAuditService
{
    private const AUDIT_FIELDS = [
        ['student_name', 'Student'],
        ['term_name', 'Term'],
        ['fee_type_name', 'Fee Type'],
        ['amount', 'Amount'],
        ['payment_date', 'Payment Date'],
        ['method', 'Method'],
        ['reference', 'Reference'],
        ['note', 'Note'],
    ];

    public static function snapshot(Payment $payment): array
    {
        $payment->loadMissing(['student.user', 'term', 'feeType', 'createdBy.profile']);
        $createdBy = $payment->createdBy;

        return [
            'id' => $payment->id,
            'student_id' => $payment->student_id,
            'student_name' => $payment->student?->full_name ?? '',
            'term_id' => $payment->term_id,
            'term_name' => $payment->term?->name ?? '',
            'fee_type_id' => $payment->fee_type_id,
            'fee_type_name' => $payment->feeType?->name ?? '',
            'amount' => (string) $payment->amount,
            'payment_date' => $payment->payment_date,
            'method' => $payment->method,
            'reference' => $payment->reference,
            'note' => $payment->note,
            'created_by_id' => $payment->created_by_user_id,
            'created_by_name' => $createdBy?->profile?->full_name ?: ($createdBy?->username ?? ''),
            'created_by_email' => $createdBy?->email ?? '',
        ];
    }

    public static function diff(array $before, array $after): array
    {
        $changes = [];

        foreach (self::AUDIT_FIELDS as [$field, $label]) {
            if (($before[$field] ?? null) !== ($after[$field] ?? null)) {
                $changes[] = [
                    'field' => $field,
                    'label' => $label,
                    'before' => $before[$field] ?? null,
                    'after' => $after[$field] ?? null,
                ];
            }
        }

        return $changes;
    }

    public static function log(Payment $payment, string $action, ?User $actor = null, ?array $snapshot = null, array $changes = []): PaymentLog
    {
        $paymentSnapshot = $snapshot ?: self::snapshot($payment);

        return PaymentLog::query()->create([
            'payment_id' => $payment->id,
            'payment_id_ref' => $paymentSnapshot['id'] ?? $payment->id,
            'student_id_ref' => $paymentSnapshot['student_id'] ?? null,
            'term_id_ref' => $paymentSnapshot['term_id'] ?? null,
            'fee_type_id_ref' => $paymentSnapshot['fee_type_id'] ?? null,
            'action' => $action,
            'actor_user_id' => $actor?->id,
            'actor_email' => ($actor?->email ?: '') ?: ($paymentSnapshot['created_by_email'] ?? ''),
            'student_name' => $paymentSnapshot['student_name'] ?? '',
            'term_name' => $paymentSnapshot['term_name'] ?? '',
            'fee_type_name' => $paymentSnapshot['fee_type_name'] ?? '',
            'amount' => $paymentSnapshot['amount'] ?? null,
            'payment_date' => $paymentSnapshot['payment_date'] ?? '',
            'method' => $paymentSnapshot['method'] ?? '',
            'reference' => $paymentSnapshot['reference'] ?? '',
            'note' => $paymentSnapshot['note'] ?? '',
            'changes' => $changes,
            'snapshot' => $paymentSnapshot,
        ]);
    }
}