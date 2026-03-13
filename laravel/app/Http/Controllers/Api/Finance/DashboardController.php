<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Profile;
use App\Models\Term;
use App\Support\Api\ApiResponse;
use App\Support\Finance\FeeCalculator;
use App\Support\Transformers\FinanceTransformer;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function show(Request $request)
    {
        $allTerms = Term::query()->orderByDesc('is_active')->orderBy('name')->get();
        $termId = $request->query('term');

        $selectedTerm = $termId
            ? $allTerms->firstWhere('id', (int) $termId)
            : null;

        $scopedTerms = $selectedTerm
            ? collect([$selectedTerm])
            : $allTerms;

        if ($scopedTerms->isEmpty()) {
            return ApiResponse::ok([
                'term' => null,
                'terms' => [],
                'total_collected' => '0',
                'total_outstanding' => '0',
                'student_count' => 0,
                'paid_count' => 0,
                'partial_count' => 0,
                'unpaid_count' => 0,
                'recent_payments' => [],
            ]);
        }

        $scopedTermIds = $scopedTerms->pluck('id');

        $totalCollected = (float) Payment::query()
            ->whereIn('term_id', $scopedTermIds)
            ->sum('amount');

        $students = Profile::query()->where('type', 'student')->where('is_active', true)->get();
        $paidCount = 0;
        $partialCount = 0;
        $unpaidCount = 0;
        $totalOutstanding = 0.0;

        foreach ($students as $student) {
            $owed = 0.0;
            $paid = 0.0;
            foreach ($scopedTerms as $term) {
                $owed += (float) FeeCalculator::studentFeeTotal($student, $term);
                $paid += (float) FeeCalculator::studentPaidTotal($student, $term);
            }
            if ($owed == 0.0) {
                continue;
            }

            $balance = $owed - $paid;
            $totalOutstanding += max($balance, 0);
            if ($paid == 0.0) {
                $unpaidCount++;
            } elseif ($paid >= $owed) {
                $paidCount++;
            } else {
                $partialCount++;
            }
        }

        $recentPayments = Payment::query()
            ->whereIn('term_id', $scopedTermIds)
            ->with(['student.user', 'term', 'feeType', 'createdBy.profile'])
            ->latest()
            ->limit(10)
            ->get();

        return ApiResponse::ok([
            'term' => $selectedTerm ? ['id' => $selectedTerm->id, 'name' => $selectedTerm->name] : null,
            'terms' => $allTerms->map(fn ($t) => ['id' => $t->id, 'name' => $t->name, 'is_active' => (bool) $t->is_active])->values()->all(),
            'total_collected' => number_format($totalCollected, 2, '.', ''),
            'total_outstanding' => number_format($totalOutstanding, 2, '.', ''),
            'student_count' => $students->count(),
            'paid_count' => $paidCount,
            'partial_count' => $partialCount,
            'unpaid_count' => $unpaidCount,
            'recent_payments' => $recentPayments->map(fn ($item) => FinanceTransformer::payment($item))->all(),
        ]);
    }
}
