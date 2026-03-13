<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Models\PaymentLog;
use App\Support\Api\ApiResponse;
use App\Support\Api\ManualPaginator;
use App\Support\Transformers\FinanceTransformer;
use Illuminate\Http\Request;

class PaymentLogController extends Controller
{
    public function index(Request $request)
    {
        $query = PaymentLog::query()->with('actor.profile')->orderByDesc('created_at')->orderByDesc('id');
        $search = trim((string) $request->query('search', ''));
        $action = trim((string) $request->query('action', ''));
        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('student_name', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%")
                    ->orWhere('actor_email', 'like', "%{$search}%")
                    ->orWhere('fee_type_name', 'like', "%{$search}%");
            });
        }
        if ($request->filled('term')) {
            $query->where('term_id_ref', $request->query('term'));
        }
        if (in_array($action, ['create', 'update', 'delete'], true)) {
            $query->where('action', $action);
        }

        [$items, $total, $page, $pageSize] = ManualPaginator::fromQuery($query, $request, 20);

        return ApiResponse::paginated(
            $items->map(fn ($item) => FinanceTransformer::paymentLog($item))->all(),
            $total,
            $page,
            $pageSize,
        );
    }
}
