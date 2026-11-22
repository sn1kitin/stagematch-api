<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\StudentSkillController;
use App\Http\Controllers\VacancyController;

return [
    'GET' => [
        '' => [VacancyController::class, 'index'],
        'skills' => [SkillController::class, 'index'],
        'skills/{id}' => [SkillController::class, 'show'],
        'students/{id}/skills' => [StudentSkillController::class, 'forStudent'],
        'vacancies' => [VacancyController::class, 'index'],
        'vacancies/{id}' => [VacancyController::class, 'show'],
        'me' => [AuthController::class, 'me'],
        'me/skills' => [StudentSkillController::class, 'index'],
    ],
    'POST' => [
        'register' => [AuthController::class, 'register'],
        'login' => [AuthController::class, 'login'],
        'logout' => [AuthController::class, 'logout'],
        'me/skills' => [StudentSkillController::class, 'store'],
        'vacancies' => [VacancyController::class, 'create'],
    ],
    'DELETE' => [
        'me/skills/{id}' => [StudentSkillController::class, 'destroy'],
    ],
];
