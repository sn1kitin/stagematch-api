<?php
namespace App\Http\Controllers;

use App\Http\ApiResponse;
use App\Http\Auth;
use App\Models\ApplicationModel;
use App\Models\StudentSkillModel;
use App\Models\VacancyModel;
use App\Services\Matcher;

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

        if (ApplicationModel::findFor($vacancyId, $student['id']) !== null) {
            ApiResponse::error(ApiResponse::CONFLICT, 'Je hebt al op deze vacature gesolliciteerd.');
        }

        $application = ApplicationModel::create([
            'vacancy_id' => $vacancyId,
            'student_id' => $student['id'],
            'motivation' => $data['motivation'] ?? null,
        ]);

        header('Location: ' . ApiResponse::url('applications/' . $application['id']));
        ApiResponse::send($application, ApiResponse::CREATED);
    }

    // GET /applications/{id} -> alleen de student zelf of het bedrijf van de vacature
    public function show(int $id): void
    {
        $user = Auth::requireUser();
        [$application, $vacancy] = $this->findOrFail($id);

        if ($application['student_id'] !== $user['id'] && $vacancy['company_id'] !== $user['id']) {
            ApiResponse::error(ApiResponse::FORBIDDEN, 'Dit is niet jouw sollicitatie.');
        }

        $application['vacancy'] = VacancyModel::withDetails($vacancy);

        ApiResponse::send($application);
    }

    // GET /me/applications -> student: eigen sollicitaties, bedrijf: alle op eigen vacatures
    public function mine(): void
    {
        $user = Auth::requireUser();

        if ($user['role'] === 'student') {
            ApiResponse::send(ApplicationModel::forStudent($user['id']));
        }

        ApiResponse::send(ApplicationModel::forCompany($user['id']));
    }

    // GET /vacancies/{id}/applications -> met hoe goed elke student past
    public function forVacancy(int $vacancyId): void
    {
        $vacancy = VacancyModel::find($vacancyId);

        if ($vacancy === null) {
            ApiResponse::error(ApiResponse::NOT_FOUND, "Vacature {$vacancyId} bestaat niet.");
        }

        Auth::requireOwner($vacancy['company_id']);

        $vacancySkills = VacancyModel::skills($vacancyId);
        $applications = [];

        foreach (ApplicationModel::forVacancy($vacancyId) as $application) {
            $skillIds = array_column(StudentSkillModel::forStudent($application['student_id']), 'id');
            $applications[] = array_merge($application, Matcher::match($vacancySkills, $skillIds));
        }

        ApiResponse::send($applications);
    }

    // PATCH /applications/{id} -> bedrijf zet de status
    public function updateStatus(int $id, array $data): void
    {
        [$application, $vacancy] = $this->findOrFail($id);

        Auth::requireOwner($vacancy['company_id']);

        $this->validate($data, [
            'status' => 'required|in:pending,accepted,rejected',
        ]);

        ApiResponse::send(ApplicationModel::update($id, ['status' => $data['status']]));
    }

    private function findOrFail(int $id): array
    {
        $application = ApplicationModel::find($id);

        if ($application === null) {
            ApiResponse::error(ApiResponse::NOT_FOUND, "Sollicitatie {$id} bestaat niet.");
        }

        return [$application, VacancyModel::find($application['vacancy_id'])];
    }
}
