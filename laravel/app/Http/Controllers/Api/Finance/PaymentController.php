<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Profile;
use App\Support\Api\ApiResponse;
use App\Support\Api\ManualPaginator;
use App\Support\Finance\PaymentAuditService;
use App\Support\Finance\PaymentMutationService;
use App\Support\Transformers\FinanceTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::query()->with(['student.user', 'term', 'feeType', 'createdBy.profile']);
        $search = (string) $request->query('search', '');
        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('reference', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($studentQuery) use ($search): void {
                        $studentQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('id_number', 'like', "%{$search}%");
                    });
            });
        }
        if ($request->filled('term')) {
            $query->where('term_id', $request->query('term'));
        }
        $query->latest();

        [$items, $total, $page, $pageSize] = ManualPaginator::fromQuery($query, $request, 20);

        return ApiResponse::paginated(
            $items->map(fn ($item) => FinanceTransformer::payment($item))->all(),
            $total,
            $page,
            $pageSize,
        );
    }

    public function store(Request $request)
    {
        $studentId = $request->input('student');
        $termId = $request->input('term');
        $amount = $request->input('amount');
        $paymentDate = (string) $request->input('payment_date', '');
        if (! $studentId || ! $termId || $amount === null || $amount === '' || $paymentDate === '') {
            return ApiResponse::error('Student, term, amount and date are required');
        }

        $student = Profile::query()->where('id', $studentId)->where('type', 'student')->first();
        if (! $student) {
            return ApiResponse::notFound('Student not found');
        }

        $payment = DB::transaction(fn () => PaymentMutationService::create(
            student: $student,
            termId: (int) $termId,
            amount: $amount,
            paymentDate: $paymentDate,
            method: (string) $request->input('method', 'cash'),
            reference: (string) $request->input('reference', ''),
            note: (string) $request->input('note', ''),
            feeTypeId: $request->input('fee_type') ?: null,
            actor: $request->user(),
        ));

        return ApiResponse::created(FinanceTransformer::payment($payment));
    }

    public function show(int $pk)
    {
        $payment = Payment::query()->with(['student.user', 'term', 'feeType', 'createdBy.profile'])->find($pk);
        if (! $payment) {
            return ApiResponse::notFound();
        }

        return ApiResponse::ok(FinanceTransformer::payment($payment));
    }

    public function update(Request $request, int $pk)
    {
        $payment = Payment::query()->with(['student.user', 'term', 'feeType', 'createdBy.profile', 'planInstallments.plan.term', 'planInstallments.plan.feeType'])->find($pk);
        if (! $payment) {
            return ApiResponse::notFound();
        }

        $studentId = $request->input('student', $payment->student_id);
        $termId = $request->input('term', $payment->term_id);
        $amount = $request->input('amount', $payment->amount);
        $paymentDate = (string) $request->input('payment_date', $payment->payment_date);
        $method = (string) $request->input('method', $payment->method);
        $reference = (string) $request->input('reference', $payment->reference);
        $note = (string) $request->input('note', $payment->note);
        $feeTypeId = $request->input('fee_type', $payment->fee_type_id);
        if ($feeTypeId === '') {
            $feeTypeId = null;
        }

        if (! $studentId || ! $termId || $amount === null || $amount === '' || $paymentDate === '') {
            return ApiResponse::error('Student, term, amount and date are required');
        }

        $student = Profile::query()->where('id', $studentId)->where('type', 'student')->first();
        if (! $student) {
            return ApiResponse::notFound('Student not found');
        }

        $linkedInstallment = $payment->planInstallments->first();
        if ($linkedInstallment) {
            if ((float) $amount !== (float) $linkedInstallment->amount) {
                return ApiResponse::error('Installment payment amount is fixed by the plan');
            }
            if ((string) $termId !== (string) $linkedInstallment->plan->term_id) {
                return ApiResponse::error('Installment payment term is fixed by the plan');
            }
            if ($linkedInstallment->plan->fee_type_id && (string) ($feeTypeId ?? '') !== (string) $linkedInstallment->plan->fee_type_id) {
                return ApiResponse::error('Installment payment fee type is fixed by the plan');
            }
        }

        $before = PaymentAuditService::snapshot($payment);

        DB::transaction(function () use ($payment, $student, $termId, $amount, $paymentDate, $method, $reference, $note, $feeTypeId, $linkedInstallment): void {
            $payment->update([
                'student_id' => $student->id,
                'term_id' => $termId,
                'amount' => $amount,
                'payment_date' => $paymentDate,
                'method' => $method,
                'reference' => $reference,
                'note' => $note,
                'fee_type_id' => $feeTypeId,
            ]);

            if ($linkedInstallment) {
                $linkedInstallment->update([
                    'paid_date' => $paymentDate,
                    'is_paid' => true,
                ]);
            }
        });

        $payment->refresh();
        $payment->load(['student.user', 'term', 'feeType', 'createdBy.profile']);
        $after = PaymentAuditService::snapshot($payment);
        $changes = PaymentAuditService::diff($before, $after);
        if ($changes !== []) {
            PaymentAuditService::log($payment, 'update', $request->user(), $after, $changes);
        }

        return ApiResponse::ok(FinanceTransformer::payment($payment));
    }

    public function destroy(Request $request, int $pk)
    {
        $payment = Payment::query()->with(['planInstallments', 'student.user', 'term', 'feeType', 'createdBy.profile'])->find($pk);
        if (! $payment) {
            return ApiResponse::notFound();
        }

        DB::transaction(fn () => PaymentMutationService::delete($payment, $request->user()));

        return response()->json([], 204);
    }
}
