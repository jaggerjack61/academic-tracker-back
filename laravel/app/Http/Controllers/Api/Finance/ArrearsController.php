<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\Term;
use App\Support\Api\ApiResponse;
use App\Support\Api\ManualPaginator;
use App\Support\Finance\FeeCalculator;
use Illuminate\Http\Request;

class ArrearsController extends Controller
{
    public function index(Request $request)
    {
        $termsQuery = Term::query()->orderByDesc('is_active')->orderBy('name');
        if ($request->filled('term')) {
            $termsQuery->where('id', $request->query('term'));
        }
        $terms = $termsQuery->get();

        $search = (string) $request->query('search', '');
        $studentsQuery = Profile::query()->where('type', 'student')->where('is_active', true);
        if ($search !== '') {
            $studentsQuery->where(function ($builder) use ($search): void {
                $builder->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('id_number', 'like', "%{$search}%");
            });
        }

        $results = [];
        foreach ($studentsQuery->orderBy('last_name')->orderBy('first_name')->get() as $student) {
            $studentArrears = [];
            $totalBalance = 0.0;
            foreach ($terms as $term) {
                $owed = (float) FeeCalculator::studentFeeTotal($student, $term);
                $paid = (float) FeeCalculator::studentPaidTotal($student, $term);
                $balance = $owed - $paid;
                if ($balance > 0) {
                    $studentArrears[] = [
                        'term_id' => $term->id,
                        'term_name' => $term->name,
                        'owed' => number_format($owed, 2, '.', ''),
                        'paid' => number_format($paid, 2, '.', ''),
                        'balance' => number_format($balance, 2, '.', ''),
                    ];
                    $totalBalance += $balance;
                }
            }

            if ($studentArrears !== []) {
                $results[] = [
                    'id' => $student->id,
                    'first_name' => $student->first_name,
                    'last_name' => $student->last_name,
                    'id_number' => $student->id_number,
                    'total_balance' => number_format($totalBalance, 2, '.', ''),
                    'terms' => $studentArrears,
                ];
            }
        }

        [$paged, $total, $page, $pageSize] = ManualPaginator::fromItems($results, $request, 20);

        return ApiResponse::paginated($paged, $total, $page, $pageSize);
    }
}
