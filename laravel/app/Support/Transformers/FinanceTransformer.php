<?php

namespace App\Support\Transformers;

use App\Models\FeeStructure;
use App\Models\FeeType;
use App\Models\Payment;
use App\Models\PaymentLog;
use App\Models\PaymentPlan;
use App\Models\PaymentPlanInstallment;
use App\Models\SpecialFee;

class FinanceTransformer
{
    public static function feeType(FeeType $feeType): array
    {
        return [
            'id' => $feeType->id,
            'name' => $feeType->name,
            'description' => $feeType->description,
            'is_active' => (bool) $feeType->is_active,
            'created_at' => optional($feeType->created_at)?->toISOString(),
            'updated_at' => optional($feeType->updated_at)?->toISOString(),
        ];
    }

    public static function feeStructure(FeeStructure $structure): array
    {
        $structure->loadMissing(['feeType', 'grade', 'term']);

        return [
            'id' => $structure->id,
            'fee_type_id' => $structure->fee_type_id,
            'grade_id' => $structure->grade_id,
            'term_id' => $structure->term_id,
            'amount' => (string) $structure->amount,
            'is_active' => (bool) $structure->is_active,
            'fee_type_name' => $structure->feeType?->name,
            'grade_name' => $structure->grade?->name,
            'term_name' => $structure->term?->name,
            'created_at' => optional($structure->created_at)?->toISOString(),
            'updated_at' => optional($structure->updated_at)?->toISOString(),
        ];
    }

    public static function specialFee(SpecialFee $specialFee): array
    {
        $specialFee->loadMissing(['student', 'term']);

        return [
            'id' => $specialFee->id,
            'student_id' => $specialFee->student_id,
            'term_id' => $specialFee->term_id,
            'name' => $specialFee->name,
            'amount' => (string) $specialFee->amount,
            'description' => $specialFee->description,
            'is_active' => (bool) $specialFee->is_active,
            'student_name' => $specialFee->student?->full_name,
            'term_name' => $specialFee->term?->name,
            'created_at' => optional($specialFee->created_at)?->toISOString(),
            'updated_at' => optional($specialFee->updated_at)?->toISOString(),
        ];
    }

    public static function installment(PaymentPlanInstallment $installment): array
    {
        return [
            'id' => $installment->id,
            'plan_id' => $installment->plan_id,
            'installment_number' => $installment->installment_number,
            'amount' => (string) $installment->amount,
            'due_date' => $installment->due_date,
            'payment_id' => $installment->payment_id,
            'is_paid' => (bool) $installment->is_paid,
            'paid_date' => $installment->paid_date,
            'created_at' => optional($installment->created_at)?->toISOString(),
            'updated_at' => optional($installment->updated_at)?->toISOString(),
        ];
    }

    public static function paymentPlan(PaymentPlan $plan): array
    {
        $plan->loadMissing(['student', 'term', 'feeType', 'planInstallments']);
        $paidAmount = $plan->planInstallments->where('is_paid', true)->sum('amount');

        return [
            'id' => $plan->id,
            'student_id' => $plan->student_id,
            'term_id' => $plan->term_id,
            'fee_type_id' => $plan->fee_type_id,
            'total_amount' => (string) $plan->total_amount,
            'installments' => $plan->installments,
            'description' => $plan->description,
            'is_active' => (bool) $plan->is_active,
            'student_name' => $plan->student?->full_name,
            'term_name' => $plan->term?->name,
            'fee_type_name' => $plan->feeType?->name,
            'plan_installments' => $plan->planInstallments->map(fn (PaymentPlanInstallment $item) => self::installment($item))->all(),
            'paid_amount' => (float) $paidAmount,
            'created_at' => optional($plan->created_at)?->toISOString(),
            'updated_at' => optional($plan->updated_at)?->toISOString(),
        ];
    }

    public static function payment(Payment $payment): array
    {
        $payment->loadMissing(['student', 'term', 'feeType', 'createdBy.profile']);
        $createdBy = $payment->createdBy;

        return [
            'id' => $payment->id,
            'student_id' => $payment->student_id,
            'term_id' => $payment->term_id,
            'fee_type_id' => $payment->fee_type_id,
            'amount' => (string) $payment->amount,
            'payment_date' => $payment->payment_date,
            'method' => $payment->method,
            'reference' => $payment->reference,
            'note' => $payment->note,
            'created_by_user_id' => $payment->created_by_user_id,
            'student_name' => $payment->student?->full_name,
            'term_name' => $payment->term?->name,
            'fee_type_name' => $payment->feeType?->name,
            'created_by_name' => $createdBy?->profile?->full_name ?: ($createdBy?->username ?? null),
            'created_at' => optional($payment->created_at)?->toISOString(),
            'updated_at' => optional($payment->updated_at)?->toISOString(),
        ];
    }

    public static function paymentLog(PaymentLog $log): array
    {
        $log->loadMissing('actor.profile');
        $actorName = $log->actor?->profile?->full_name ?: ($log->actor?->username ?: ($log->actor_email ?: null));

        if ($log->action === 'create') {
            $summary = 'Payment recorded';
        } elseif ($log->action === 'delete') {
            $summary = 'Payment deleted';
        } else {
            $labels = collect($log->changes)->pluck('label')->filter()->values()->all();
            $summary = $labels ? implode(', ', $labels) : 'Payment updated';
        }

        return [
            'id' => $log->id,
            'payment_id' => $log->payment_id,
            'payment_id_ref' => $log->payment_id_ref,
            'student_id_ref' => $log->student_id_ref,
            'term_id_ref' => $log->term_id_ref,
            'fee_type_id_ref' => $log->fee_type_id_ref,
            'action' => $log->action,
            'actor_user_id' => $log->actor_user_id,
            'actor_email' => $log->actor_email,
            'student_name' => $log->student_name,
            'term_name' => $log->term_name,
            'fee_type_name' => $log->fee_type_name,
            'amount' => $log->amount !== null ? (string) $log->amount : null,
            'payment_date' => $log->payment_date,
            'method' => $log->method,
            'reference' => $log->reference,
            'note' => $log->note,
            'changes' => $log->changes,
            'snapshot' => $log->snapshot,
            'actor_name' => $actorName,
            'change_summary' => $summary,
            'created_at' => optional($log->created_at)?->toISOString(),
        ];
    }
}