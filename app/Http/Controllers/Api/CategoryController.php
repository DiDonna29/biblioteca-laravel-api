<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    /**
     * Listar todas las categorías disponibles.
     *
     * Endpoint: GET /api/categories
     */
    public function index(): AnonymousResourceCollection
    {
        $categories = Category::withCount('books')->orderBy('name')->get();

        return CategoryResource::collection($categories);
    }

    /**
     * Crear una nueva categoría.
     *
     * Endpoint: POST /api/categories
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = Category::create($request->validated());

        return (new CategoryResource($category))
            ->additional([
                'message' => 'Categoría creada exitosamente.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Mostrar la información de una categoría específica con sus libros.
     *
     * Endpoint: GET /api/categories/{id}
     */
    public function show(string|int $id): JsonResponse|CategoryResource
    {
        $category = Category::with('books')->find($id);

        if (!$category) {
            return response()->json([
                'message' => "La categoría con ID {$id} no fue encontrada.",
            ], 404);
        }

        return new CategoryResource($category);
    }

    /**
     * Actualizar una categoría existente.
     *
     * Endpoint: PUT /api/categories/{id}
     */
    public function update(UpdateCategoryRequest $request, string|int $id): JsonResponse|CategoryResource
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => "La categoría con ID {$id} que intentas editar no existe.",
            ], 404);
        }

        $category->update($request->validated());

        return (new CategoryResource($category))
            ->additional([
                'message' => 'Categoría actualizada exitosamente.',
            ])
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Eliminar una categoría.
     *
     * Regla de negocio: Verificar que una categoría no tenga libros asociados
     * antes de permitir su eliminación.
     * Endpoint: DELETE /api/categories/{id}
     */
    public function destroy(string|int $id): JsonResponse
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => "La categoría con ID {$id} que intentas eliminar no existe.",
            ], 404);
        }

        // Validación de regla de negocio: no eliminar si tiene libros asociados
        if ($category->books()->exists()) {
            return response()->json([
                'message' => 'No es posible eliminar la categoría porque tiene libros asociados en el sistema.',
                'error' => 'category_has_associated_books',
                'books_count' => $category->books()->count(),
            ], 409);
        }

        $category->delete();

        return response()->json([
            'message' => 'Categoría eliminada exitosamente.',
        ], 200);
    }
}
