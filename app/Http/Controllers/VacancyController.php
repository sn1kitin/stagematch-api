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

    public function index(): void
    {
        $vacancies = array_map([VacancyModel::class, 'withDetails'], VacancyModel::all());

        ApiResponse::send($vacancies);
    }

    public function show(int $id): void
    {
        ApiResponse::send(VacancyModel::withDetails($this->findOrFail($id)));
    }

    public function mine(): void
    {
        $company = Auth::requireRole('company');
        $vacancies = array_map([VacancyModel::class, 'withDetails'], VacancyModel::where('company_id', $company['id']));

        ApiResponse::send($vacancies);
    }

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

    public function update(int $id, array $data): void
    {
        $this->save($id, $data, false);
    }

    public function patch(int $id, array $data): void
    {
        $this->save($id, $data, true);
    }

    private function save(int $id, array $data, bool $partial): void
    {
        $vacancy = $this->findOrFail($id);
        Auth::requireOwner($vacancy['company_id']);

        unset($data['company_id']);

        $this->validate($data, self::RULES, $partial);

        if (isset($data['skills'])) {
            $this->validateSkills($data['skills']);
            VacancyModel::setSkills($id, $data['skills']);
        }

        if (!empty(array_intersect_key($data, array_flip(['title', 'description', 'location'])))) {
            $vacancy = VacancyModel::update($id, $data);
        }

        ApiResponse::send(VacancyModel::withDetails($vacancy));
    }

    public function destroy(int $id): void
    {
        $vacancy = $this->findOrFail($id);
        Auth::requireOwner($vacancy['company_id']);

        $vacancy = VacancyModel::withDetails($vacancy);
        VacancyModel::delete($id);

        ApiResponse::send($vacancy);
    }

    private function findOrFail(int $id): array
    {
        $vacancy = VacancyModel::find($id);

        if ($vacancy === null) {
            ApiResponse::error(ApiResponse::NOT_FOUND, "Vacature {$id} bestaat niet.");
        }

        return $vacancy;
    }

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
