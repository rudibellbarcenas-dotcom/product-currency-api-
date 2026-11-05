# Product & Currency Management RESTful API

API RESTful profesional construida con **Laravel**, diseñada bajo principios de arquitectura limpia, bases de datos relacionales, prevención de problemas N+1, validaciones de integridad referencial y soporte multidivisa.

---

## 1. Requisitos del Sistema

- **PHP**: `^8.1 || ^8.2 || ^8.3` (Extensiones: `pdo`, `pdo_mysql`, `pdo_sqlite`, `mbstring`, `openssl`, `bcmath`, `curl`)
- **Composer**: `^2.2+`
- **Base de Datos**: MySQL 8.0+ (o SQLite para pruebas y desarrollo local)
- **Git**

---

## 2. Instalación y Puesta en Marcha

### Paso 1: Clonar o posicionarse en el proyecto
```bash
https://github.com/rudibellbarcenas-dotcom/product-currency-api-.git
```

### Paso 2: Instalar dependencias de Composer
```bash
composer install
```

### Paso 3: Configurar variables de entorno
Copiar el archivo de plantilla `.env.example` a `.env`:
```bash
cp .env.example .env
```

Generar la clave de cifrado de la aplicación:
```bash
php artisan key:generate
```

### Paso 4: Configurar la base de datos
Editar el archivo `.env` con las credenciales de su servidor MySQL:
```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=product_currency_db
DB_USERNAME=root
DB_PASSWORD=secret
```

*Nota: Asegúrese de haber creado la base de datos previamente en MySQL:*
```sql
CREATE DATABASE product_currency_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Paso 5: Ejecutar migraciones y seeders
```bash
php artisan migrate --seed
```
Esto creará las tablas `currencies`, `products`, `product_prices` y cargará divisas iniciales (USD, EUR, MXN, GBP, JPY) junto con productos de muestra.

### Paso 6: Iniciar el servidor local
```bash
php artisan serve
```
La API quedará disponible en: `http://localhost:8000/api`

---

## 3. Ejecución de Pruebas Automatizadas

La suite de pruebas automatizadas utiliza SQLite en memoria (`:memory:`), por lo que no altera los datos de su base de datos de desarrollo.

Para ejecutar todas las pruebas:
```bash
php artisan test
```
O usando PHPUnit directamente:
```bash
./vendor/bin/phpunit
```

Para ejecutar una suite o clase específica:
```bash
php artisan test --filter=ProductApiTest
php artisan test --filter=ProductPriceApiTest
php artisan test --filter=CurrencyApiTest
```

---

## 4. Endpoints y Catálogo de la API

Todos los endpoints retornan una respuesta en formato JSON con la siguiente estructura uniforme:
```json
{
  "success": true,
  "message": "Mensaje descriptivo de la operación",
  "data": { ... }
}
```

### Divisas (`/api/currencies`)

| Método | Endpoint | Descripción | Códigos HTTP |
|---|---|---|---|
| `GET` | `/api/currencies` | Lista todas las divisas disponibles | `200` |
| `POST` | `/api/currencies` | Registra una nueva divisa | `201`, `422` |
| `GET` | `/api/currencies/{id}` | Muestra el detalle de una divisa | `200`, `404` |
| `PUT` | `/api/currencies/{id}` | Actualiza el nombre o tasa de cambio de una divisa | `200`, `404`, `422` |

### Productos (`/api/products`)

| Método | Endpoint | Descripción | Códigos HTTP |
|---|---|---|---|
| `GET` | `/api/products` | Lista productos con paginación (`?page=1&per_page=15`) | `200` |
| `POST` | `/api/products` | Crea un producto en su divisa base | `201`, `422` |
| `GET` | `/api/products/{id}` | Obtiene un producto con su divisa base y precios | `200`, `404` |
| `PUT` | `/api/products/{id}` | Actualiza los datos de un producto | `200`, `404`, `422` |
| `DELETE` | `/api/products/{id}` | Elimina un producto y sus precios en cascada | `200`, `404` |

### Precios por Divisa (`/api/products/{id}/prices`)

| Método | Endpoint | Descripción | Códigos HTTP |
|---|---|---|---|
| `GET` | `/api/products/{id}/prices` | Lista los precios fijados para el producto | `200`, `404` |
| `POST` | `/api/products/{id}/prices` | Registra el precio para una divisa específica | `201`, `404`, `409`, `422` |

---

## 5. Documentación OpenAPI y Postman

- **Especificación OpenAPI 3.0**: Disponible en `docs/openapi.yaml`. Puede visualizarse en [Swagger Editor](https://editor.swagger.io/).
- **Colección de Postman**: Disponible en `docs/postman_collection.json`. Puede importarse directamente en Postman con variables preconfiguradas (`base_url`, `product_id`, `currency_id`).
