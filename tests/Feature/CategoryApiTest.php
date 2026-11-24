<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_all_categories(): void
    {
        Category::factory()->count(3)->create();

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'books_count', 'created_at', 'updated_at'],
                ],
            ]);
    }

    public function test_can_create_a_category(): void
    {
        $payload = [
            'name' => 'Ciencia Ficción',
        ];

        $response = $this->postJson('/api/categories', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Ciencia Ficción');

        $this->assertDatabaseHas('categories', [
            'name' => 'Ciencia Ficción',
        ]);
    }

    public function test_cannot_create_category_with_duplicate_name(): void
    {
        Category::factory()->create(['name' => 'Historia']);

        $response = $this->postJson('/api/categories', [
            'name' => 'Historia',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_cannot_create_category_without_name(): void
    {
        $response = $this->postJson('/api/categories', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_can_show_category(): void
    {
        $category = Category::factory()->create(['name' => 'Filosofía']);

        $response = $this->getJson("/api/categories/{$category->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $category->id)
            ->assertJsonPath('data.name', 'Filosofía');
    }

    public function test_can_update_category(): void
    {
        $category = Category::factory()->create(['name' => 'Arte']);

        $response = $this->putJson("/api/categories/{$category->id}", [
            'name' => 'Bellas Artes',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Bellas Artes');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Bellas Artes',
        ]);
    }

    public function test_cannot_delete_category_with_associated_books(): void
    {
        // Regla de negocio: Verificar que una categoría no tenga libros asociados antes de permitir su eliminación
        $category = Category::factory()->create(['name' => 'Tecnología']);
        Book::factory()->create([
            'category_id' => $category->id,
            'title' => 'Refactoring',
            'author' => 'Martin Fowler',
        ]);

        $response = $this->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(409)
            ->assertJson([
                'error' => 'category_has_associated_books',
            ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_can_delete_category_without_associated_books(): void
    {
        $category = Category::factory()->create(['name' => 'Vacía']);

        $response = $this->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Categoría eliminada exitosamente.',
            ]);

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_returns_404_when_deleting_nonexistent_category(): void
    {
        $response = $this->deleteJson('/api/categories/99999');

        $response->assertStatus(404);
    }
}
