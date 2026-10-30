@echo off
chcp 65001 > nul
echo ========================================================
echo   EJECUTANDO PRUEBAS DE LA API CON CURL
echo ========================================================
echo.

echo [TEST 1] Listar Divisas (GET /api/currencies)
curl.exe -s -X GET http://localhost:8000/api/currencies -H "Accept: application/json"
echo.
echo.

echo [TEST 2] Crear Nuevo Producto en USD (POST /api/products)
curl.exe -s -X POST http://localhost:8000/api/products -H "Content-Type: application/json" -H "Accept: application/json" -d "{\"name\": \"Teclado Mecanico RGB\", \"description\": \"Switches Red silenciosos\", \"price\": 120.00, \"currency_id\": 1, \"tax_cost\": 12.00, \"manufacturing_cost\": 60.00}"
echo.
echo.

echo [TEST 3] Consultar Producto ID 1 con sus relaciones (GET /api/products/4)
curl.exe -s -X GET http://localhost:8000/api/products/4 -H "Accept: application/json"
echo.
echo.

echo [TEST 4] Registrar Precio en EUR para Producto 1 (POST /api/products/4/prices)
curl.exe -s -X POST http://localhost:8000/api/products/4/prices -H "Content-Type: application/json" -H "Accept: application/json" -d "{\"currency_id\": 2, \"price\": 110.40}"
echo.
echo.

echo [TEST 5] Consultar Precios del Producto 1 (GET /api/products/4/prices)
curl.exe -s -X GET http://localhost:8000/api/products/4/prices -H "Accept: application/json"
echo.
echo.

echo [TEST 6] Regla de Negocio: Rechazo de Precio Duplicado (POST /api/products/4/prices)
curl.exe -s -X POST http://localhost:8000/api/products/4/prices -H "Content-Type: application/json" -H "Accept: application/json" -d "{\"currency_id\": 2, \"price\": 115.00}"
echo.
echo.

echo [TEST 7] Regla de Negocio: Rechazo de Precio Negativo (POST /api/products)
curl.exe -s -X POST http://localhost:8000/api/products -H "Content-Type: application/json" -H "Accept: application/json" -d "{\"name\": \"Producto Invalido\", \"price\": -25.00, \"currency_id\": 1}"
echo.
echo.

echo ========================================================
echo   PRUEBAS FINALIZADAS EXITOSAMENTE
echo ========================================================
pause
