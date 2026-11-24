<?php

use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\CategoryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Biblioteca API
|--------------------------------------------------------------------------
|
| Endpoints para la gestión de Libros y Categorías con sus respectivas
| validaciones, relaciones Eloquent y reglas de negocio.
|
*/

// Endpoints de Libros (CRUD y filtrado por categoría)
Route::apiResource('books', BookController::class);

// Endpoints de Categorías (CRUD y verificación de integridad)
Route::apiResource('categories', CategoryController::class);
