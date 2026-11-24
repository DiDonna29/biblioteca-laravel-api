<?php

use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\CategoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'name' => 'API de Gestión de Biblioteca (Laravel & PHP)',
        'version' => '1.0.0',
        'status' => 'online',
        'documentation' => [
            'books' => [
                'list_all' => 'GET /api/books (o /books)',
                'filter_by_category' => 'GET /api/books?category={id}',
                'create' => 'POST /api/books',
                'show' => 'GET /api/books/{id}',
                'update' => 'PUT /api/books/{id}',
                'delete' => 'DELETE /api/books/{id}',
            ],
            'categories' => [
                'list_all' => 'GET /api/categories (o /categories)',
                'create' => 'POST /api/categories',
                'show' => 'GET /api/categories/{id}',
                'update' => 'PUT /api/categories/{id}',
                'delete' => 'DELETE /api/categories/{id} (valida que no tenga libros asociados)',
            ],
        ],
    ]);
});

// Soporte de endpoints directos sin prefijo /api para compatibilidad exacta con el enunciado
Route::apiResource('books', BookController::class);
Route::apiResource('categories', CategoryController::class);
