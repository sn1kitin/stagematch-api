<?php
namespace App\Http\Controllers;

use App\Http\ApiResponse;
use App\Http\Auth;
use App\Models\ApplicationModel;
use App\Models\VacancyModel;

class ApplicationController extends Controller
{
    // POST /vacancies/{id}/applications
    public function create(int $vacancyId, array $data): void
    {
        $student = Auth::requireRole('student');

        if (VacancyModel::find($vacancyId) === null) {
            ApiResponse::error(ApiResponse::NOT_FOUND, "Vacature {$vacancyId} bestaat niet.");
        }

        $this->validate($data, [
            'motivation' => 'string|max:1000',
        ]);

        $application = ApplicationModel::create([
            'vacancy_id' => $vacancyId,
            'student_id' => $student['id'],
            'motivation' => $data['motivation'] ?? null,
        ]);

        ApiResponse::send($application, ApiResponse::CREATED);
    }

    // GET /me/applications -> eigen sollicitaties met de status
    public function mine(): void
    {
        $student = Auth::requireRole('student');

        ApiResponse::send(ApplicationModel::forStudent($student['id']));
    }
}
