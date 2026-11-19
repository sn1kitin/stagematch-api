<?php
namespace App\Http\Controllers;

use App\Http\ApiResponse;
use App\Models\VacancyModel;

class VacancyController extends Controller
{
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
}
