<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Models\PaymentPlan;
use App\Models\PaymentPlanInstallment;
use App\Support\Api\ApiResponse;
use App\Support\Api\ManualPaginator;
use App\Support\Finance\PaymentMutationService;
use App\Support\Transformers\FinanceTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentPlanController extends Controller
{
    public function index(Request $request)
    {
        $query = PaymentPlan::query()->with(['student.user', 'term', 'feeType', 'planInstallments.payment']);
        $search = (string) $request->query('search', '');
        if ($search !== '') {
            $query->whereHas('student', function ($studentQuery) use ($search): void {
                $studentQuery->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }
        if ($request->filled('term')) {
            $query->where('term_id', $request->query('term'));
        }
        if ($request->filled('student')) {
            $query->where('student_id', $request->query('student'));
        }
        $query->latest();

        [$items, $total, $page, $pageSize] = ManualPaginator::fromQuery($query, $request, 20);

        return ApiResponse::paginated(
            $items->map(fn ($item) => FinanceTransformer::paymentPlan($item))->all(),
            $total,
            $page,
            $pageSize,
        );
    }

    public function store(Request $request)
    {
        $studentId = $request->input('student');
        $termId = $request->input('term');
        $totalAmount = $request->input('total_amount');
        $installmentCount = $request->input('installments');
        if (! $studentId || ! $termId || $totalAmount === null || $totalAmount === '' || ! $installmentCount) {
            return ApiResponse::error('All fields are required');
        }

        $plan = DB::transaction(function () use ($request, $studentId, $termId, $totalAmount, $installmentCount) {
            $plan = PaymentPlan::query()->create([
                'student_id' => $studentId,
                'term_id' => $termId,
                'fee_type_id' => $request->input('fee_type') ?: null,
                'total_amount' => $totalAmount,
                'installments' => $installmentCount,
                'description' => (string) $request->input('description', ''),
                'is_active' => true,
            ]);

            foreach ((array) $request->input('installments_data', []) as $index => $installment) {
                PaymentPlanInstallment::query()->create([
                    'plan_id' => $plan->id,
                    'installment_number' => $index + 1,
                    'amount' => $installment['amount'] ?? 0,
                    'due_date' => $installment['due_date'] ?? '',
                    'payment_id' => null,
                    'is_paid' => false,
                    'paid_date' => '',
                ]);
            }

            return $plan->load(['student.user', 'term', 'feeType', 'planInstallments.payment']);
        });

        return ApiResponse::created(FinanceTransformer::paymentPlan($plan));
    }

    public function show(int $pk)
    {
        $plan = PaymentPlan::query()->with(['student.user', 'term', 'feeType', 'planInstallments.payment'])->find($pk);
        if (! $plan) {
            return ApiResponse::notFound();
        }

        return ApiResponse::ok(FinanceTransformer::paymentPlan($plan));
    }

    public function togglePaid(Request $request, int $pk)
    {
        $installment = PaymentPlanInstallment::query()->with(['plan.student.user', 'plan.term', 'plan.feeType', 'payment'])->find($pk);
        if (! $installment) {
            return ApiResponse::notFound();
        }

        if ($installment->is_paid) {
            if (! $request->boolean('mark_unpaid')) {
                return ApiResponse::error('Installment is already marked paid');
            }

            DB::transaction(function () use ($installment, $request): void {
                if ($installment->payment_id) {
                    PaymentMutationService::delete($installment->payment, $request->user());
                } else {
                    $installment->update([
                        'payment_id' => null,
                        'is_paid' => false,
                        'paid_date' => '',
                    ]);
                }
            });

            $installment->refresh();

            return ApiResponse::ok(FinanceTransformer::installment($installment));
        }

        $paymentDate = trim((string) $request->input('payment_date', ''));
        $termId = $request->input('term');
        $amount = $request->input('amount', $installment->amount);
        $feeTypeId = $request->input('fee_type', $installment->plan->fee_type_id) ?: null;
        if ($paymentDate === '') {
            return ApiResponse::error('Payment date is required');
        }
        if ($termId !== null && $termId !== '' && (string) $termId !== (string) $installment->plan->term_id) {
            return ApiResponse::error('Installment term is fixed by the plan');
        }
        if ((float) $amount !== (float) $installment->amount) {
            return ApiResponse::error('Installment amount is fixed by the plan');
        }
        if ($installment->plan->fee_type_id && $feeTypeId && (int) $feeTypeId !== $installment->plan->fee_type_id) {
            return ApiResponse::error('Fee type is fixed by the plan');
        }

        DB::transaction(function () use ($installment, $feeTypeId, $paymentDate, $request): void {
            if (! $installment->plan->fee_type_id && $feeTypeId) {
                $installment->plan->update(['fee_type_id' => $feeTypeId]);
            }

            $payment = PaymentMutationService::create(
                student: $installment->plan->student,
                termId: $installment->plan->term_id,
                amount: $installment->amount,
                paymentDate: $paymentDate,
                method: (string) $request->input('method', 'cash'),
                reference: (string) $request->input('reference', ''),
                note: (string) $request->input('note', ''),
                feeTypeId: $feeTypeId,
                actor: $request->user(),
            );

            $installment->update([
                'payment_id' => $payment->id,
                'is_paid' => true,
                'paid_date' => $paymentDate,
            ]);
        });

        $installment->refresh();

        return ApiResponse::ok(FinanceTransformer::installment($installment));
    }
}
