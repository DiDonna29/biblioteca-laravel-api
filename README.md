# 📚 Sistema de Gestión de Biblioteca - API REST (Laravel & PHP)

API RESTful desarrollada con **PHP 8.3** y **Laravel** para la gestión integral de una biblioteca (libros y categorías), con soporte para operaciones CRUD completas, filtrado por categorías, validación de reglas de negocio, integridad referencial y suite de pruebas automatizadas.

---

## 🚀 Características Principales

* **Arquitectura Limpia (MVC):** Separación estricta de responsabilidades entre Modelos, Controladores, Recursos API y Form Requests.
* **ORM Eloquent & Relaciones:**
  * `Category` tiene muchos `Book` (`hasMany`).
  * `Book` pertenece a una `Category` (`belongsTo`).
  * Eager Loading (`with('category')`) para evitar el problema de consultas $N+1$.
* **Validaciones Robustas (Form Requests):**
  * Título obligatorio (mínimo 3 caracteres, máximo 255).
  * Autor obligatorio.
  * Año de publicación válido (entero entre año 1000 y año en curso).
  * Validación de existencia de la categoría (`exists:categories,id`).
  * Nombre de categoría requerido y único (`unique:categories,name`).
* **Lógica de Negocio e Integridad:**
  * **Verificación de existencia del libro:** Validación previa antes de eliminar, retornando `404 Not Found` descriptivo si el registro no existe.
  * **Protección de integridad referencial:** Restricción de eliminación de categorías con libros asociados (`409 Conflict`), previniendo registros huérfanos.
* **Compatibilidad de Rutas:** Soporte nativo para endpoints tanto bajo `/api/*` (estándar REST de Laravel) como bajo la raíz `/*` (según enunciado).
* **Base de Datos Sin Configuración Adicional:** Configurado con **SQLite** para permitir que cualquier evaluador clone, migre y ejecute la prueba en segundos sin requerir servidores externos de MySQL/PostgreSQL.

---

## 🛠️ Requisitos Previos

* **PHP:** >= 8.2 (con extensiones `pdo_sqlite`, `curl`, `mbstring`, `openssl`, `zip`)
* **Composer:** >= 2.0
* **Git**

---

## 📦 Instalación y Puesta en Marcha

Sigue estos sencillos pasos para ejecutar el proyecto localmente:

### 1. Clonar el repositorio
```bash
git clone <URL_DE_TU_REPOSITORIO>
cd biblioteca-laravel-api
```

### 2. Instalar dependencias
```bash
composer install
```

### 3. Configuración de Entorno
Copia el archivo de ejemplo `.env.example` a `.env`:
```bash
# En Windows (PowerShell / CMD)
copy .env.example .env

# En Linux / macOS
cp .env.example .env
```

Genera la clave única de la aplicación:
```bash
php artisan key:generate
```

### 4. Crear Base de Datos y Ejecutar Migraciones con Seeders
Ejecuta las migraciones y puebla la base de datos con registros iniciales de prueba (categorías y libros realistas):
```bash
php artisan migrate:fresh --seed
```

### 5. Iniciar Servidor Local
```bash
php artisan serve
```
El servidor quedará disponible en: **`http://127.0.0.1:8000`**

---

## 🧪 Ejecución de Pruebas Automatizadas (Tests)

El proyecto cuenta con **23 pruebas automatizadas** (115 aserciones) que validan exhaustivamente cada endpoint, validación y regla de negocio solicitada:

```bash
php artisan test
```

Para ver la salida descriptiva de cada caso de prueba:
```bash
./vendor/bin/phpunit --testdox
```

