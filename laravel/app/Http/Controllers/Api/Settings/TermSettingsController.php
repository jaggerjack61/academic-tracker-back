<?php

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
use App\Models\Term;
use App\Support\Api\ApiResponse;
use App\Support\Transformers\CoreTransformer;
use Illuminate\Http\Request;

class TermSettingsController extends Controller
{
    public function index()
    {
        return ApiResponse::ok(Term::query()->latest()->get()->map(fn ($term) => CoreTransformer::term($term))->all());
    }

    public function store(Request $request)
    {
        $term = Term::query()->create([
            'name' => (string) $request->input('name', ''),
            'start' => (string) $request->input('start', ''),
            'end' => (string) $request->input('end', ''),
        ]);

        return ApiResponse::created(['message' => 'Term created', 'id' => $term->id]);
    }

    public function update(Request $request, int $pk)
    {
        $term = Term::query()->find($pk);
        if (! $term) {
            return ApiResponse::notFound();
        }

        $term->update([
            'name' => (string) $request->input('name', $term->name),
            'start' => (string) $request->input('start', $term->start),
            'end' => (string) $request->input('end', $term->end),
        ]);

        return ApiResponse::message('Term updated');
    }

    public function toggle(int $pk)
    {
        $term = Term::query()->find($pk);
        if (! $term) {
            return ApiResponse::notFound();
        }

        $term->update(['is_active' => ! $term->is_active]);
        return ApiResponse::message('Term status updated', ['is_active' => $term->is_active]);
    }
}
