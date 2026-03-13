<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Models\FeeStructure;
use App\Models\Payment;
use App\Models\PaymentPlan;
use App\Models\Profile;
use App\Models\SpecialFee;
use App\Models\Term;
use App\Support\Api\ApiResponse;
use App\Support\Api\ManualPaginator;
use App\Support\Finance\FeeCalculator;
use App\Support\Transformers\CoreTransformer;
use App\Support\Transformers\FinanceTransformer;
use Illuminate\Http\Request;

class StudentFeeController extends Controller
{
    public function index(Request $request)
    {
        $termsQuery = Term::query()->orderByDesc('is_active')->orderBy('name');
        if ($request->filled('term')) {
            $termsQuery->where('id', $request->query('term'));
        }
        $terms = $termsQuery->get();
        if ($terms->isEmpty()) {
            return ApiResponse::paginated([], 0, 1, 20);
        }

        $search = (string) $request->query('search', '');
        $flag = (string) $request->query('flag', '');
        $studentsQuery = Profile::query()->where('type', 'student')->where('is_active', true);
        if ($search !== '') {
            $studentsQuery->where(function ($builder) use ($search): void {
                $builder->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('id_number', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        $results = [];
        foreach ($studentsQuery->orderBy('last_name')->orderBy('first_name')->get() as $student) {
            $totalOwed = 0.0;
            $totalPaid = 0.0;
            foreach ($terms as $term) {
                $totalOwed += (float) FeeCalculator::studentFeeTotal($student, $term);
                $totalPaid += (float) FeeCalculator::studentPaidTotal($student, $term);
            }

            $balance = $totalOwed - $totalPaid;
            if ($totalOwed == 0.0) {
                $status = 'no-fees';
            } elseif ($totalPaid == 0.0) {
                $status = 'unpaid';
            } elseif ($totalPaid >= $totalOwed) {
                $status = 'paid';
            } else {
                $status = 'partial';
            }
            if ($flag !== '' && $flag !== $status) {
                continue;
            }

            $results[] = [
                'id' => $student->id,
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'id_number' => $student->id_number,
                'total_owed' => number_format($totalOwed, 2, '.', ''),
                'total_paid' => number_format($totalPaid, 2, '.', ''),
                'balance' => number_format($balance, 2, '.', ''),
                'status' => $status,
            ];
        }

        [$paged, $total, $page, $pageSize] = ManualPaginator::fromItems($results, $request, 20);
        return ApiResponse::paginated($paged, $total, $page, $pageSize);
    }

    public function show(Request $request, int $pk)
    {
        $student = Profile::query()->where('id', $pk)->where('type', 'student')->with('user')->first();
        if (! $student) {
            return ApiResponse::notFound('Student not found');
        }

        $termsQuery = Term::query()->orderByDesc('is_active')->orderBy('name');
        if ($request->filled('term')) {
            $termsQuery->where('id', $request->query('term'));
        }

        $termData = [];
        foreach ($termsQuery->get() as $term) {
            $totalOwed = (float) FeeCalculator::studentFeeTotal($student, $term);
            $totalPaid = (float) FeeCalculator::studentPaidTotal($student, $term);
            $balance = $totalOwed - $totalPaid;
            $gradeIds = $student->courseEnrollments()->where('is_active', true)->with('course:id,grade_id')->get()->pluck('course.grade_id')->filter()->unique();

            $structures = FeeStructure::query()->with(['feeType', 'grade', 'term'])
                ->whereIn('grade_id', $gradeIds)
                ->where('term_id', $term->id)
                ->where('is_active', true)
                ->get();
            $specials = SpecialFee::query()->with(['student.user', 'term'])
                ->where('student_id', $student->id)
                ->where('term_id', $term->id)
                ->where('is_active', true)
                ->get();
            $payments = Payment::query()->with(['student.user', 'term', 'feeType', 'createdBy.profile'])
                ->where('student_id', $student->id)
                ->where('term_id', $term->id)
                ->orderByDesc('payment_date')
                ->get();
            $plans = PaymentPlan::query()->with(['student.user', 'term', 'feeType', 'planInstallments.payment'])
                ->where('student_id', $student->id)
                ->where('term_id', $term->id)
                ->get();

            if ($totalOwed == 0.0) {
                $status = 'no-fees';
            } elseif ($totalPaid == 0.0) {
                $status = 'unpaid';
            } elseif ($totalPaid >= $totalOwed) {
                $status = 'paid';
            } else {
                $status = 'partial';
            }

            $termData[] = [
                'term' => ['id' => $term->id, 'name' => $term->name, 'is_active' => $term->is_active],
                'total_owed' => number_format($totalOwed, 2, '.', ''),
                'total_paid' => number_format($totalPaid, 2, '.', ''),
                'balance' => number_format($balance, 2, '.', ''),
                'status' => $status,
                'fee_structures' => $structures->map(fn ($item) => FinanceTransformer::feeStructure($item))->all(),
                'special_fees' => $specials->map(fn ($item) => FinanceTransformer::specialFee($item))->all(),
                'payments' => $payments->map(fn ($item) => FinanceTransformer::payment($item))->all(),
                'payment_plans' => $plans->map(fn ($item) => FinanceTransformer::paymentPlan($item))->all(),
            ];
        }

        return ApiResponse::ok([
            'student' => CoreTransformer::profile($student),
            'terms' => $termData,
        ]);
    }
}
