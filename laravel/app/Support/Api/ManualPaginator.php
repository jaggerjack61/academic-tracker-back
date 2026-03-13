<?php

namespace App\Support\Api;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ManualPaginator
{
    public static function fromQuery(Builder $query, Request $request, int $defaultPageSize): array
    {
        $page = max((int) $request->query('page', 1), 1);
        $pageSize = max((int) $request->query('page_size', $defaultPageSize), 1);
        $total = (clone $query)->count();
        $items = $query->forPage($page, $pageSize)->get();

        return [$items, $total, $page, $pageSize];
    }

    public static function fromItems(array|Collection $items, Request $request, int $defaultPageSize): array
    {
        $page = max((int) $request->query('page', 1), 1);
        $pageSize = max((int) $request->query('page_size', $defaultPageSize), 1);
        $collection = $items instanceof Collection ? $items->values() : collect($items)->values();
        $total = $collection->count();

        return [
            $collection->slice(($page - 1) * $pageSize, $pageSize)->values()->all(),
            $total,
            $page,
            $pageSize,
        ];
    }
}