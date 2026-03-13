<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Models\FeeStructure;
use App\Support\Api\ApiResponse;
use App\Support\Transformers\FinanceTransformer;
use Illuminate\Http\Request;

class FeeStructureController extends Controller
{
    public function index(Request $request)
    {
        $query = FeeStructure::query()->with(['feeType', 'grade', 'term']);
        if ($request->filled('term')) {
            $query->where('term_id', $request->query('term'));
        }
        if ($request->filled('grade')) {
            $query->where('grade_id', $request->query('grade'));
        }

        $items = $query->get()->sortBy([
            fn ($item) => $item->term?->name,
            fn ($item) => $item->grade?->name,
            fn ($item) => $item->feeType?->name,
        ])->values();

        return ApiResponse::ok($items->map(fn ($item) => FinanceTransformer::feeStructure($item))->all());
    }

    public function store(Request $request)
    {
        $feeTypeId = $request->input('fee_type');
        $gradeIds = (array) $request->input('grades', []);
        $gradeId = $request->input('grade');
        $termId = $request->input('term');
        $amount = $request->input('amount');
        if ($gradeId && $gradeIds === []) {
            $gradeIds = [$gradeId];
        }
        if (! $feeTypeId || ! $gradeIds || ! $termId || $amount === null || $amount === '') {
            return ApiResponse::error('All fields are required');
        }

        $created = [];
        $skipped = [];
        foreach ($gradeIds as $selectedGradeId) {
            $exists = FeeStructure::query()
                ->where('fee_type_id', $feeTypeId)
                ->where('grade_id', $selectedGradeId)
                ->where('term_id', $termId)
                ->exists();
            if ($exists) {
                $skipped[] = $selectedGradeId;
                continue;
            }

            $created[] = FeeStructure::query()->create([
                'fee_type_id' => $feeTypeId,
                'grade_id' => $selectedGradeId,
                'term_id' => $termId,
                'amount' => $amount,
                'is_active' => true,
            ]);
        }

        if ($created === [] && $skipped !== []) {
            return ApiResponse::error('All selected fee structures already exist');
        }

        return ApiResponse::created(array_map(fn ($item) => FinanceTransformer::feeStructure($item), $created));
    }

    public function update(Request $request, int $pk)
    {
        $item = FeeStructure::query()->find($pk);
        if (! $item) {
            return ApiResponse::notFound();
        }

        $item->update([
            'amount' => $request->input('amount', $item->amount),
            'fee_type_id' => $request->input('fee_type', $item->fee_type_id),
            'grade_id' => $request->input('grade', $item->grade_id),
            'term_id' => $request->input('term', $item->term_id),
        ]);
        $item->load(['feeType', 'grade', 'term']);

        return ApiResponse::ok(FinanceTransformer::feeStructure($item));
    }

    public function toggle(int $pk)
    {
        $item = FeeStructure::query()->with(['feeType', 'grade', 'term'])->find($pk);
        if (! $item) {
            return ApiResponse::notFound();
        }

        $item->update(['is_active' => ! $item->is_active]);

        return ApiResponse::ok(FinanceTransformer::feeStructure($item));
    }
}
