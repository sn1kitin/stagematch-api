<?php
namespace App\Http\Controllers;

use App\Http\ApiResponse;
use App\Models\SkillModel;

class SkillController extends Controller
{
    // GET /skills
    public function index(): void
    {
        ApiResponse::send(SkillModel::all());
    }

    // GET /skills/{id}
    public function show(int $id): void
    {
        $skill = SkillModel::find($id);

        if ($skill === null) {
            ApiResponse::error(ApiResponse::NOT_FOUND, "Skill {$id} bestaat niet.");
        }

        ApiResponse::send($skill);
    }
}
