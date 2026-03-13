<?php

namespace App\Support\Finance;

use App\Models\FeeStructure;
use App\Models\Payment;
use App\Models\Profile;
use App\Models\SpecialFee;
use App\Models\Term;

class FeeCalculator
{
    public static function studentFeeTotal(Profile $student, Term $term): string
    {
        $grades = $student->courseEnrollments()
            ->where('is_active', true)
            ->with('course:id,grade_id')
            ->get()
            ->pluck('course.grade_id')
            ->filter()
            ->unique()
            ->values();

        $structureTotal = (float) FeeStructure::query()
            ->whereIn('grade_id', $grades)
            ->where('term_id', $term->id)
            ->where('is_active', true)
            ->sum('amount');

        $specialTotal = (float) SpecialFee::query()
            ->where('student_id', $student->id)
            ->where('term_id', $term->id)
            ->where('is_active', true)
            ->sum('amount');

        return number_format($structureTotal + $specialTotal, 2, '.', '');
    }

    public static function studentPaidTotal(Profile $student, Term $term): string
    {
        $paidTotal = (float) Payment::query()
            ->where('student_id', $student->id)
            ->where('term_id', $term->id)
            ->sum('amount');

        return number_format($paidTotal, 2, '.', '');
    }

    public static function subtract(string|float|int $left, string|float|int $right): string
    {
        return number_format((float) $left - (float) $right, 2, '.', '');
    }
}