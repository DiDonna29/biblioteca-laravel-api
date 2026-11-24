<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BookController extends Controller
{
    /**
     * Listar todos los libros almacenados o filtrarlos por categoría.
     *
     * Endpoint: GET /api/books
     * Endpoint con filtro: GET /api/books?category={category_id}
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Book::with('category');

        // Filtrado por categoría si se especifica el parámetro query 'category'
        if ($request->filled('category')) {
            $query->where('category_id', $request->query('category'));
        }

        $books = $query->latest('id')->get();

        return BookResource::collection($books);
    }

    /**
     * Crear un nuevo libro en el sistema.
     *
     * Endpoint: POST /api/books
     */
    public function store(StoreBookRequest $request): JsonResponse
    {
        $book = Book::create($request->validated());

        return (new BookResource($book->load('category')))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Mostrar la información de un libro específico.
     *
     * Endpoint: GET /api/books/{id}
     */
    public function show(string|int $id): JsonResponse|BookResource
    {
        $book = Book::with('category')->find($id);

        if (!$book) {
            return response()->json([
                'message' => "El libro con ID {$id} no fue encontrado.",
            ], 404);
        }

        return new BookResource($book);
    }

    /**
     * Actualizar/Editar la información de un libro existente.
     *
     * Endpoint: PUT /api/books/{id}
     */
    public function update(UpdateBookRequest $request, string|int $id): JsonResponse|BookResource
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json([
                'message' => "El libro con ID {$id} que intentas editar no existe.",
            ], 404);
        }

        $book->update($request->validated());

        return (new BookResource($book->fresh('category')))
            ->additional([
                'message' => 'Libro actualizado exitosamente.',
            ])
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Eliminar un libro específico.
     *
     * Regla de negocio: Verificar la existencia del libro antes de proceder con su eliminación.
     * Endpoint: DELETE /api/books/{id}
     */
    public function destroy(string|int $id): JsonResponse
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json([
                'message' => "El libro con ID {$id} que intentas eliminar no existe.",
            ], 404);
        }

        $book->delete();

        return response()->json([
            'message' => 'Libro eliminado exitosamente.',
        ], 200);
    }
}
