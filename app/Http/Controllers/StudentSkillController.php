<?php
namespace App\Http\Controllers;

use App\Http\ApiResponse;
use App\Http\Auth;
use App\Models\SkillModel;
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

        $skillId = (int) $data['skill_id'];

        if (StudentSkillModel::has($student['id'], $skillId)) {
            ApiResponse::error(ApiResponse::CONFLICT, 'Je hebt deze skill al.');
        }

        StudentSkillModel::add($student['id'], $skillId);

        ApiResponse::send(StudentSkillModel::forStudent($student['id']), ApiResponse::CREATED);
    }

    // DELETE /me/skills/{id}
    public function destroy(int $skillId): void
    {
        $student = Auth::requireRole('student');

        if (!StudentSkillModel::has($student['id'], $skillId)) {
            ApiResponse::error(ApiResponse::NOT_FOUND, "Je hebt skill {$skillId} niet.");
        }

        $skill = SkillModel::find($skillId);
        StudentSkillModel::remove($student['id'], $skillId);

        ApiResponse::send($skill);
    }
}
