# Documentación Técnica: API RESTful de Productos y Precios Multidivisa

- **Tecnología Principal:** Laravel 11 / PHP 8.2+
- **Base de Datos:** MySQL / SQLite
- **Arquitectura:** RESTful con Eloquent ORM, Form Requests, API Resources y manejo de excepciones centralizado.
- **Formato de Comunicación:** JSON estandarizado con códigos de estado HTTP semánticos.

---

## 1. Opciones de Documentación Disponibles en el Repositorio

Para cumplir con los diferentes requerimientos de entrega, este proyecto incluye **4 formatos de documentación listos para usar**:

| Formato | Ubicación en el Proyecto | Cómo usarlo / visualizarlo |
|---|---|---|
| **Swagger / OpenAPI 3.0** | `docs/openapi.yaml` | Importar en [Swagger Editor](https://editor.swagger.io/) o Swagger UI |
| **JSON Postman** | `docs/postman_collection.json` | Importar en Postman (versión Collection v2.1) |
| **JSON Insomnia** | `docs/insomnia_collection.json` | Importar en Insomnia (menú *Import -> From File*) |
| **Documento Técnico Completo** | `docs/DOCUMENTACION_API.md` (este archivo) | Lectura directa en Markdown, GitHub o exportable a PDF |

---

## 2. Convención y Formato de Respuestas de la API

Todas las respuestas emitidas por la API siguen una envoltura (*envelope*) predecible:

### Respuesta Exitosa (`200 OK`, `201 Created`):
```json
{
  "success": true,
  "message": "Descripción de la operación exitosa.",
  "data": { ... }
}
```

### Respuesta de Error (`400`, `404`, `422`, `500`):
```json
{
  "success": false,
  "message": "Descripción del error.",
  "errors": {
    "campo_afectado": [
      "Mensaje de validación detallado."
    ]
  }
}
```

---

## 3. Especificación de Endpoints

### 3.1. Módulo de Divisas (`/api/currencies`)

#### 1. Listar Divisas
- **Método:** `GET`
- **URL:** `/api/currencies`
- **Cabeceras:** `Accept: application/json`
- **Respuesta (200 OK):**
```json
{
  "success": true,
  "message": "Listado de divisas obtenido correctamente.",
  "data": [
    {
      "id": 1,
      "name": "USD",
      "symbol": "$",
      "exchange_rate": 1.000000,
      "created_at": "2026-01-01T10:00:00.000000Z",
      "updated_at": "2026-01-01T10:00:00.000000Z"
    },
    {
      "id": 2,
      "name": "EUR",
      "symbol": "€",
      "exchange_rate": 0.920000,
      "created_at": "2026-01-01T10:00:00.000000Z",
      "updated_at": "2026-01-01T10:00:00.000000Z"
    }
  ]
}
```

#### 2. Registrar Nueva Divisa
- **Método:** `POST`
- **URL:** `/api/currencies`
- **Cuerpo (JSON):**
```json
{
  "name": "GBP",
  "symbol": "£",
  "exchange_rate": 0.79
}
```
- **Validaciones:**
  - `name`: obligatorio, texto máx. 50, único en la tabla `currencies`.
  - `symbol`: obligatorio, texto máx. 10.
  - `exchange_rate`: obligatorio, numérico mayor a 0.
- **Respuesta (201 Created):** Retorna el objeto de la divisa creada.

#### 3. Consultar Divisa por ID
- **Método:** `GET`
- **URL:** `/api/currencies/{id}`
- **Respuesta:** `200 OK` si existe, o `404 Not Found` si no existe.

#### 4. Actualizar Divisa
- **Método:** `PUT`
- **URL:** `/api/currencies/{id}`
- **Cuerpo (JSON):**
```json
{
  "exchange_rate": 0.81
}
```
- **Respuesta (200 OK):** Retorna la divisa con la tasa de cambio actualizada.

---

### 3.2. Módulo de Productos (`/api/products`)

#### 1. Listar Productos con Paginación
- **Método:** `GET`
- **URL:** `/api/products?page=1&per_page=15`
- **Respuesta (200 OK):**
```json
{
  "success": true,
  "message": "Listado de productos recuperado exitosamente.",
  "data": {
    "items": [
      {
        "id": 1,
        "name": "Laptop Gamer Pro 16",
        "description": "Portátil de alto rendimiento con GPU RTX 4070",
        "price": 1500.00,
        "currency_id": 1,
        "base_currency": {
          "id": 1,
          "name": "USD",
          "symbol": "$",
          "exchange_rate": 1.000000
        },
        "tax_cost": 150.00,
        "manufacturing_cost": 950.00,
        "total_cost": 1100.00,
        "gross_margin": 400.00,
        "prices": [
          {
            "id": 1,
            "product_id": 1,
            "currency_id": 2,
            "currency": {
              "id": 2,
              "name": "EUR",
              "symbol": "€"
            },
            "price": 1380.00
          }
        ]
      }
    ],
    "pagination": {
      "total": 1,
      "per_page": 15,
      "current_page": 1,
      "last_page": 1,
      "has_more_pages": false
    }
  }
}
```

#### 2. Crear un Producto
- **Método:** `POST`
- **URL:** `/api/products`
- **Cuerpo (JSON):**
```json
{
  "name": "Teclado Mecanico RGB",
  "description": "Switches Red silenciosos",
  "price": 120.00,
  "currency_id": 1,
  "tax_cost": 12.00,
  "manufacturing_cost": 60.00
}
```
- **Validaciones:**
  - `name`: obligatorio, texto hasta 255 caracteres.
  - `price`: obligatorio, numérico no negativo ($\ge 0$).
  - `currency_id`: obligatorio, entero, debe existir en la tabla `currencies`.
  - `tax_cost`: numérico no negativo ($\ge 0$).
  - `manufacturing_cost`: numérico no negativo ($\ge 0$).
- **Respuesta (201 Created):** Retorna el producto creado con métricas computadas de margen bruto y costo total.

#### 3. Consultar Detalle de Producto
- **Método:** `GET`
- **URL:** `/api/products/{id}`
- **Respuesta:** `200 OK` si existe, o `404 Not Found`.

#### 4. Actualizar Producto
- **Método:** `PUT`
- **URL:** `/api/products/{id}`
- **Cuerpo (JSON):**
```json
{
  "price": 129.99,
  "description": "Edición especial con reposamuñecas acolchado"
}
```
- **Respuesta (200 OK):** Retorna el producto con los datos modificados.

#### 5. Eliminar Producto
- **Método:** `DELETE`
- **URL:** `/api/products/{id}`
- **Comportamiento en Base de Datos:** Eliminación en cascada garantizada mediante clave foránea con `cascadeOnDelete()`. Al eliminar el producto, todos sus precios en divisas secundarias se eliminan de forma atómica y consistente.
- **Respuesta (200 OK):**
```json
{
  "success": true,
  "message": "Producto y sus precios asociados eliminados correctamente.",
  "data": null
}
```

---

### 3.3. Módulo de Precios por Divisa (`/api/products/{id}/prices`)

#### 1. Listar Precios de un Producto en Distintas Divisas
- **Método:** `GET`
- **URL:** `/api/products/{id}/prices`
- **Respuesta (200 OK):** Lista de precios registrados para ese producto.

#### 2. Registrar Precio en una Divisa Específica
- **Método:** `POST`
- **URL:** `/api/products/{id}/prices`
- **Cuerpo (JSON):**
```json
{
  "currency_id": 2,
  "price": 110.40
}
```
- **Regla de Negocio Crítica (Prevención de Duplicados):**
  Un producto **no puede tener dos registros de precio para la misma divisa**.
  Si se intenta registrar un precio para una divisa ya existente en ese producto, la API responde con **`422 Unprocessable Entity`**:
```json
{
  "success": false,
  "message": "Errores de validación en la solicitud.",
  "errors": {
    "currency_id": [
      "Este producto ya cuenta con un precio registrado para la divisa indicada."
    ]
  }
}
```

---

## 4. Reglas de Negocio y Lógica Financiera

1. **Interpretación de la Tasa de Cambio (`exchange_rate`):**
   - La divisa pivote base tiene una tasa de `1.000000` (USD).
   - Las demás tasas indican cuántas unidades de moneda extranjera corresponden a 1 unidad base (ej. 1 USD = 0.92 EUR).
   - Conversión dinámica:
     $$\text{Precio Convertido} = \text{Precio Base} \times \left(\frac{\text{Tasa Destino}}{\text{Tasa Origen}}\right)$$

2. **Inmutabilidad de Precios Comerciales Registrados:**
   - Los precios almacenados en la tabla `product_prices` representan acuerdos o fijaciones comerciales estables para mercados internacionales. No se sobreescriben automáticamente al variar una tasa de cambio general.

3. **Cálculo de Rentabilidad y Costos:**
   - $\text{Costo Total} = \text{Costo de Fabricación} + \text{Costo de Impuestos}$
   - $\text{Margen Bruto} = \text{Precio de Venta} - \text{Costo Total}$

---

## 5. Instrucciones para Ejecutar las Pruebas

### Pruebas Automatizadas (PHPUnit):
```bash
php artisan test
```

### Pruebas con Postman CLI:
```bash
postman collection run docs/postman_collection.json
```

### Pruebas Rápidas con Script por Lotes:
```cmd
.\test_api.bat
```
