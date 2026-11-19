<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\VacancyController;

return [
    'GET' => [
        '' => [VacancyController::class, 'index'],
        'skills' => [SkillController::class, 'index'],
        'skills/{id}' => [SkillController::class, 'show'],
        'vacancies' => [VacancyController::class, 'index'],
        'vacancies/{id}' => [VacancyController::class, 'show'],
    ],
    'POST' => [
        'register' => [AuthController::class, 'register'],
        'login' => [AuthController::class, 'login'],
        'logout' => [AuthController::class, 'logout'],
    ],
];
