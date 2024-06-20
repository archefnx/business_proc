<?php

use App\Http\Controllers\ProcessController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Регистрация маршрута для получения всех процессов
Route::get('/processes', [ProcessController::class, 'index']);
// Маршрут для создания нового процесса
Route::post('/processes', [ProcessController::class, 'store']);

// Маршрут для получения детальной информации о процессе по ID
Route::get('/processes/{id}', [ProcessController::class, 'show']);

// Маршрут для обновления процесса по ID
Route::put('/processes/{id}', [ProcessController::class, 'update']);
Route::patch('/processes/{id}', [ProcessController::class, 'update']); // PATCH если необходимо обновлять частично

// Маршрут для удаления процесса по ID
Route::delete('/processes/{id}', [ProcessController::class, 'destroy']);
