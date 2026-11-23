<?php
namespace App\Http\Controllers;

use App\Http\ApiResponse;
use App\Http\Auth;
use App\Models\StudentSkillModel;
use App\Models\VacancyModel;

class MatchController extends Controller
{
    // GET /matches -> alle vacatures, beste match bovenaan
    public function index(): void
    {
        $student = Auth::requireRole('student');
        $skillIds = array_column(StudentSkillModel::forStudent($student['id']), 'id');

        $matches = [];
        foreach (VacancyModel::all() as $vacancy) {
            $vacancy = VacancyModel::withDetails($vacancy);

            $matched = [];
            $missing = [];
            foreach ($vacancy['skills'] as $skill) {
                if (in_array($skill['id'], $skillIds)) {
                    $matched[] = $skill;
                } else {
                    $missing[] = $skill;
                }
            }

            // geen gevraagde skills = iedereen past
            $total = count($vacancy['skills']);
            $vacancy['match_percentage'] = $total === 0 ? 100 : (int) round(count($matched) / $total * 100);
            $vacancy['matched_skills'] = $matched;
            $vacancy['missing_skills'] = $missing;

            $matches[] = $vacancy;
        }

        // bij gelijk percentage de vacature met minder ontbrekende skills eerst
        usort($matches, function ($a, $b) {
            return [$b['match_percentage'], count($a['missing_skills'])] <=> [$a['match_percentage'], count($b['missing_skills'])];
        });

        ApiResponse::send($matches);
    }
}
