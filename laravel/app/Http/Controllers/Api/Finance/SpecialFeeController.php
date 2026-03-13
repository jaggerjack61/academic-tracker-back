<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Models\SpecialFee;
use App\Support\Api\ApiResponse;
use App\Support\Api\ManualPaginator;
use App\Support\Transformers\FinanceTransformer;
use Illuminate\Http\Request;

class SpecialFeeController extends Controller
{
    public function index(Request $request)
    {
        $query = SpecialFee::query()->with(['student.user', 'term']);
        $search = (string) $request->query('search', '');
        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($studentQuery) use ($search): void {
                        $studentQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
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
            $items->map(fn ($item) => FinanceTransformer::specialFee($item))->all(),
            $total,
            $page,
            $pageSize,
        );
    }

    public function store(Request $request)
    {
        $studentId = $request->input('student');
        $termId = $request->input('term');
        $name = trim((string) $request->input('name', ''));
        $amount = $request->input('amount');
        if (! $studentId || ! $termId || $name === '' || $amount === null || $amount === '') {
            return ApiResponse::error('All fields are required');
        }

        $item = SpecialFee::query()->create([
            'student_id' => $studentId,
            'term_id' => $termId,
            'name' => $name,
            'amount' => $amount,
            'description' => (string) $request->input('description', ''),
            'is_active' => true,
        ]);
        $item->load(['student.user', 'term']);

        return ApiResponse::created(FinanceTransformer::specialFee($item));
    }

    public function toggle(int $pk)
    {
        $item = SpecialFee::query()->with(['student.user', 'term'])->find($pk);
        if (! $item) {
            return ApiResponse::notFound();
        }

        $item->update(['is_active' => ! $item->is_active]);

        return ApiResponse::ok(FinanceTransformer::specialFee($item));
    }
}
