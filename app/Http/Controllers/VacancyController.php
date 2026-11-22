<?php
namespace App\Http\Controllers;

use App\Http\ApiResponse;
use App\Http\Auth;
use App\Models\SkillModel;
use App\Models\VacancyModel;

class VacancyController extends Controller
{
    private const RULES = [
        'title' => 'required|string|max:100',
        'description' => 'required|string',
        'location' => 'required|string|max:100',
        'skills' => 'required|array',
    ];

    // GET /vacancies
    public function index(): void
    {
        $vacancies = array_map([VacancyModel::class, 'withDetails'], VacancyModel::all());

        ApiResponse::send($vacancies);
    }

    // GET /vacancies/{id}
    public function show(int $id): void
    {
        $vacancy = VacancyModel::find($id);

        if ($vacancy === null) {
            ApiResponse::error(ApiResponse::NOT_FOUND, "Vacature {$id} bestaat niet.");
        }

        ApiResponse::send(VacancyModel::withDetails($vacancy));
    }

    // POST /vacancies
    public function create(array $data): void
    {
        $company = Auth::requireRole('company');

        $this->validate($data, self::RULES);
        $this->validateSkills($data['skills']);

        $data['company_id'] = $company['id'];
        $vacancy = VacancyModel::create($data);
        VacancyModel::setSkills($vacancy['id'], $data['skills']);

        header('Location: ' . ApiResponse::url('vacancies/' . $vacancy['id']));
        ApiResponse::send(VacancyModel::withDetails($vacancy), ApiResponse::CREATED);
    }

    // elke skill moet een geldig id zijn dat in de skills tabel staat
    private function validateSkills(array $skills): void
    {
        $wrong = [];

        foreach ($skills as $skillId) {
            if (filter_var($skillId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false || SkillModel::find((int) $skillId) === null) {
                $wrong[] = $skillId;
            }
        }

        if (!empty($wrong)) {
            ApiResponse::error(ApiResponse::UNPROCESSABLE, 'De invoer is niet geldig.', [
                'skills' => 'Deze skills bestaan niet: ' . implode(', ', $wrong) . '.',
            ]);
        }
    }
}