### Cobertura de Pruebas:
* ✔ Listado general de libros.
* ✔ Filtrado de libros por categoría (`?category={id}`).
* ✔ Creación de libros con datos válidos (`201 Created`).
* ✔ Validación de título con menos de 3 caracteres (`422 Unprocessable Content`).
* ✔ Validación de campos obligatorios faltantes (`422 Unprocessable Content`).
* ✔ Validación de categoría inexistente (`422 Unprocessable Content`).
* ✔ Detalle de libro (`200 OK`) y control de 404 en inexistentes.
* ✔ Actualización de libro (`200 OK`).
* ✔ Eliminación de libro existente (`200 OK`).
* ✔ Verificación de existencia al eliminar libro (`404 Not Found`).
* ✔ Listado y creación de categorías con nombre único (`201 Created`).
* ✔ Rechazo de categoría con nombre duplicado (`422 Unprocessable Content`).
* ✔ **Regla de negocio:** Bloqueo de eliminación de categoría si posee libros asociados (`409 Conflict`).
* ✔ Eliminación permitida de categoría vacía (`200 OK`).

---

## 📖 Colección de Endpoints

### 1. Libros (`/api/books` o `/books`)

| Método | Endpoint | Descripción | Códigos HTTP |
| :--- | :--- | :--- | :--- |
| **GET** | `/api/books` | Listar todos los libros con su categoría | `200 OK` |
| **GET** | `/api/books?category={id}` | Filtrar libros según el ID de categoría | `200 OK` |
| **POST** | `/api/books` | Registrar un nuevo libro | `201 Created`, `422 Unprocessable` |
| **GET** | `/api/books/{id}` | Obtener detalle de un libro específico | `200 OK`, `404 Not Found` |
| **PUT** | `/api/books/{id}` | Actualizar datos de un libro existente | `200 OK`, `404`, `422` |
| **DELETE** | `/api/books/{id}` | Eliminar un libro (verifica existencia) | `200 OK`, `404 Not Found` |

#### Ejemplo de Cuerpo de Petición (POST / PUT `/api/books`):
```json
{
  "title": "Cien años de soledad",
  "author": "Gabriel García Márquez",
  "description": "Obra cumbre de la literatura universal.",
  "published_year": 1967,
  "category_id": 1
}
```

---

### 2. Categorías (`/api/categories` o `/categories`)

| Método | Endpoint | Descripción | Códigos HTTP |
| :--- | :--- | :--- | :--- |
| **GET** | `/api/categories` | Listar todas las categorías con conteo de libros | `200 OK` |
| **POST** | `/api/categories` | Crear una nueva categoría (nombre único) | `201 Created`, `422 Unprocessable` |
| **GET** | `/api/categories/{id}` | Ver detalle de una categoría con sus libros | `200 OK`, `404 Not Found` |
| **PUT** | `/api/categories/{id}` | Actualizar nombre de una categoría | `200 OK`, `404`, `422` |
| **DELETE** | `/api/categories/{id}` | Eliminar categoría (**bloquea si tiene libros**) | `200 OK`, `404`, `409 Conflict` |

#### Ejemplo de Cuerpo de Petición (POST `/api/categories`):
```json
{
  "name": "Ciencia Ficción"
}
```

---

## 🏛️ Estructura del Proyecto

```text
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       ├── BookController.php       # Lógica CRUD de libros y filtrado
│   │       └── CategoryController.php   # Lógica CRUD e integridad de categorías
│   ├── Requests/
│   │   ├── StoreBookRequest.php         # Reglas de validación al crear libros
│   │   ├── UpdateBookRequest.php        # Reglas de validación al editar libros
│   │   ├── StoreCategoryRequest.php     # Validación y unicidad de categorías
│   │   └── UpdateCategoryRequest.php    # Validación al actualizar categorías
│   └── Resources/
│       ├── BookResource.php             # Transformación y serialización de libros
│       └── CategoryResource.php         # Transformación y serialización de categorías
├── Models/
│   ├── Book.php                         # Modelo Eloquent con relación belongsTo
│   └── Category.php                     # Modelo Eloquent con relación hasMany
database/
├── factories/                           # Factorías para generación de datos
├── migrations/                          # Migraciones con llaves foráneas e índices
└── seeders/                             # Seeders con datos iniciales reales
tests/
└── Feature/
    ├── BookApiTest.php                  # 12 pruebas de integración para Libros
    └── CategoryApiTest.php              # 9 pruebas de integración para Categorías
```

---

## 👤 Autor
* **John Di Donna** - [GitHub: @DiDonna29](https://github.com/DiDonna29)
