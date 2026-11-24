<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MatchController;
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
        'vacancies/{id}/applications' => [ApplicationController::class, 'forVacancy'],
        'applications/{id}' => [ApplicationController::class, 'show'],
        'matches' => [MatchController::class, 'index'],
        'me' => [AuthController::class, 'me'],
        'me/skills' => [StudentSkillController::class, 'index'],
        'me/vacancies' => [VacancyController::class, 'mine'],
        'me/applications' => [ApplicationController::class, 'mine'],
    ],
    'POST' => [
        'register' => [AuthController::class, 'register'],
        'login' => [AuthController::class, 'login'],
        'logout' => [AuthController::class, 'logout'],
        'me/skills' => [StudentSkillController::class, 'store'],
        'vacancies' => [VacancyController::class, 'create'],
        'vacancies/{id}/applications' => [ApplicationController::class, 'create'],
    ],
    'PUT' => [
        'vacancies/{id}' => [VacancyController::class, 'update'],
    ],
    'PATCH' => [
        'vacancies/{id}' => [VacancyController::class, 'patch'],
        'applications/{id}' => [ApplicationController::class, 'updateStatus'],
    ],
    'DELETE' => [
        'me/skills/{id}' => [StudentSkillController::class, 'destroy'],
        'vacancies/{id}' => [VacancyController::class, 'destroy'],
    ],
];
