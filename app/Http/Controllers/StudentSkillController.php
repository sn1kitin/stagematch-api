<?php
namespace App\Http\Controllers;

use App\Http\ApiResponse;
use App\Http\Auth;
use App\Models\StudentSkillModel;
use App\Models\UserModel;

class StudentSkillController extends Controller
{
    // GET /students/{id}/skills
    public function forStudent(int $id): void
    {
        $student = UserModel::find($id);

        if ($student === null || $student['role'] !== 'student') {
            ApiResponse::error(ApiResponse::NOT_FOUND, "Student {$id} bestaat niet.");
        }

        $student['skills'] = StudentSkillModel::forStudent($id);

        ApiResponse::send($student);
    }

    // GET /me/skills
    public function index(): void
    {
        $student = Auth::requireRole('student');

        ApiResponse::send(StudentSkillModel::forStudent($student['id']));
    }

    // POST /me/skills
    public function store(array $data): void
    {
        $student = Auth::requireRole('student');

        $this->validate($data, [
            'skill_id' => 'required|int|exists:App\Models\SkillModel',
        ]);

        StudentSkillModel::add($student['id'], (int) $data['skill_id']);

        ApiResponse::send(StudentSkillModel::forStudent($student['id']), ApiResponse::CREATED);
    }
}
