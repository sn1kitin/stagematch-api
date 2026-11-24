<?php
namespace App\Http\Controllers;

use App\Http\ApiResponse;
use App\Http\Auth;
use App\Models\StudentSkillModel;
use App\Models\VacancyModel;
use App\Services\Matcher;

class MatchController extends Controller
{
    public function index(): void
    {
        $student = Auth::requireRole('student');
        $skillIds = array_column(StudentSkillModel::forStudent($student['id']), 'id');

        $matches = [];
        foreach (VacancyModel::all() as $vacancy) {
            $vacancy = VacancyModel::withDetails($vacancy);
            $matches[] = array_merge($vacancy, Matcher::match($vacancy['skills'], $skillIds));
        }

        usort($matches, function ($a, $b) {
            return [$b['match_percentage'], count($a['missing_skills'])] <=> [$a['match_percentage'], count($b['missing_skills'])];
        });

        ApiResponse::send($matches);
    }
}
