<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Models\FeeType;
use App\Support\Api\ApiResponse;
use App\Support\Transformers\FinanceTransformer;
use Illuminate\Http\Request;

class FeeTypeController extends Controller
{
    public function index()
    {
        return ApiResponse::ok(FeeType::query()->orderBy('name')->get()->map(fn ($item) => FinanceTransformer::feeType($item))->all());
    }

    public function store(Request $request)
    {
        $name = trim((string) $request->input('name', ''));
        if ($name === '') {
            return ApiResponse::error('Name is required');
        }

        $item = FeeType::query()->create([
            'name' => $name,
            'description' => (string) $request->input('description', ''),
        ]);

        return ApiResponse::created(FinanceTransformer::feeType($item));
    }

    public function update(Request $request, int $pk)
    {
        $item = FeeType::query()->find($pk);
        if (! $item) {
            return ApiResponse::notFound();
        }

        $item->update([
            'name' => (string) $request->input('name', $item->name),
            'description' => (string) $request->input('description', $item->description),
        ]);

        return ApiResponse::ok(FinanceTransformer::feeType($item));
    }

    public function toggle(int $pk)
    {
        $item = FeeType::query()->find($pk);
        if (! $item) {
            return ApiResponse::notFound();
        }

        $item->update(['is_active' => ! $item->is_active]);

        return ApiResponse::ok(FinanceTransformer::feeType($item));
    }
}
